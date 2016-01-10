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

// Githubbers!
define('GRAPH_ID', '0df3be5d-dcfd-4040-b002-0a504d36d176');

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

// get repo id
$repoId = $opt['r'];

// set page
$page = 0;
$pageMax = 50;
if (isset($opt['p'])) {
    $page = (int) $opt['p'];
}

// set sleep
$sleep = 5;
if (isset($opt['s'])) {
    $sleep = (int) $opt['s'];
}

static $commitCount = [];

while (true) {
    $page++;
    if ($page > $pageMax) {
        print "Page number is {$pageMax}, exiting...\n";
        exit();
    }

    print ">> Process for page #{$page}.\n";

    print ">> Getting only '1' commit for '{$repoId}'.\n";
    $commit = gc_github_repo_commits($repoId, $page)[0] ?? null;
    if (empty($commit)) {
        die("No more commits!\n");
    }
    // pre($commit,1);

    print ">> Adding repo '{$repoId}'.\n";

    $repo = gc_github_repo($repoId);
    if (!isset($repo['id'])) {
        print ">> No repo found, skipping...\n";
        print ">> -------------------------\n";
        continue;
    }

    $repoData = [];
    $repoData['_id']    = $repoId;
    $repoData['name']   = $repoId;
    $repoData['link']   = $repo['html_url'];
    $repoData['desc']   = $repo['description'];
    $repoData['avatar'] = $repo['owner']['avatar_url'];
    $repoData['langs']  = $repo['langs'];
    // // $result = gc_add_repo_node($repoData);
    $result = gc_add_repo_node2($repoData);
    if ($result == false) {
        die("Cannot create node for {$repoId}!\n");
    }

    $userId = $commit['author']['login'];
    $commitId = $commit['sha'];
    if (empty($userId) || empty($commitId)) {
        print ">> No user/commit ID, skipping...\n";
        print ">> -------------------------\n";
        continue;
    }

    // user commit count
    if (!isset($commitCount[$userId])) {
        $commitCount[$userId] = 1;
    } else {
        $commitCount[$userId]++;
    }
    $weight = $commitCount[$userId];

    print ">> Adding commit '{$commitId}'.\n";

    $userData = [];
    $userData['_id']      = $userId;
    $userData['name']     = $commit['commit']['author']['name'];
    $userData['link']     = $commit['author']['html_url'];
    $userData['avatar']   = $commit['author']['avatar_url'];
    $userData['username'] = $commit['author']['login'];

    // $commitData = [];
    // $commitData['_id']              = $commitId;
    // $commitData['name']             = $commitId;
    // $commitData['repo']             = $repoId;
    // $commitData['link']             = $commit['html_url'];
    // $commitData['message']          = $commit['commit']['message'];

    // // $result = gc_add_commit_node($repoId, $userData, $commitData);
    $result = gc_add_commit_node2($repoId, $userData, $weight);
    if ($result == false) {
        die("Cannot create node for {$repoId}!\n");
    }

    print ">> Sleeping for {$sleep} seconds...\n";
    print ">> ------------------------------\n";
    sleep($sleep);

    // break;
}
