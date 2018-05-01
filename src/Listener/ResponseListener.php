<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseListener {

	/**
	 * Actions, performed for all responses
	 * Allows to filter a Response object
	 */
	function onResponse(FilterResponseEvent $event) {
		$response = $event->getResponse();

		// set Content-Security-Policy header for XSS prevention
		// these will block all inline/external scripts without nonce
		$csp_nonce = base64_encode(random_bytes(20));
		$response->headers->set("Content-Security-Policy", "script-src 'self' 'strict-dynamic' 'nonce-$csp_nonce'");
	}

}
