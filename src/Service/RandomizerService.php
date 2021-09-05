<?php

namespace App\Service;

class RandomizerService
{
    public static function getUniqueRandomArray(int $count, int $min, int $max): array
    {
        for (;;)
        {
            $result = self::getRandomArray($count, $min, $max);

            if (sizeof(array_unique($result)) == sizeof($result))
            {
                return $result;
            }
        }
    }

    public static function getRandomArray(int $count, int $min, int $max): array
    {
        $result = [];
    
        for ($i = 0; $i < $count; ++$i)
        {
            $result[] = rand($min, $max);
        }

        return $result;
    }
}