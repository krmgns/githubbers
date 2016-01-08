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

function gc_github_commits($path, $page = 1, $pageLimit = 1) {
    $query = [];
    if ($page) $query['page'] = $page;
    if ($pageLimit) $query['per_page'] = $pageLimit;

    return gc_curl_get("/repos/{$path}/commits", $query);
}
