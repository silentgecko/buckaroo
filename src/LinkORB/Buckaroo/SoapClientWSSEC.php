<?php

namespace LinkORB\Buckaroo;

use DOMDocument;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;
use SoapClient;

class SoapClientWSSEC extends SoapClient
{
    private $pemdata = null;

    public function __call($name, $args)
    {
        // buckaroo requires all numbers to have period notation, otherwise
        // an internal error will occur on the server.
        $locale = setlocale(LC_NUMERIC, '0');
        setlocale(LC_NUMERIC, ['en_US', 'en_US.UTF-8']);
        $ret = parent::__call($name, $args);
        setlocale(LC_NUMERIC, $locale);

        return $ret;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $domDOC = new DOMDocument();
        $domDOC->loadXML($request);

        if (!$this->pemdata) {
            throw new InvalidArgumentException('PEM file not yet loaded. Use loadPem()');
        }

        //Sign the document
        $this->SignDomDocument($domDOC);

        return parent::__doRequest($domDOC->saveXML($domDOC->documentElement), $location, $action, $version, $one_way);
    }

    private function SignDomDocument(DOMDocument $domDocument) :void
    {
        //create xPath
        $xPath = new DOMXPath($domDocument);

        //register namespaces to use in xpath query's
        $xPath->registerNamespace(
            'wsse',
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'
        );
        $xPath->registerNamespace('sig', 'http://www.w3.org/2000/09/xmldsig#');
        $xPath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');

        //Set id on soap body to easily extract the body later.
        $bodyNodeList = $xPath->query('/soap:Envelope/soap:Body');
        $bodyNode = $bodyNodeList->item(0);
        $bodyNode->setAttribute('Id', '_body');

        //Get the digest values
        $controlHash = $this->CalculateDigestValue($this->GetCanonical($this->GetReference('_control', $xPath)));
        $bodyHash = $this->CalculateDigestValue($this->GetCanonical($this->GetReference('_body', $xPath)));

        //Set the digest value for the control reference
        $Control = '#_control';
        $controlHashQuery = $query = '//*[@URI="' . $Control . '"]/sig:DigestValue';
        $controlHashQueryNodeset = $xPath->query($controlHashQuery);
        $controlHashNode = $controlHashQueryNodeset->item(0);
        $controlHashNode->nodeValue = $controlHash;

        //Set the digest value for the body reference
        $Body = '#_body';
        $bodyHashQuery = $query = '//*[@URI="' . $Body . '"]/sig:DigestValue';
        $bodyHashQueryNodeset = $xPath->query($bodyHashQuery);
        $bodyHashNode = $bodyHashQueryNodeset->item(0);
        $bodyHashNode->nodeValue = $bodyHash;

        //Get the SignedInfo nodeset
        $SignedInfoQuery = '//wsse:Security/sig:Signature/sig:SignedInfo';
        $SignedInfoQueryNodeSet = $xPath->query($SignedInfoQuery);
        $SignedInfoNodeSet = $SignedInfoQueryNodeSet->item(0);

        //Canonicalize nodeset
        $signedINFO = $this->GetCanonical($SignedInfoNodeSet);

        //Sign signedinfo with privatekey
        openssl_sign($signedINFO, $signature2, $this->pemdata);

        //Add signature value to xml document
        $sigValQuery = '//wsse:Security/sig:Signature/sig:SignatureValue';
        $sigValQueryNodeset = $xPath->query($sigValQuery);
        $sigValNodeSet = $sigValQueryNodeset->item(0);
        $sigValNodeSet->nodeValue = base64_encode($signature2);

        //Get signature node
        $sigQuery = '//wsse:Security/sig:Signature';
        $sigQueryNodeset = $xPath->query($sigQuery);
        $sigNodeSet = $sigQueryNodeset->item(0);

        //Create keyinfo element and Add public key to KeyIdentifier element
        $KeyTypeNode = $domDocument->createElementNS("http://www.w3.org/2000/09/xmldsig#", "KeyInfo");
        $SecurityTokenReference = $domDocument->createElementNS(
            'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            'SecurityTokenReference'
        );
        $KeyIdentifier = $domDocument->createElement("KeyIdentifier");

        $thumbprint = $this->sha1_thumbprint($this->pemdata);
        $KeyIdentifier->nodeValue = $thumbprint;
        $KeyIdentifier->setAttribute(
            'ValueType',
            'http://docs.oasis-open.org/wss/oasis-wss-soap-message-security-1.1#ThumbPrintSHA1'
        );
        $SecurityTokenReference->appendChild($KeyIdentifier);
        $KeyTypeNode->appendChild($SecurityTokenReference);
        $sigNodeSet->appendChild($KeyTypeNode);
    }

    /**
     * Calculate digest value (sha1 hash)
     */
    private function CalculateDigestValue(string $input) :string
    {
        $digValueControl = base64_encode(pack("H*", sha1($input)));

        return $digValueControl;
    }

    /**
     * Canonicalize nodeset
     * @return string|false on error
     */
    private function GetCanonical(DOMNode $Object)
    {
        return $Object->C14N(true, false);
    }

    /**
     * Get nodeset based on xpath and ID
     */
    private function GetReference(string $id, DOMXPath $xPath) :?DOMNode
    {
        $query = '//*[@Id="' . $id . '"]';
        $nodeset = $xPath->query($query);

        return $nodeset->item(0);
    }

    /**
     * @param mixed $fullcert
     */
    private function sha1_thumbprint($fullcert) :string
    {
        // First, strip out only the right section
        $result = openssl_x509_export($fullcert, $pem);

        // Then calculate sha1 of base64 decoded cert
        $pem = preg_replace('/\-+BEGIN CERTIFICATE\-+/', '', $pem);
        $pem = preg_replace('/\-+END CERTIFICATE\-+/', '', $pem);
        $pem = trim($pem);
        $pem = str_replace(["\n\r", "\n", "\r"], '', $pem);
        $bin = base64_decode($pem);
        return sha1($bin);
    }

    public function loadPem(string $pemfilename) :void
    {
        if (!file_exists($pemfilename)) {
            throw new InvalidArgumentException('PEM file does not exist');
        }
        $fp = fopen($pemfilename, "r");
        $this->pemdata = fread($fp, 8192);
        fclose($fp);
    }
}

