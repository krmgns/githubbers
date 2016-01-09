<?php defined('APIKEY') or die('...');

require('./src/fsurl/FSUrl/FSUrlException.php');
require('./src/fsurl/FSUrl/FSUrl.php');

function gc_github_url($uri, $urip = null) {
    $uri = preg_replace('~(^|[^:])//+~', '\\1/', trim($uri, '/'));
    if (!empty($urip)) {
        if (is_array($urip)) {
            $urip = http_build_query($urip);
        }
        $uri .= '?'. $urip;
    }
    return 'https://api.github.com/'. $uri;
}

function gc_github_get($uri, $urip = null) {
    $url = gc_github_url($uri, $urip);
    $fsu = new FSUrl($url);
    $fsu->setRequestHeader('User-Agent', 'Githubbers v1.0.0-beta (+https://github.com/qeremy/githubbers)');
    $fsu->run();

    // @debug
    // pre($fsu);
    // pre($fsu->getRequest());
    // pre($fsu->getResponse());

    return json_decode($fsu->getResponseBody(), true);
}

function gc_github_repo($path) {
    return gc_github_get("/repos/{$path}");
}

function gc_github_repo_commits($path, $page = 1, $pageLimit = 1) {
    $query = [];
    if ($page) $query['page'] = $page;
    if ($pageLimit) $query['per_page'] = $pageLimit;

    return gc_github_get("/repos/{$path}/commits", $query);
}
