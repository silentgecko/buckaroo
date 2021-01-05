<?php

namespace LinkORB\Buckaroo;

use InvalidArgumentException;
use LinkORB\Buckaroo\SOAP\Body;
use LinkORB\Buckaroo\SOAP\DigestMethodType;
use LinkORB\Buckaroo\SOAP\Header;
use LinkORB\Buckaroo\SOAP\MessageControlBlock;
use LinkORB\Buckaroo\SOAP\ReferenceType;
use LinkORB\Buckaroo\SOAP\SecurityType;
use LinkORB\Buckaroo\SOAP\SignatureType;
use LinkORB\Buckaroo\SOAP\SignedInfoType;
use LinkORB\Buckaroo\SOAP\TransformType;
use SOAPHeader;

class Request
{

    protected static array $defaultSoapOptions = [
        'trace' => 1,
        'classmap' => [
            'Body' => 'LinkORB\\Buckaroo\\SOAP\\Type\\Body',
            'Status' => 'LinkORB\\Buckaroo\\SOAP\\Type\\Status',
            'RequiredAction' => 'LinkORB\\Buckaroo\\SOAP\\Type\\RequiredAction',
            'ParameterError' => 'LinkORB\\Buckaroo\\SOAP\\Type\\ParameterError',
            'CustomParameterError' => 'LinkORB\\Buckaroo\\SOAP\\Type\\CustomParameterError',
            'ServiceError' => 'LinkORB\\Buckaroo\\SOAP\\Type\\ServiceError',
            'ActionError' => 'LinkORB\\Buckaroo\\SOAP\\Type\\ActionError',
            'ChannelError' => 'LinkORB\\Buckaroo\\SOAP\\Type\\ChannelError',
            'RequestErrors' => 'LinkORB\\Buckaroo\\SOAP\\Type\\RequestErrors',
            'StatusCode' => 'LinkORB\\Buckaroo\\SOAP\\Type\\StatusCode',
            'StatusSubCode' => 'LinkORB\\Buckaroo\\SOAP\\Type\\StatusCode',
        ],
    ];
    private ?SoapClientWSSEC $soapClient = null;
    private ?string $websiteKey = null;
    private string $culture = 'nl-NL';
    private bool $testMode = false;
    private string $channel = 'Web';

    public function __construct(string $websiteKey = null, bool $testMode = false, array $soapOptions = [])
    {
        $this->websiteKey = $websiteKey;
        $this->testMode = $testMode;

        $wsdl_url = "https://checkout.buckaroo.nl/soap/soap.svc?wsdl";
        $this->soapClient = new SoapClientWSSEC($wsdl_url, array_merge(static::$defaultSoapOptions, $soapOptions));
    }

    public function loadPem(string $filename) :void
    {
        $this->soapClient->loadPem($filename);
    }

    public function setChannel(string $channel) :void
    {
        $this->channel = $channel;
    }

    public function sendRequest(Body $TransactionRequest, string $type) :array
    {

        if (!$this->websiteKey) {
            throw new InvalidArgumentException('websiteKey not defined');
        }

        // Envelope and wrapper stuff
        $Header = new Header();
        $Header->MessageControlBlock = new MessageControlBlock();
        $Header->MessageControlBlock->Id = '_control';
        $Header->MessageControlBlock->WebsiteKey = $this->websiteKey;
        $Header->MessageControlBlock->Culture = $this->culture;

        $Header->MessageControlBlock->TimeStamp = time();
        $Header->MessageControlBlock->Channel = $this->channel;
        $Header->Security = new SecurityType();
        $Header->Security->Signature = new SignatureType();
        $Header->Security->Signature->SignedInfo = new SignedInfoType();

        $Reference = new ReferenceType();
        $Reference->URI = '#_body';
        $Transform = new TransformType();
        $Transform->Algorithm = 'http://www.w3.org/2001/10/xml-exc-c14n#';
        $Reference->Transforms = [$Transform];

        $Reference->DigestMethod = new DigestMethodType();
        $Reference->DigestMethod->Algorithm = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $Reference->DigestValue = '';

        $Transform2 = new TransformType();
        $Transform2->Algorithm = 'http://www.w3.org/2001/10/xml-exc-c14n#';
        $ReferenceControl = new ReferenceType();
        $ReferenceControl->URI = '#_control';
        $ReferenceControl->DigestMethod = new DigestMethodType();
        $ReferenceControl->DigestMethod->Algorithm = 'http://www.w3.org/2000/09/xmldsig#sha1';
        $ReferenceControl->DigestValue = '';
        $ReferenceControl->Transforms = [$Transform2];

        $Header->Security->Signature->SignedInfo->Reference = [$Reference, $ReferenceControl];
        $Header->Security->Signature->SignatureValue = '';

        $soapHeaders[] = new SOAPHeader(
            'https://checkout.buckaroo.nl/PaymentEngine/',
            'MessageControlBlock',
            $Header->MessageControlBlock
        );
        $soapHeaders[] = new SOAPHeader(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            'Security',
            $Header->Security
        );
        $this->soapClient->__setSoapHeaders($soapHeaders);

        if ($this->testMode) {
            $this->soapClient->__SetLocation('https://testcheckout.buckaroo.nl/soap/');
        } else {
            $this->soapClient->__SetLocation('https://checkout.buckaroo.nl/soap/');
        }

        $return = [];
        switch ($type) {
            case 'invoiceinfo':
                $return['result'] = $this->soapClient->InvoiceInfo($TransactionRequest);
                break;
            case 'transaction':
                $return['result'] = $this->soapClient->TransactionRequest($TransactionRequest);
                break;
            case 'transactionstatus':
                $return['result'] = $this->soapClient->TransactionStatus($TransactionRequest);
                break;
            case 'refundinfo':
                $return['result'] = $this->soapClient->RefundInfo($TransactionRequest);
                break;
        }

        $return['response'] = $this->soapClient->__getLastResponse();
        $return['request'] = $this->soapClient->__getLastRequest();

        return $return;
    }

    public function getTestMode() :bool
    {
        return $this->testMode;
    }

    public function setTestMode(bool $testMode) :self
    {
        $this->testMode = $testMode;

        return $this;
    }

    public function getCulture() :string
    {
        return $this->culture;
    }

    public function setCulture(string $culture) :self
    {
        $this->culture = $culture;

        return $this;
    }
}
