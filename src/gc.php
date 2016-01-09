<?php defined('APIKEY') or die('...');

$gc = new GraphCommons\GraphCommons(APIKEY);

function gc_ping() {
    global $gc;
    try {
        $gc->api->status();
        return $gc->client->response->ok();
    } catch (Exception $e) {}

    return false;
}

function gc_ping_node($id) {
    global $gc;
    try {
        $gc->api->getNode($id);
        return $gc->client->response->ok();
    } catch (Exception $e) {}

    return false;
}
