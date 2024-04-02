<?php
namespace App;
use App\gpt\ChatGPT;
use App\google\GoogleTranslator;
class TranslationRouter {
    private $chatGPT;
    private $googleTranslator;

    public function __construct(ChatGPT $chatGPT, GoogleTranslator $googleTranslator) {
        $this->chatGPT = $chatGPT;
        $this->googleTranslator = $googleTranslator;
    }

    public function translate($text, $langDestino, $useGPT = false) {
        if ($useGPT) {
            return $this->chatGPT->translate($text,$langDestino);
        } else {
            return $this->googleTranslator->translate($text, $langDestino);
        }
    }
}
