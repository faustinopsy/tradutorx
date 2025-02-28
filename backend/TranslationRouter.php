<?php
namespace App;

use App\gpt\ChatGPT;
use App\google\GoogleTranslator;
use App\gemma\GemmaTranslator;

class TranslationRouter {
    private $chatGPT;
    private $googleTranslator;
    private $gemmaTranslator;

    public function __construct(ChatGPT $chatGPT, GoogleTranslator $googleTranslator, GemmaTranslator $gemmaTranslator) {
        $this->chatGPT = $chatGPT;
        $this->googleTranslator = $googleTranslator;
        $this->gemmaTranslator = $gemmaTranslator;
    }

    public function translate($text, $langDestino, $useGPT = false, $useGemma = false) {
        if ($useGemma) {
            return $this->gemmaTranslator->translate($text, $langDestino);
        } elseif ($useGPT) {
            return $this->chatGPT->translate($text, $langDestino);
        } else {
            return $this->googleTranslator->translate($text, $langDestino);
        }
    }
}
