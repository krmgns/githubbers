<?php defined('ROOT') or die('No root!');

$dbCouch  = new Couch\Couch();
$dbClient = new Couch\Client($dbCouch);
$db       = new Couch\Database($dbClient, 'githubbers');

function gc_db_find_doc($id) {
    global $db;
    $doc = (new Couch\Document($db, ['_id' => $id]))->find();
    // pre($db->client->request->toString());
    return $doc;
}

function gc_db_find_repo($id) {
    return gc_db_find_doc($id);
}

function gc_db_find_commit($id) {
    return gc_db_find_doc($id);
}

function gc_db_find_user($id) {
    return gc_db_find_doc($id);
}

function gc_db_save_repo(array $data) {
    global $db;
    $data['type'] = 'repo';
    $db->createDocument($data);
    if ($db->client->response->getStatusCode() == 201) {
        return $data;
    }
    return null;
}

function gc_db_save_commit(array $data) {
    global $db;
    $data['type'] = 'commit';
    $db->createDocument($data);
    if ($db->client->response->getStatusCode() == 201) {
        return $data;
    }
    return null;
}

function gc_db_save_user(array $data) {
    global $db;
    $data['type'] = 'user';
    $db->createDocument($data);
    if ($db->client->response->getStatusCode() == 201) {
        return $data;
    }
    return null;
}
