<?php defined('ROOT') or die('No root!');

use GraphCommons\GraphCommons;
use GraphCommons\Graph\Graph;
use GraphCommons\Graph\Signal;
use GraphCommons\Graph\SignalCollection;

$gc = new GraphCommons(API_KEY_GC);

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

function gc_add_repo_node(array $data) {
    global $gc;
    try {
        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray(array(
            array(
                'action'          => Signal::NODE_CREATE,
                'parameters'      => array(
                    'name'        => $data['name'],
                    'type'        => 'Repository',
                    'description' => $data['desc'],
                    'image'       => $data['avatar'],
                    'reference'   => $data['link'],
                ),
            ),
            array(
                'action'          => Signal::EDGE_CREATE,
                'parameters'      => array(
                    'from_name'   => $data['name'],
                    'from_type'   => 'Repository',
                    'to_name'     => 'Github',
                    'to_type'     => 'Github',
                    'name'        => 'REPOSITORY',
                ),
            ),
        )));
        return true; // success
    } catch (Exception $e) {}
    return false; // failure
}

function gc_add_user_node(array $data, array $dataTo) {
    global $gc;
    try {
        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray(array(
            array(
                'action'          => Signal::NODE_CREATE,
                'parameters'      => array(
                    'name'        => '@'. $data['_id'],
                    'type'        => 'Githubber',
                    'description' => $data['name'] .' (@'. $data['username'] .')',
                    'image'       => $data['avatar'],
                    'reference'   => $data['link'],
                ),
            ),
            array(
                'action'          => Signal::EDGE_CREATE,
                'parameters'      => array(
                    'from_name'   => '@'. $data['_id'],
                    'from_type'   => 'Githubber',
                    'to_name'     => $dataTo['_id'],
                    'to_type'     => 'Repository',
                    'name'        => 'CONTRIBUTION',
                ),
            ),
        )));
        return true; // success
    } catch (Exception $e) {}
    return false; // failure
}

function gc_add_commit_node(array $data, array $dataTo) {
    global $gc;
    try {
        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray(array(
            array(
                'action'          => Signal::NODE_CREATE,
                'parameters'      => array(
                    'name'        => '#'. $data['_id'],
                    'type'        => 'Commit',
                    'description' => $data['message'],
                    'image'       => 'https://cdn0.iconfinder.com/data/icons/octicons/1024/git-commit-128.png',
                    'reference'   => $data['link'],
                ),
            ),
            array(
                'action'          => Signal::EDGE_CREATE,
                'parameters'      => array(
                    'from_name'   => '#'. $data['_id'],
                    'from_type'   => 'Commit',
                    'to_name'     => '@'. $dataTo['_id'],
                    'to_type'     => 'Githubber',
                    'name'        => 'COMMIT',
                ),
            ),
            // bunu bir de repo'ya bagla
            // array(
            //     'action'          => Signal::EDGE_CREATE,
            //     'parameters'      => array(
            //         'from_name'   => '@'. $data['_id'],
            //         'from_type'   => 'Githubber',
            //         'to_name'     => repo _id,
            //         'to_type'     => 'Repository',
            //         'name'        => 'CONTRIBUTION',
            //     ),
            // ),
        )));
        return true; // success
    } catch (Exception $e) {}
    return false; // failure
}
