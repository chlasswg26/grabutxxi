<?php
require_once('lib.function.php');

use simple_curl\curl;

    $snipe = URL . 'advanced-search/';

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('Select Genre 1', $response);
    $content = explode('Select Genre 2', $content[1]);

    preg_match_all('/">(.*?)<\/option>/', $content[0], $genre);

    $array = [];
    for ($i = 0; $i < count($genre[1]); $i++)
    {
        $array[] = [
            'code' => base64_encode(str_replace(' ', '-', $genre[1][$i])),
            'title' => $genre[1][$i]
        ];
        
    }

    echo json_encode((object)$array, JSON_PRETTY_PRINT);