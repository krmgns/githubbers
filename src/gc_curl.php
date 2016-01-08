<?php
function gc_curl_get($uri) {
    $url = 'https://'. preg_replace('~/+~', '/', "api.github.com/{$uri}");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT,
        'Githubbers Agent v1.0.0-beta (+https://github.com/qeremy/githubbers)');
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
