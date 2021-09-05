<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidatorService
{
    public function validateFields(Request $request, array $fieldNames, array $fieldTypes, array $required, array $allowedOrMin = [])
    {
        $result = [];

        for ($i = 0; $i < sizeof($fieldNames); ++$i)
        {
            $field = $request->query->get($fieldNames[$i]);
            if ($field === null)
            {
                if ($required[$i])
                {
                    throw new BadRequestHttpException($fieldNames[$i] . ' is a required field and it was not passed in!');
                }

                if ($fieldTypes[$i] == 'array')
                {
                    $result[$fieldNames[$i]] = [];
                }

                else
                {
                    $result[$fieldNames[$i]] = null;
                }
                
                continue;                
            }

            if (gettype($field) != $fieldTypes[$i])
            {
                echo gettype($field);
                echo $fieldTypes[$i];
                if (((gettype($field) != 'integer' && gettype($field) != 'double') || !is_numeric($field)))
                {
                    throw new BadRequestHttpException($fieldNames[$i] . ' does not have the required type of ' . $fieldTypes[$i]);
                }
            }            

            if ($fieldTypes[$i] == 'integer' && $allowedOrMin[$i] !== null && $field !== null && $field < $allowedOrMin[$i])
            {
                throw new BadRequestHttpException($fieldNames[$i] . ' needs to have value >= ' . $allowedOrMin[$i]);
            }

            if ($fieldTypes[$i] == 'array')
            {
                $field = explode(',', $field);
                $keys = array_flip($field);
                $allowedKeys = array_flip($allowedOrMin);

                if (!empty($allowedOrMin[$i]))
                {
                    foreach ($keys as $key)
                    {
                        if (!array_key_exists($key, $allowedKeys))
                        {
                            throw new BadRequestHttpException($fieldNames[$i] . ' can only accept the following values: ' . implode(',', $allowedOrMin[$i]));
                        }
                    }
                }
            }

            $result[$fieldNames[$i]] = $field;
            
        }

        return $result;
    }
}