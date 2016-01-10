<?php defined('ROOT') or die('No root!');

use GraphCommons\GraphCommons;
use GraphCommons\Graph\Graph;
use GraphCommons\Graph\Signal;
use GraphCommons\Graph\SignalCollection;

$gc = new GraphCommons(API_KEY_GC, ['debug' => !true]);

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

function gc_add_repo_node(array $repoData) {
    global $gc; $return = true;
    try {
        $repoId = $repoData['_id'];
        $array = []; $i = 0;
        $array[$i]['action']     = Signal::NODE_CREATE;
        $array[$i]['parameters'] = [
            'name'           => $repoId,
            'type'           => 'Repository',
            'description'    => $repoData['desc'],
            'image'          => $repoData['avatar'],
            'reference'      => $repoData['link'],
        ];
        $i++;
        $array[$i]['action']     = Signal::EDGE_CREATE;
        $array[$i]['parameters'] = [
            'from_name'      => $repoId,
            'from_type'      => 'Repository',
            'to_name'        => 'Github',
            'to_type'        => 'Github',
            'name'           => 'REPOSITORY',
        ];
        if (!empty($repoData['langs'])) foreach ($repoData['langs'] as $langName => $langCodes) {
            $i++;
            $array[$i]['action']     = Signal::NODE_CREATE;
            $array[$i]['parameters'] = [
                'name'           => $langName,
                'type'           => 'Language',
                'description'    => sprintf('%s lines.', $langCodes),
            ];
            $i++;
            $array[$i]['action']     = Signal::EDGE_CREATE;
            $array[$i]['parameters'] = [
                'from_name'      => $langName,
                'from_type'      => 'Language',
                'to_name'        => $repoId,
                'to_type'        => 'Repository',
                'name'           => 'LANGUAGE',
            ];
        }
        // pre($array,1);

        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray($array));
    } catch (Exception $e) {
        $return = false;
    }

    return $return;
}

function gc_add_commit_node($repoId, array $userData, array $commitData) {
    global $gc; $return = true;
    try {
        $userId   = sprintf('@%s', $userData['_id']);
        $commitId = sprintf('#%s', $commitData['_id']);
        $array = []; $i = 0;
        // commit
        $array[$i]['action']      = Signal::NODE_CREATE;
        $array[$i]['parameters']  = [
            'name'        => $commitId,
            'type'        => 'Commit',
            'description' => $commitData['message'],
            'image'       => 'https://cdn0.iconfinder.com/data/icons/octicons/1024/git-commit-128.png',
            'reference'   => $commitData['link'],
        ];
        $i++;
        // commit edge
        $array[$i]['action']      = Signal::EDGE_CREATE;
        $array[$i]['parameters']  = [
            'from_name'   => $commitId,
            'from_type'   => 'Commit',
            'to_name'     => $repoId,
            'to_type'     => 'Repository',
            'name'        => 'COMMIT',
        ];
        $i++;
        // user
        if (isset($userData['name'])) {
            $array[$i]['action']      = Signal::NODE_CREATE;
            $array[$i]['parameters']  = [
                'name'        => $userId,
                'type'        => 'Githubber',
                'description' => sprintf('%s (%s)', $userData['name'], $userId),
                'image'       => $userData['avatar'],
                'reference'   => $userData['link'],
            ];
        }
        $i++;
        // user edge
        $array[$i]['action']            = Signal::EDGE_CREATE;
        $array[$i]['parameters']        = [
            'from_name'   => $userId,
            'from_type'   => 'Githubber',
            'to_name'     => $commitId,
            'to_type'     => 'Commit',
            'name'        => 'CONTRIBUTION',
        ];
        // pre($array,1);

        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray($array));
    } catch (Exception $e) {
        $return = false;
    }

    return $return;
}

function gc_add_repo_node2(array $repoData) {
    global $gc; $return = true;
    try {
        $repoId = $repoData['_id'];
        $array = []; $i = 0;
        $array[$i]['action']     = Signal::NODE_CREATE;
        $array[$i]['parameters'] = [
            'name'           => $repoId,
            'type'           => 'Repository',
            'description'    => $repoData['desc'],
            'image'          => $repoData['avatar'],
            'reference'      => $repoData['link'],
        ];
        if (!empty($repoData['langs'])) {
            $langCodesSum = array_sum($repoData['langs']);
            foreach ($repoData['langs'] as $langName => $langCodes) {
                $i++;
                $array[$i]['action']     = Signal::NODE_CREATE;
                $array[$i]['parameters'] = [
                    'name'           => $langName,
                    'type'           => 'Language',
                ];
                $i++;
                $array[$i]['action']     = Signal::EDGE_CREATE;
                $array[$i]['parameters'] = [
                    'from_name'      => $langName,
                    'from_type'      => 'Language',
                    'to_name'        => $repoId,
                    'to_type'        => 'Repository',
                    'name'           => 'LANGUAGE',
                    'weight'         => sprintf('%.2f', $langCodes / $langCodesSum * 100),
                ];
            }
        }
        // pre($array,1);

        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray($array));
    } catch (Exception $e) {
        $return = false;
    }

    return $return;
}

function gc_add_commit_node2($repoId, array $userData, $weight) {
    global $gc; $return = true;
    try {
        $userId = sprintf('@%s', $userData['_id']);
        $array = []; $i = 0;
        // user
        if (isset($userData['name'])) {
            $array[$i]['action']      = Signal::NODE_CREATE;
            $array[$i]['parameters']  = [
                'name'        => $userId,
                'type'        => 'Githubber',
                'description' => sprintf('%s (%s)', $userData['name'], $userId),
                'image'       => $userData['avatar'],
                'reference'   => $userData['link'],
            ];
        }
        $i++;
        // user edge
        $array[$i]['action']     = Signal::EDGE_CREATE;
        $array[$i]['parameters'] = [
            'from_name'   => $userId,
            'from_type'   => 'Githubber',
            'to_name'     => $repoId,
            'to_type'     => 'Repository',
            'name'        => 'COMMIT',
            'weight'      => $weight,
        ];
        // pre($array,1);

        $gc->api->addGraphSignal(GRAPH_ID, SignalCollection::fromArray($array));
    } catch (Exception $e) {
        $return = false;
    }

    return $return;
}
