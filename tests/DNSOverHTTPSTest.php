<?php

namespace Elphin\PHPCertificateToolbox;

use Elphin\PHPCertificateToolbox\DNSValidator\DNSOverHTTPS;
use Elphin\PHPCertificateToolbox\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * This is an integration test with external dependencies and will be excluded from the usual
 * continuous integration tests
 *
 * @group integration
 */
class DNSOverHTTPSTest extends TestCase
{
    public function testGetGoogle()
    {
        $client = new DNSOverHTTPS(DNSOverHTTPS::DNS_GOOGLE);
        $output = $client->getDNS('example.com', 1);
        $this->assertEquals(0, $client->formatResponse($output)->Status);
    }

    public function testGetMozilla()
    {
        $client = new DNSOverHTTPS(DNSOverHTTPS::DNS_MOZILLA);
        $output = $client->getDNS('example.com', 1);
        $this->assertEquals(0, $client->formatResponse($output)->Status);
    }

    public function testGetCloudflare()
    {
        $client = new DNSOverHTTPS(DNSOverHTTPS::DNS_CLOUDFLARE);
        $output = $client->getDNS('example.com', 1);
        $this->assertEquals(0, $client->formatResponse($output)->Status);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFakeDoHServer()
    {
        $client = new DNSOverHTTPS('example.com/example');
        $client->checkChallenge('example.com', '');
    }

    /**
     * Domains that are unicode need to be converted to IDN before submission to the DoH
     * provider. PHP has a "built-in" function for this idn_to_ascii(), on Ubuntu this
     * is packaged in "php-intl".
     * Notes: Without converting to the IDN standard the response from Google will return
     * NXDOMAIN & Cloudflare will return a 200 response with a blank page causing an
     * exception from json_decode().
     */
    public function testUnicodeDomains()
    {
        $client = new DNSOverHTTPS(DNSOverHTTPS::DNS_GOOGLE);

        $nonIDNFormat = $client->getDNS('科技部.中国', 1);
        $this->assertEquals(3, $client->formatResponse($nonIDNFormat)->Status); //NXDOMAIN

        $IDNFormat = $client->getDNS('xn--fiq53l90e917afrv.xn--fiqs8s', 1);
        $this->assertEquals(0, $client->formatResponse($IDNFormat)->Status);    //NOERROR

        if (function_exists('idn_to_ascii')) {
            $IDNConvert = $client->getDNS(idn_to_ascii('科技部.中国'), 1);
            $this->assertEquals(0, $client->formatResponse($IDNConvert)->Status);
        }

        $cloud = $client->getDNS('科技部.中国', 1, DNSOverHTTPS::DNS_CLOUDFLARE);
        $this->expectException(RuntimeException::class);
        $client->formatResponse($cloud);
    }
}
