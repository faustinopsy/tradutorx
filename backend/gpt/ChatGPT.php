<?php
namespace App\gpt;
use App\CacheHandler;
use App\helper\CurlHandler;

class ChatGPT {
    private $curlHandler;
    private $cacheHandler;
    private $apiURL = 'https://api.openai.com/v1/chat/completions';
    private $apiKey;

    public function __construct($apiKey, CurlHandler $curlHandler, CacheHandler $cacheHandler) {
        $this->apiKey = $apiKey;
        $this->curlHandler = $curlHandler;
        $this->cacheHandler = $cacheHandler;
    }

    public function translate($text,$langDestino) {
        $ligua=[
        "es-ES" => "spanish",
        "en" => "English",
        "fr-FR" => "French",
        ];
        $cacheKey = "chatgpt_translate_{$text}";
        $cachedTranslation = $this->cacheHandler->buscaCache($cacheKey);
        if ($cachedTranslation) {
            return $cachedTranslation;
        }
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ];

        $payload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'translate the text from Portuguese to '. $ligua[$langDestino] ],
                ['role' => 'user', 'content' => $text],
            ],
        ];

        $response = $this->curlHandler->executeCurl($this->apiURL, $headers, $payload);
                    $this->cacheHandler->salvaNoCache($cacheKey, $response);
        if (isset($response['error'])) {
            return ['error' => $response['error']['message']];
        }
        $chatbotResponse = $response['choices'][0]['message']['content'];
        return ['response' => $chatbotResponse, 'prompt_tokens'=> $response["usage"]["prompt_tokens"], "total_token"=> $response["usage"]["total_tokens"]];
    }
}
