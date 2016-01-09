<?php
// hey!
chdir(__dir__);

$autoload = require('./src/couch-php/Couch/Autoload.php');
$autoload->register();

$autoload = require('./src/graphcommons-php/GraphCommons/Autoload.php');
$autoload->register();

// api cred.
define('APIKEY', file_get_contents('./.apikey'));

require('./src/gc_inc.php');
require('./src/gc_github.php');
require('./src/gc_db.php');
require('./src/gc.php');

use GraphCommons\GraphCommons;
use GraphCommons\Graph\Graph;
use GraphCommons\Graph\Signal;
use GraphCommons\Graph\SignalCollection;

if (PHP_SAPI != 'cli') {
    die("Available on via CLI!\n");
}

$opt = getopt('r:p:');
if (!isset($opt['r'])) {
    die("Usage: -r <org|user/repo> \n");
}
// pre($opt,1);

// get repo id
$repoId = $opt['r'];

// set page
$page = 1;
if (isset($opt['p'])) {
    $page = $opt['p'];
}

while (true) {
    // $commit = gc_github_repo_commits($repoId, $page)[0] ?? null;
    // if (empty($commit)) {
    //     die("No more commits!\n");
    // }
    // pre($commit);

    $repoData = gc_db_find_repo($repoId);
    if (empty($repoData)) {
        $repo = gc_github_repo($repoId);
        if (isset($repo['id'])) {
            $repoData['name'] = $repoId;
            $repoData['link'] = $repo['html_url'];
            $repoData['desc'] = $repo['description'];
            $repoData['avatar'] = $repo['owner']['avatar_url'];
        }
        // pre($repoData,1);
        // $repoData = gc_db_save_repo($repoData);
    }
    pre($repoData);

    // $commitData = gc_db_find_commit('4eb51782d8d28f641be99f774f1f297e5e7be6f4');
    // pre($commitData);

    print "Sleeping for 5 seconds..\n";
    // sleep(5);

    break;
}
