<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener
{
    public function __construct($csp_nonce)
    {
        $this->csp_nonce = $csp_nonce;
    }

    /**
     * Actions, performed for all responses
     * Allows to filter a Response object
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        // set Content-Security-Policy header for XSS prevention
        // these will block all internal/external scripts without nonce
        $response->headers->set("Content-Security-Policy", "script-src 'strict-dynamic' 'nonce-{$this->csp_nonce}'");
    }
}
