<?php
require_once('lib.function.php');

use simple_curl\curl;

$query = base64_decode($_GET['query']);
$server = $_GET['server'];

if (empty($query) && !isset($query))
{
    die();
} else {
    if (!empty($server) && isset($server) && is_numeric($server))
    {
        $snipe = URL . $query . '/?v=' . $server;
    } else {
        $snipe = URL . $query . '/?v=1';
    }

    curl::prepare($snipe, NULL);
    curl::exec_get();
    $response = curl::get_response();

    $content = explode('<div id="content">', $response);
    $content = explode('<div class="relatedposts">', $content[1]);

    preg_match('/<h1>(.*?)<\/h1>/', $content[0], $title);
    preg_match('/<div class="bottomtitle">(.*?)<\/div>/', $content[0], $uploader);
    preg_match('/<div id="embed_holder">.*?src="(.*?)"/', $content[0], $stream_url);
    preg_match_all('/<li><a href="(.*?)"/', $content[0], $server_list);
    preg_match_all('/<li><a href=".*?">(\w+)<\/a>/', $content[0], $server_name);
    preg_match('/<span class="right">(\d+).*?<\/span>/', $content[0], $viewer);
    preg_match('/<\/span>\s+<a href="(.*?)" class="downloadlink"/', $content[0], $download);
    preg_match('/<div class="infomovie">\s+<img width="\d+" height="\d+" src="(.*?)"/', $content[0], $image);
    preg_match('/<\/span>\s+<span class="synopsis".*<p>(.*)/', $content[0], $synopsis);

    $array = [
        'title' => $title[1],
        'uploader' => $uploader[1],
        'stream' => [
            'embed' => $stream_url[1],
            'list' => []
        ],
        'viewer' => $viewer[1],
        'download' => $download[1],
        'image' => toBase64($image[1]),
        'synopsis' => preg_replace('/<em><strong><a href=".*?">.*?<\/a><\/strong><\/em>/', '', $synopsis[1])
    ];

    for ($i = 0; $i < count($server_list[1]); $i++)
    {
        $array['stream']['list'][] = [
            'server' => str_replace('?v=', '', $server_list[1][$i]),
            'provider' => strtoupper($server_name[1][$i])
        ];
    }

    echo json_encode((object)$array, JSON_PRETTY_PRINT);
}