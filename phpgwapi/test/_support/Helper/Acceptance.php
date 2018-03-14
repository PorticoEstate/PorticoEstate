<?php
namespace Helper;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    /**
     * Locates element matching a CSS selector and returns
     * CSS value on specified property.
     *
     * @param string $css_selector
     * @param string $css_property
     */
    public function getCSSValue($css_selector, $css_property)
    {
        $webdriver = $this->getModule('WebDriver');
        return $webdriver->executeInSelenium(function(RemoteWebDriver $webdriver) use ($css_selector, $css_property) {
            return $webdriver->findElement(WebDriverBy::cssSelector($css_selector))->getCSSValue($css_property);
        });
    }
}
