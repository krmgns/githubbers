<?php
$dbCouch  = new Couch\Couch();
$dbClient = new Couch\Client($dbCouch);
$db       = new Couch\Database($dbClient, 'githubbers');

function gc_db_find_doc($id) {
    global $db;
    $d= (new Couch\Document($db, ['_id' => $id]))->find();
    pre($db->client->request->toString());
    return $d;
}

function gc_db_find_repo($id) {
    return gc_db_find_doc($id);
}

function gc_db_find_commit($id) {
    return gc_db_find_doc($id);
}

function gc_db_save_repo(array $data) {
    global $db;
    $data['type'] = 'repo';
    $db->createDocument($data);
    if (201 != $db->client->response->getStatusCode()) {
        return null;
    }
    return $data;
}

function gc_db_save_commit(array $data) {
    global $db;
    $data['type'] = 'commit';
    $db->createDocument($data);
    if (201 != $db->client->response->getStatusCode()) {
        return null;
    }
    return $data;
}
