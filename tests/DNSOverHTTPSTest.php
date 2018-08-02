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
}
