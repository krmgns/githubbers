<?php
function gc_db_repo($name) {
    $file = sprintf('./db/repo/%s.json', $name);
    if (!is_file($file)) {
        touch($file);
    }
    return $file;
}

function gc_db_get_repo($name) {
    return json_decode(file_get_contents(gc_db_repo($name)), true);
}

function gc_db_put_repo($name, array $data) {
    return file_put_contents(gc_db_repo($name), json_encode($data));
}
