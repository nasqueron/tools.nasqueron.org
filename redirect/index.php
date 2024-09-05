<?php

function resolve_url (string $url) : string {
    $search = [
        "%%YYYY%%",
        "%%MM%%",
        "%%DD%%",
    ];
    $replace = [
        date('Y'),
        date('m'),
        date('d'),
    ];

    return str_replace($search, $replace, $url);
}

$target = $_REQUEST["target"] ?? $_REQUEST["t"] ?? "";
$targets = require('_targets.php');

if ($target !== "" && array_key_exists($target, $targets)) {
    $target_url = resolve_url($targets[$target]);
    header('Location: '. $target_url);
    exit;
}

header("HTTP/1.0 404 Not Found");
