<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bajdzis\System\Routing;
use Bajdzis\System\Uri;

$_SERVER['REQUEST_SCHEME'] = 'http';
$_SERVER['HTTP_HOST'] = 'example.com';
$_SERVER['REQUEST_URI'] = '/blog/some-title/';

final class RoutingTest extends TestCase
{
    static $text;

    public static function addBlogStringToTextField()
    {
        RoutingTest::$text .= "Blog";
        return false;
    }

    public static function addHomepageStringToTextField()
    {
        RoutingTest::$text .= "Homepage";
        return false;
    }

    public static function addJoinParamsToTextField($params)
    {
        RoutingTest::$text .= implode('', $params);
        return false;
    }

    public static function returnFalse()
    {
        return false;
    }

    public static function returnTrue()
    {
        return true;
    }

    public function testRoutingOrder()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl();
        $routing = new Routing($uri);

        $routing->addPath('/', 'RoutingTest::addBlogStringToTextField');
        $routing->addPath('/', 'RoutingTest::addHomepageStringToTextField');

        $this->assertSame(RoutingTest::$text, '');
        $routing->execute();
        $this->assertSame(RoutingTest::$text, 'BlogHomepage');
    }

    public function testRoutingBreakIfFunctionReturnTrue()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl();
        $routing = new Routing($uri);

        $routing->addPath('/', 'RoutingTest::addBlogStringToTextField');
        $routing->addPath('/', 'RoutingTest::returnFalse');
        $routing->addPath('/', 'RoutingTest::addBlogStringToTextField');
        $routing->addPath('/', 'RoutingTest::returnTrue');
        $routing->addPath('/', 'RoutingTest::addHomepageStringToTextField');

        $routing->execute();
        $this->assertSame(RoutingTest::$text, 'BlogBlog');
    }

    public function testRoutingSetRelativeParams()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl(['blog', 'some-title']);
        $routing = new Routing($uri);

        $routing->addPath('/', 'RoutingTest::addJoinParamsToTextField');
        $routing->addPath('/blog/', 'RoutingTest::addJoinParamsToTextField');
        $routing->addPath('/not-execute-this/', 'RoutingTest::addJoinParamsToTextField');
        $routing->addPath('/not/execute/this/', 'RoutingTest::addJoinParamsToTextField');

        $routing->execute();
        $this->assertSame(RoutingTest::$text, 'blogsome-titlesome-title');
    }

    public function prepareUrl($params = [])
    {
        $uri = new Uri();
        $uri->setScheme($_SERVER['REQUEST_SCHEME']);
        $uri->setHost($_SERVER['HTTP_HOST']);
        $uri->setParams($params);
        
        return $uri;
    }
}
