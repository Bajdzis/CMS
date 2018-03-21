<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bajdzis\System\Uri;

final class UriTest extends TestCase
{
    public function testUriHavePropertyProtocol()
    {
        $httpUri = new Uri('http://my-domain.com/');
        $secureUri = new Uri('https://my-secure-domain.com/');

        $this->assertSame($httpUri->getScheme(), 'http');
        $this->assertSame($secureUri->getScheme(), 'https');
    }

    public function testUriHavePropertyDomain()
    {
        $uri = new Uri('http://example.com/');

        $this->assertSame($uri->getDomain(), 'example.com');
        $this->assertSame($uri->getSubDomain(), null);
    }

    public function testUriHavePropertySubDomain()
    {
        $uri = new Uri('https://sub.domain.com/');

        $this->assertSame($uri->getDomain(), 'domain.com');
        $this->assertSame($uri->getSubDomain(), 'sub');
    }

    public function testUriHavePropertyParams()
    {
        $uriWithoutSlash = new Uri('https://sub.domain.com/not/have/slash');
        $uriWithSlash = new Uri('https://sub.domain.com/i/have/slash/');

        $this->assertSame($uriWithoutSlash->getParams(), ['not', 'have', 'slash']);
        $this->assertSame($uriWithSlash->getParams(), ['i', 'have', 'slash']);
    }

    public function testUriParamsMustUseUriDecode()
    {
        $uri = new Uri('https://domain.com/slash%2Fcode/this+is+space/');

        $this->assertSame($uri->getParams(), ['slash/code', 'this is space']);
    }

    public function testUriParamsMustUseUriEncode()
    {
        $uri = new Uri('https://domain.com/');
        $uri->setParams(['slash/code', 'this is space']);

        $this->assertSame($uri->getParams(), ['slash/code', 'this is space']);
        $this->assertSame($uri->getLink(), '//domain.com/slash%2Fcode/this+is+space/');
    }

    public function testUriGetLinkAndUri()
    {
        $uri = new Uri('https://domain.com/param1/');

        $this->assertSame($uri->getLink(), '//domain.com/param1/');
        $this->assertSame($uri->getUri(), 'https://domain.com/param1/');
    }

    public function testUriNeutralizeDuplicateContent()
    {
        $uri = new Uri('https://domain.com/param1/');
        $sameUri = new Uri('http://www.domain.com/param1');

        $this->assertSame($uri->getLink(), $sameUri->getLink());
    }

    public function testUriDomainWithoutSlashIsValidUri()
    {
        $uri = new Uri('http://domain.com');

        $this->assertSame($uri->getDomain(), 'domain.com');
    }

    public function testCreateEmptyUri()
    {
        $uri = new Uri();

        $this->assertSame($uri->getScheme(), null);
        $this->assertSame($uri->getDomain(), null);
        $this->assertSame($uri->getSubDomain(), null);
        $this->assertSame($uri->getParams(), []);
    }

    public function testCreateCurrentUri()
    {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['HTTP_HOST'] = 'en.example.com';
        $_SERVER['REQUEST_URI'] = '/blog/some-title/';

        $uri = Uri::getCurrentUri();

        $this->assertSame($uri->getScheme(), 'http');
        $this->assertSame($uri->getDomain(), 'example.com');
        $this->assertSame($uri->getSubDomain(), 'en');
        $this->assertSame($uri->getParams(), ['blog', 'some-title']);
    }

}
