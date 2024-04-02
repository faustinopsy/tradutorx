<?php
namespace App\google;
use App\CacheHandler;
use App\helper\CurlHandler;

class GoogleTranslator {
    private $curlHandler;
    private $cacheHandler;
    private $apiURL = 'https://translation.googleapis.com/language/translate/v2';
    private $apiKey;

    public function __construct($apiKey, CurlHandler $curlHandler, CacheHandler $cacheHandler) {
        $this->apiKey = $apiKey;
        $this->curlHandler = $curlHandler;
        $this->cacheHandler = $cacheHandler;
    }

    public function translate($text, $langDestino) {
        $cacheKey = "google_translate_{$text}_{$langDestino}";
        $cachedTranslation = $this->cacheHandler->buscaCache($cacheKey);
        if ($cachedTranslation) {
            return ['response' => $cachedTranslation];
        }
        $headers = ['Content-Type: application/json'];
        $payload = [
            'q' => $text,
            'target' => $langDestino,
            'source' => 'pt'
        ];
        $url = $this->apiURL . '?key=' . $this->apiKey;
        $response = $this->curlHandler->executeCurl($url, $headers, $payload);
                    $this->cacheHandler->salvaNoCache($cacheKey, $response);
        if (isset($response['error'])) {
            return ['error' => $response['error']['message']];
        }
        return ['response' => $response];
    }
}
