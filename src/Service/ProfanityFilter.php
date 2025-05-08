<?php

namespace App\Service;

class ProfanityFilter
{
    private array $profanityWords;

    public function __construct(array $profanityWords = [])
    {
        // Liste par défaut si aucune n'est fournie
        $this->profanityWords = $profanityWords ?: [
            'badword', 'insult', 'offensive',
            'fuck', 'shit', 'asshole',
            'connard', 'salope', 'pute',
        ];
    }

    public function containsProfanity(string $text): bool
    {
        $text = mb_strtolower($text);

        foreach ($this->profanityWords as $word) {
            if (false !== mb_strpos($text, mb_strtolower($word))) {
                return true;
            }
        }

        return false;
    }

    public function getProfanityWords(): array
    {
        return $this->profanityWords;
    }

    public function findProfanities(string $text): array
    {
        $found = [];
        foreach ($this->profanityWords as $word) {
            if (preg_match('/\b'.preg_quote($word, '/').'\b/i', $text)) {
                $found[] = $word;
            }
        }

        return $found;
    }
}
