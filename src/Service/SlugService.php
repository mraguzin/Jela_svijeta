<?php

namespace App\Service;

class SlugService
{
    public static function escapeText(string $text)
    {
        $lowercased = substr(strtolower($text), 0, 35);
        return str_replace(array('š', 'đ', 'č', 'ć', 'ž', ' ', '.', '!', '?', '+', '&', '#', ':', ';', ',', '"', '\''),
                           array('s', 'd', 'c', 'c', 'z', '-', '',  '',  '',  'p', '',  '',  '',  '',  '',  '',  ''  ), $lowercased);
    }
}