<?php
require_once('lib.function.php');

use simple_curl\curl;

    $snipe = URL . 'popular-movies/';

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('<div id="boxid">', $response);
    $content = explode('<div id="sidebar_right">', $content[1]);

    preg_match_all('/href="(.*?)"/', $content[0], $url);
    preg_match_all('/<div class="footerpost"><h2>(.*?)<\/h2>/', $content[0], $title);
    preg_match_all('/data-lazy-src="\/\/(.*?)"/', $content[0], $image);

    $page = explode('<div class="pagination">', $response);
    $page = explode('</div>', $page[1]);
    preg_match('/class="page-numbers current">(\d)<\/span>/', $page[0], $pagination);

    $array = [];
    for ($i = 0; $i < count($url[1]); $i++)
    {
        $array[] = [
            'code' => base64_encode(str_replace('/', '', str_replace(URL, '', $url[1][$i]))),
            'title' => $title[1][$i],
            'image' => toBase64('https://' . $image[1][$i])
        ];
    }

    echo json_encode((object)$array, JSON_PRETTY_PRINT);