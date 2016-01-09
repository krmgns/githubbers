<?php
// hey!
chdir(__dir__);

$autoload = require('./src/couch-php/Couch/Autoload.php');
$autoload->register();

$autoload = require('./src/graphcommons-php7/GraphCommons/Autoload.php');
$autoload->register();

$apiKeys = array_map('trim', file('./.apikeys', FILE_SKIP_EMPTY_LINES));

// root
define('ROOT', __dir__);

// api creds.
define('API_KEY_GC', $apiKeys[0]);
define('API_KEY_GH', $apiKeys[1]);

define('GRAPH_ID', '6f7429a5-1acb-4c6d-a940-fd56511da361'); // Githubbers - Test

require('./src/gc_inc.php');
require('./src/gc_github.php');
require('./src/gc_db.php');
require('./src/gc.php');

if (PHP_SAPI != 'cli') {
    die("Available on via CLI!\n");
}

$opt = getopt('r:p:s:');
if (!isset($opt['r'])) {
    die("Usage: -r <org|user/repo> \n");
}
// pre($opt,1);

// get repo id
$repoId = $opt['r'];

// set page
$page = 0;
if (isset($opt['p'])) {
    $page = (int) $opt['p'];
}

// set sleep
$sleep = 10;
if (isset($opt['s'])) {
    $page = (int) $opt['s'];
}

while (true) {
    $page++;

    print ">> Process for page #{$page}.\n";

    print ">> Getting only '1' commit for '{$repoId}'.\n";
    $commit = gc_github_repo_commits($repoId, $page)[0] ?? null;
    if (empty($commit)) {
        die("No more commits!\n");
    }
    // pre($commit);

    print ">> Checking/saving repo: {$repoId}.\n";
    $repoData = gc_db_find_repo($repoId);
    if (!isset($repoData['_id'])) {
        $repo = gc_github_repo($repoId);
        if (isset($repo['id'])) {
            $repoData = [];
            $repoData['_id']    = $repoId;
            $repoData['name']   = $repoId;
            $repoData['link']   = $repo['html_url'];
            $repoData['desc']   = $repo['description'];
            $repoData['avatar'] = $repo['owner']['avatar_url'];
        }
        $repoData = gc_db_save_repo($repoData);
        if (!empty($repoData)) {
            print ">> Creating repo node: {$repoId}.\n";
            $result = gc_add_repo_node($repoData);
            if ($result == false) {
                die("Cannot create node for {$repoId}!\n");
            }
        }
    }
    // pre($repoData,1);

    $userId = $commit['author']['login'];
    if (empty($userId)) {
        print ">> No user ID, skipping...\n";
        print ">> -----------------------\n";
        continue;
    }
    print ">> Checking/saving user: {$userId}.\n";

    $userData = gc_db_find_user($userId);
    if (!isset($userData['_id'])) {
        $userData = [];
        $userData['_id']      = $userId;
        $userData['name']     = $commit['commit']['author']['name'];
        $userData['link']     = $commit['author']['html_url'];
        $userData['avatar']   = $commit['author']['avatar_url'];
        $userData['username'] = $commit['author']['login'];
        $userData = gc_db_save_user($userData);
        if (!empty($userData)) {
            print ">> Creating user node: {$userId}.\n";
            $result = gc_add_user_node($userData, $repoData);
            if ($result == false) {
                die("Cannot create node for {$userId}!\n");
            }
        }
    }
    // pre($userData,1);

    $commitId = $commit['sha'];
    if (empty($userId)) {
        print ">> No commit ID, skipping...\n";
        print ">> -------------------------\n";
        continue;
    }
    print ">> Checking/saving commit '{$commitId}'.\n";

    $commitData = gc_db_find_commit($commitId);
    if (!isset($commitData['_id'])) {
        $commitData = [];
        $commitData['_id']              = $commitId;
        $commitData['repo']             = $repoId;
        $commitData['link']             = $commit['html_url'];
        $commitData['message']          = $commit['commit']['message'];
        $commitData['user']['name']     = $commit['commit']['author']['name'];
        $commitData['user']['link']     = $commit['author']['html_url'];
        $commitData['user']['avatar']   = $commit['author']['avatar_url'];
        $commitData['user']['username'] = $commit['author']['login'];
        $commitData = gc_db_save_commit($commitData);
        if (!empty($commitData)) {
            print ">> Creating commit node: {$commitId}.\n";
            $result = gc_add_commit_node($commitData, $userData);
            if ($result == false) {
                die("Cannot create node for {$commitId}!\n");
            }
        }
    }
    // pre($commitData,1);

    print ">> Sleeping for {$sleep} seconds.\n"; sleep($sleep);
    print ">> ------------------------------\n";

    // break;
}
