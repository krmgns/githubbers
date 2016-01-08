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

// prd(gc_ping_node($uid));
// sleep(1);
// prd(gc_ping_node($nid));

// $data = gc_db_get_repo('froq');
// pre($data);
// $data['commits'][] = '123';
// prd(gc_db_put_repo('froq', $data));

// $data = gc_db_get_user('qeremy');
// pre($data);
// $data['repos'][] = 'foo';
// prd(gc_db_put_user('qeremy', $data));

// GET /repos/:user/:repo/commits

$user = 'qeremy';
$repo = 'froq/froq';
$commits = gc_github_commits($repo);
pre($commits);
