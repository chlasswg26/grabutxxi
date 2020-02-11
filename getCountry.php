<?php
require_once('lib.function.php');

use simple_curl\curl;

    $snipe = URL . 'advanced-search/';

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('Select Country', $response);
    $content = explode('Select Year', $content[1]);

    preg_match_all('/">(.*?)<\/option>/', $content[0], $country);

    $array = [];
    for ($i = 0; $i < count($country[1]); $i++)
    {
        $array[] = [
            'code' => base64_encode($country[1][$i])
        ];
        
    }

    echo json_encode($array, JSON_PRETTY_PRINT);