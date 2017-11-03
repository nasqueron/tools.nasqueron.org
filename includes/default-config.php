<?php

$Config = [
    // Theme
    'Theme' => 'RalfFallsIntoFoundation',

    // Router configuration
    'SiteURL' => get_server_url(),
    'BaseURL' => '',
    'AllowTopicArticleRequest' => true,
    'Homepage' => '_index/index.html',
];

$Config['Pages'] = [
    'Error404' => 'themes/' . $Config['Theme']  . '/404.php',
];
