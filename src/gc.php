<?php defined('APIKEY') or die('...');

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
    } catch (Exception $e) {}

    return false;
}

function gc_ping_node($id) {
    try {
        $gc = gc();
        $gc->api->getNode($id);
        return $gc->client->response->ok();
    } catch (Exception $e) {}

    return false;
}
