<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidatorService
{
    public function validateFields(Request $request, array $fieldNames, array $fieldTypes, array $required, array $allowedOrMin = [])
    {
        $result = [];

        for ($i = 0; $i < count($fieldNames); $i++) {
            $field = $request->query->get($fieldNames[$i]);

            if ($field === null) {
                if ($required[$i]) {
                    throw new BadRequestHttpException($fieldNames[$i] . ' is a required field and it was not passed in!');
                }

                if ($fieldTypes[$i] == 'array') {
                    $result[$fieldNames[$i]] = [];
                } else {
                    $result[$fieldNames[$i]] = null;
                }

                continue;
            }

            if (gettype($field) != $fieldTypes[$i]) {
                if (!((($fieldTypes[$i] == 'integer' || $fieldTypes[$i] == 'double')
                    && is_numeric($field)) || $fieldTypes[$i] == 'array' || $fieldTypes[$i] == 'boolean')) {
                    throw new BadRequestHttpException($fieldNames[$i] . ' does not have the required type of ' . $fieldTypes[$i]);
                }

                if ($fieldTypes[$i] == 'integer') {
                    $field = (int) $field;
                } elseif ($fieldTypes[$i] == 'double') {
                    $field = (float) $field;
                } elseif ($fieldTypes[$i] == 'boolean') {
                    $field = $field == 'true' ? true : false;
                }
            }

            if ($fieldTypes[$i] == 'integer' && $allowedOrMin[$i] !== null && $field !== null && $field < $allowedOrMin[$i]) {
                throw new BadRequestHttpException($fieldNames[$i] . ' needs to have value >= ' . $allowedOrMin[$i]);
            }

            if ($fieldTypes[$i] == 'array') {
                $field = explode(',', $field);
                $field = $field != [''] ? $field : null;

                if (!empty($allowedOrMin[$i])) {
                    foreach ($field as $key) {
                        if (!in_array($key, $allowedOrMin[$i])) {
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
