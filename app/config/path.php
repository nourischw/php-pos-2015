<?php

$domain = "http://".$_SERVER['HTTP_HOST'].'/pos';
$root = $domain . '/';

return array(
    // Root path
    'ROOT' => $root,

    // Images path
    'IMAGES' => $root . 'img/',

    // CSS, JavaScript path
	'JS' => $root . 'app/js/app/',
	'JS_LIBS' => $root . 'app/js/libs/',
	
    'CSS' => $root . 'app/css/',
	
	// Log files path
	'LOG_QUERY_TESTSPEED' => $_SERVER['DOCUMENT_ROOT'] . "/pos/app/storage/logs/test_query_speed.log"
);
