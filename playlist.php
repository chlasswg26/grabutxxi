<?php
require_once('lib.function.php');

use simple_curl\curl;

$query = base64_decode($_GET['query']);

if (empty($query) || !isset($query))
{
    die();
} else {
    $snipe = URL . 'playlist/' . $query . '/';

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('<div id="boxid">', $response);
    $content = explode('<div id="sidebar_right">', $content[1]);

    preg_match_all('/<a class=".*?" rel=".*?" href="(.*?)"/', $content[0], $url);
    preg_match_all('/<div class="footerpost"><h2>(.*?)<\/h2>/', $content[0], $title);

    $page = explode('<div class="pagination">', $response);
    $page = explode('</div>', $page[1]);
    preg_match('/class="page-numbers current">(\d)<\/span>/', $page[0], $pagination);

    $array = [];
    for ($i = 0; $i < count($url[1]); $i++)
    {
        $array[] = [
            'code' => base64_encode(str_replace('/', '', str_replace(URL, '', $url[1][$i]))),
            'title' => $title[1][$i]
        ];
    }

    echo json_encode($array, JSON_PRETTY_PRINT);
}