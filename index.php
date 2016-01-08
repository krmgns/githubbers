<?php
header('content-type: text/plain; charset=utf-8');

// hey!
chdir(__dir__);

$autoload = require('./src/couch-php/Couch/Autoload.php');
$autoload->register();

$autoload = require('./src/graphcommons-php/GraphCommons/Autoload.php');
$autoload->register();

// api cred.
define('APIKEY', file_get_contents('./.apikey'));

require('./src/gc_inc.php');
require('./src/gc_curl.php');
require('./src/gc_db.php');
require('./src/gc.php');

use GraphCommons\GraphCommons;
use GraphCommons\Graph\Graph;
use GraphCommons\Graph\Signal;
use GraphCommons\Graph\SignalCollection;

// prd(gc_ping());

// @test (froq)
$uid = '80a43f69-1882-254e-6242-4e9f5d5ace4d';
$nid = '8dcbb598-be52-6531-156a-4b637c6ea001';

$commits = gc_github_commits('froq/froq');
pre($commits);

$repoData = gc_db_find_repo('froq/froq');
if (empty($repo)) {
    $repoData = gc_db_save_repo([
        '_id'     => 'froq/froq',
        'name'    => 'Froq',
        'desc'    => 'Just a web service framework..',
        'commits' => [],
    ]);
}
pre($repoData);

$commitData = gc_db_find_commit('4eb51782d8d28f641be99f774f1f297e5e7be6f4');
pre($commitData);
