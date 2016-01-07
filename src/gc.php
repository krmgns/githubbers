<?php defined('APIKEY') or die('...');

$autoload = require('./src/graphcommons-php/GraphCommons/Autoload.php');
$autoload->register();

use GraphCommons\GraphCommons;

function gc() {
    if (!isset($GLOBALS['gc'])) {
        $GLOBALS['gc'] = new GraphCommons(APIKEY);
    }
    return $GLOBALS['gc'];
}

function gc_ping() {
    try {
        $gc = gc();
        $gc->api->status();
        return $gc->client->response->ok();
    } catch (\Exception $e) {
        return false;
    }
}
