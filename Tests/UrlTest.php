<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bajdzis\System\Url;

final class UrlTest extends TestCase
{
    public function testUrlHavePropertyProtocol()
    {
        $httpUrl = new Url('http://my-domain.com/');
        $secureUrl = new Url('https://my-secure-domain.com/');

        $this->assertSame($httpUrl->getProtocol(), 'http');
        $this->assertSame($secureUrl->getProtocol(), 'https');
    }

    public function testUrlHavePropertyDomain()
    {
        $url = new Url('http://example.com/');

        $this->assertSame($url->getDomain(), 'example.com');
        $this->assertSame($url->getSubDomain(), null);
    }

    public function testUrlHavePropertySubDomain()
    {
        $url = new Url('https://sub.domain.com/');

        $this->assertSame($url->getDomain(), 'domain.com');
        $this->assertSame($url->getSubDomain(), 'sub');
    }

    public function testUrlHavePropertyParams()
    {
        $urlWithoutSlash = new Url('https://sub.domain.com/not/have/slash');
        $urlWithSlash = new Url('https://sub.domain.com/i/have/slash/');

        $this->assertSame($urlWithoutSlash->getParams(), ['not', 'have', 'slash']);
        $this->assertSame($urlWithSlash->getParams(), ['i', 'have', 'slash']);
    }

    public function testUrlParamsMustUseUrlDecode()
    {
        $url = new Url('https://domain.com/slash%2Fcode/this+is+space/');

        $this->assertSame($url->getParams(), ['slash/code', 'this is space']);
    }

    public function testUrlParamsMustUseUrlEncode()
    {
        $url = new Url('https://domain.com/');
        $url->setParams(['slash/code', 'this is space']);

        $this->assertSame($url->getParams(), ['slash/code', 'this is space']);
        $this->assertSame($url->getLink(), '//domain.com/slash%2Fcode/this+is+space/');
    }

    public function testUrlGetLinkAndUrl()
    {
        $url = new Url('https://domain.com/param1/');

        $this->assertSame($url->getLink(), '//domain.com/param1/');
        $this->assertSame($url->getUrl(), 'https://domain.com/param1/');
    }

    public function testUrlNeutralizeDuplicateContent()
    {
        $url = new Url('https://domain.com/param1/');
        $sameUrl = new Url('http://www.domain.com/param1');

        $this->assertSame($url->getLink(), $sameUrl->getLink());
    }

    public function testUrlDomainWithoutSlashIsValidUrl()
    {
        $url = new Url('http://domain.com');

        $this->assertSame($url->getDomain(), 'domain.com');
    }

    public function testCreateEmptyUrl()
    {
        $url = new Url();
        
        $this->assertSame($url->getProtocol(), null);
        $this->assertSame($url->getDomain(), null);
        $this->assertSame($url->getSubDomain(), null);
        $this->assertSame($url->getParams(), null);
    }

    public function testCreateCurrentUrl()
    {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['HTTP_HOST'] = 'en.example.com';
        $_SERVER['REQUEST_URI'] = '/blog/some-title/';

        $url = Url::getCurrentUrl();

        $this->assertSame($url->getProtocol(), 'http');
        $this->assertSame($url->getDomain(), 'example.com');
        $this->assertSame($url->getSubDomain(), 'en');
        $this->assertSame($url->getParams(), ['blog', 'some-title']);
    }

}
