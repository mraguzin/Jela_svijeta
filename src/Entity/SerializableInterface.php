<?php

namespace App\Entity;

interface SerializableInterface
{
    public function getFullObject(string $language, $timestamp, array $ignoredFields = [], bool $json = false);
}