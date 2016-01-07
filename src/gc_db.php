<?php
function gc_db_file($type, $name) {
    $file = sprintf('./db/%s/%s.json', $type, $name);
    if (!is_file($file)) {
        touch($file);
    }
    return $file;
}

function gc_db_get_repo($name) {
    return json_decode(file_get_contents(gc_db_file('repo', $name)), true);
}

function gc_db_put_repo($name, array $data) {
    return (bool) file_put_contents(gc_db_file('repo', $name), json_encode($data));
}

function gc_db_get_user($name) {
    return json_decode(file_get_contents(gc_db_file('user', $name)), true);
}

function gc_db_put_user($name, array $data) {
    return (bool) file_put_contents(gc_db_file('user', $name), json_encode($data));
}
