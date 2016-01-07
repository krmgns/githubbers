<?php
header('content-type: text/plain; charset=utf-8');

// hey!
chdir(__dir__);

// api cred.
define('APIKEY', file_get_contents('./.apikey'));

require('./src/gc_inc.php');
require('./src/gc.php');

use GraphCommons\GraphCommons;
use GraphCommons\Graph\Graph;
use GraphCommons\Graph\Signal;
use GraphCommons\Graph\SignalCollection;

prd(gc_ping());
