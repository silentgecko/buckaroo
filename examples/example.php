<?php

namespace LinkORB\Buckaroo;

use LinkORB\Buckaroo\SOAP\Body;
use LinkORB\Buckaroo\SOAP\IPAddress;
use LinkORB\Buckaroo\SOAP\RequestParameter;
use LinkORB\Buckaroo\SOAP\Service;
use LinkORB\Buckaroo\SOAP\Services;

class Example
{
    static function demoRequest()
    {

        // Start autoloader for class files
        require_once(__DIR__ . '/../vendor/autoload.php');

        // Configuration
        $websiteKey = 'CHANGEME';
        $req = new Request($websiteKey);
        $req->loadPem('private_key.pem');

        // Create the message body (actual request)
        $TransactionRequest = new Body();
        $TransactionRequest->Currency = 'EUR';
        $TransactionRequest->AmountDebit = 1.34;
        $TransactionRequest->Invoice = 'DNK_PHP_1';
        $TransactionRequest->Description = 'Example description for this request';
        $TransactionRequest->ReturnURL = 'http://www.linkorb.com/';
        $TransactionRequest->StartRecurrent = false;

        // Specify which service / action we are calling
        $TransactionRequest->Services = new Services();
        $TransactionRequest->Services->Service = new Service('ideal', 'Pay', 2);

        // Add parameters for this service
        $TransactionRequest->Services->Service->RequestParameter = new RequestParameter(
            'issuer',
            'RABONL2U'
        );

        // Optionally pass the client ip-address for logging
        $TransactionRequest->ClientIP = new IPAddress('123.123.123.123');

        // Send the request to Buckaroo, and retrieve the response
        $response = $req->sendRequest($TransactionRequest, 'transaction');

        // Display the response:
        var_dump($response);
    }
}

Example::demoRequest();
