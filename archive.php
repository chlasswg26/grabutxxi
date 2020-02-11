<?php
require_once('lib.function.php');

use simple_curl\curl;

    $snipe = URL . 'playlist/';

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('<div id="content">', $response);
    $content = explode('<div id="sidebar_right">', $content[1]);
    
    preg_match_all('/href="(.*?)"/', $content[0], $url);
    preg_match_all('/data-lazy-src="\/\/(.*?)"/', $content[0], $image);

    $array = [];
    for ($i = 0; $i < count($url[1]); $i++)
    {
        $array[] = [
            'code' => base64_encode(str_replace('/', '', str_replace(URL . 'playlist', '', $url[1][$i]))),
            'image' => toBase64('https://' . $image[1][$i])
        ];
    }

    echo json_encode($array, JSON_PRETTY_PRINT);