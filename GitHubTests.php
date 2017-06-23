<?php

require __DIR__ . '/vendor/autoload.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Class GitHubTests
 */
class GitHubTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    /**
     *
     */
    public function setUp()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        $caps = array(
            "browser" => "Chrome",
            "os" => "Windows",
            "os_version" => "10",
            "resolution" => "1024x768",
            "build" => "First build"
        );

        $browserStackUsername = getenv('BROWSER_STACK_USERNAME');
        echo "browserStackUsername: $browserStackUsername" . PHP_EOL;

        $browserAccessKey = getenv('BROWSER_STACK_ACCESS_KEY');
        echo "browserAccessKey: $browserAccessKey" . PHP_EOL;

        $this->webDriver = RemoteWebDriver::create("https://$browserStackUsername:$browserAccessKey@hub-cloud.browserstack.com/wd/hub", $caps);
    }

    /**
     *
     */
    public function tearDown()
    {
        try {
            $this->webDriver->quit();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * @var string
     */
    protected $url = 'https://github.com';

    /**
     *
     */
    public function testGitHubHome()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('GitHub', $this->webDriver->getTitle());
    }
}
