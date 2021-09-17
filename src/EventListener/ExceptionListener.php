<?php

namespace App\EventListener;

use App\Controller\ErrorResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
            $response = new ErrorResponse($exception->getMessage(), null, [], $exception->getStatusCode());
            $event->setResponse($response);
        }
    }
}