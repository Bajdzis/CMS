<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bajdzis\System\Routing;
use Bajdzis\System\RoutingAction;
use Bajdzis\System\Uri;

$_SERVER['REQUEST_SCHEME'] = 'http';
$_SERVER['HTTP_HOST'] = 'example.com';
$_SERVER['REQUEST_URI'] = '/blog/some-title/';

/**
 * HELPER CLASSES
 * 
 */

class addBlogStringToTextField extends RoutingAction
{
    public function execute()
    {
        RoutingTest::$text .= "Blog";
        return RoutingAction::CONTINUE_WORK;
    }
}

class addHomepageStringToTextField extends RoutingAction
{
    public function execute()
    {
        RoutingTest::$text .= "Homepage";
        return RoutingAction::CONTINUE_WORK;
    }
}

class addJoinParamsToTextField extends RoutingAction
{
    public function execute()
    {
        RoutingTest::$text .= implode('', $this->relativeParams);
        return RoutingAction::CONTINUE_WORK;
    }
}

class returnFalse extends RoutingAction
{
    public function execute()
    {
        return RoutingAction::CONTINUE_WORK;
    }
}

class returnTrue extends RoutingAction
{
    public function execute()
    {
        return RoutingAction::DONE_WORK;
    }
}

/**
 * TEST CLASS
 * 
 */
final class RoutingTest extends TestCase
{
    static $text;

    public function testRoutingOrder()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl();
        $routing = new Routing($uri);

        $routing->addPath('/', '\addBlogStringToTextField');
        $routing->addPath('/', '\addHomepageStringToTextField');

        $this->assertSame(RoutingTest::$text, '');
        $routing->execute();
        $this->assertSame(RoutingTest::$text, 'BlogHomepage');
    }

    public function testRoutingBreakIfFunctionReturnTrue()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl();
        $routing = new Routing($uri);

        $routing->addPath('/', '\addBlogStringToTextField');
        $routing->addPath('/', '\returnFalse');
        $routing->addPath('/', '\addBlogStringToTextField');
        $routing->addPath('/', '\returnTrue');
        $routing->addPath('/', '\addHomepageStringToTextField');

        $routing->execute();
        $this->assertSame(RoutingTest::$text, 'BlogBlog');
    }

    public function testRoutingSetRelativeParams()
    {
        RoutingTest::$text = "";
        $uri = $this->prepareUrl(['blog', 'some-title']);
        $routing = new Routing($uri);

        $routing->addPath('/', '\addJoinParamsToTextField');
        $routing->addPath('/blog/', '\addJoinParamsToTextField');
        $routing->addPath('/not-execute-this/', '\addJoinParamsToTextField');
        $routing->addPath('/not/execute/this/', '\addJoinParamsToTextField');

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
