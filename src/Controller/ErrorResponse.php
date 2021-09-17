<?php

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(string $message, $data = null, array $errors = [], int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($message, $data, $errors), $status, $headers, $json);
    }

    private function format(string $message, $data = null, array $errors = [])
    {
        if ($data === null) {
            $data = [];
        }

        $response = ['message' => $message, 'data' => $data];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}