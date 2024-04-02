<?php

require '../vendor/autoload.php';

use App\gpt\ChatGPT;
use App\google\GoogleTranslator;
use App\helper\CurlHandler;
use App\TranslationRouter;
use App\CacheHandler;

require_once __DIR__ . '/config/config.php';

$curlHandler = new CurlHandler();
$cache = new CacheHandler();
$chatGPT = new ChatGPT(OPENAI_API_KEY, $curlHandler, $cache );
$googleTranslator = new GoogleTranslator(API, $curlHandler, $cache );
$router = new TranslationRouter($chatGPT, $googleTranslator);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    $text = $data['text'] ?? '';
    $langDestino = $data['langDestino'] ?? 'en';
    $useGPT = $data['useGPT'] ?? false;
    $response = $router->translate($text, $langDestino, $useGPT);

    echo json_encode($response);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}
