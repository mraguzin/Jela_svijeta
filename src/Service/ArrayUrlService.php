<?php

namespace App\Service;

class ArrayUrlService
{
    public function escapeArray(array $fields)
    {
        $result = [];

        foreach ($fields as $key=>$field)
        {
            if (is_array($field))
            {
                if (empty($field))
                {
                    unset($field[$key]);
                }

                else
                {
                    $result[$key] = implode(',', $field);
                }
            }

            else
            {
                $result[$key] = $field;
            }
        }

        return $result;
    }
}