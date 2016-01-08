<?php
function gc_curl_get($uri, $urip = null) {
    $url = 'https://'. preg_replace('~/+~', '/', "api.github.com/{$uri}");
    if (!empty($urip)) {
        if (is_array($urip)) {
            $urip = http_build_query($urip);
        }
        $url .= '?'. $urip;
    }

    // pre("\n$url\n");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT,
        'Githubbers Agent v1.0.0-beta (+https://github.com/qeremy/githubbers)');

    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}
