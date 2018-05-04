<?php

// generates Content-Security-Policy nonce
$container->setParameter("csp_nonce", base64_encode(random_bytes(20)));
