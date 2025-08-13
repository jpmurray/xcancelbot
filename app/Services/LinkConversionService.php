<?php

namespace App\Services;

class LinkConversionService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('xcancel');
    }

    public function containsTwitterLink(string $content): bool
    {
        foreach ($this->config['patterns'] as $pattern) {
            if (preg_match($pattern['match'], $content)) {
                return true;
            }
        }
        return false;
    }

    public function convert(string $content): ?object
    {
        $convertedLinks = [];
        $stats = ['twitter' => 0, 'x' => 0];
        $originalLinks = [];

        foreach ($this->config['patterns'] as $type => $pattern) {
            if (preg_match_all($pattern['match'], $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $originalLink = $match[0];
                    $originalLinks[] = $originalLink;
                    
                    $convertedLink = str_replace('{domain}', $this->config['domain'], $pattern['replace']);
                    $convertedLink = preg_replace($pattern['match'], $convertedLink, $originalLink);
                    
                    if (!in_array($convertedLink, $convertedLinks)) {
                        $convertedLinks[] = $convertedLink;
                        $stats[$type]++;
                    }
                }
            }
        }

        if (empty($convertedLinks)) {
            return null;
        }

        return (object) [
            'convertedContent' => implode("\n", $convertedLinks),
            'stats' => $stats,
            'originalLinks' => $originalLinks
        ];
    }
}