<?php
namespace App\gemma;

use App\helper\CurlHandler;
use App\CacheHandler;

class GemmaTranslator {
    private $curlHandler;
    private $cacheHandler;
    private $modelo;
    private $apiURL = 'http://localhost:11434/api/generate';

    public function __construct(CurlHandler $curlHandler, CacheHandler $cacheHandler) {
         $this->curlHandler = $curlHandler;
         $this->cacheHandler = $cacheHandler;
         $this->modelo = 'gemma:2b';
    }

    public function translate($text, $langDestino) {
         $cacheKey = "gemma_translate_{$text}_{$langDestino}";
         $cachedTranslation = $this->cacheHandler->buscaCache($cacheKey);
         if ($cachedTranslation) {
             return ['response' => $cachedTranslation];
         }
         
         $headers = ['Content-Type: application/json'];
         $payload = [
             'prompt' => "You are a professional interpreter specialized in simultaneous translation and contextual interpretation. When you receive a text, do not simply translate it word-for-word; analyze the context, preserve nuances, tone, and the original intent of the message. Furthermore, if the user initiates a conversation, asks questions, or discusses other topics, respond naturally while maintaining your role as an interpreter. Adapt your responses to reflect both the translation and the interactive context of the conversation, ensuring clear, accurate, and human communication. and Translate from Portuguese to {$langDestino}: " . $text,
             'model' => $this->modelo,
             'stream' => false
         ];
         $response = $this->curlHandler->executeCurl($this->apiURL, $headers, $payload);
         $this->cacheHandler->salvaNoCache($cacheKey, $response);
         
         if (isset($response['error'])) {
             return ['error' => $response['error']['message']];
         }
         
         return ['response' => $response['response']];
    }
}
