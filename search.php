<?php
require_once('lib.function.php');

use simple_curl\curl;

$query = strtolower($_GET['query']);
$page = $_GET['page'];

if (empty($query) || !isset($query))
{
    die();
} else {
    if ((!empty($page) || isset($page)) && is_numeric($page))
    {
        $snipe = URL . 'page/' . $page . '/?s=' . $query . '&post_type=post';
    } else {
        $snipe = URL . '?s=' . $query . '&post_type=post';
    }

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('<div id="content">', $response);
    $content = explode('<div id="sidebar_right">', $content[1]);

    preg_match_all('/<a class=".*?" rel=".*?" href="(.*?)"/', $content[0], $url);
    preg_match_all('/<div class="footerpost">\s+<h2>(.*?)<\/h2>/', $content[0], $title);
    preg_match_all('/<img width=".*?" height=".*?" src="(.*?)"/', $content[0], $image);

    $page = explode('<div class="pagination">', $response);
    $page = explode('</div>', $page[1]);
    preg_match('/class="page-numbers current">(\d)<\/span>/', $page[0], $pagination);

    $array = [
        'isPaging' => (boolean)$pagination[1],
        'page' => [],
        'data' => []
    ];
        if($pagination[1] == true)
        {
            preg_match('/<a class="next page-numbers" href=".*?page\/(\d)\//', $page[0], $next);
            preg_match('/<a class="prev page-numbers" href=".*?page\/(\d)\//', $page[0], $prev);
            $array['page'] = [
                    'next' => $next[1],
                    'prev' => $prev[1]
            ];
        }

    for ($i = 0; $i < count($url[1]); $i++)
    {
        $array['data'][] = [
            'code' => base64_encode(str_replace('/', '', str_replace(URL, '', $url[1][$i]))),
            'title' => $title[1][$i],
            'image' => toBase64($image[1][$i])
        ];
    }

    echo json_encode($array, JSON_PRETTY_PRINT);
}