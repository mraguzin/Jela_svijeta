<?php

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new ErrorResponse($exception->getMessage(), null, [], $exception->getCode());

        $event->setResponse($response);
    }
}