<?php

use Facebook\WebDriver\WebDriver as WebDriver;

	class PropertyMessagesCest
	{

		private $priorityNames = [
			'priority1',
			'priority2',
			'priority3'
		];

		public function _before(AcceptanceTester $I)
		{
		}

		public function _after(AcceptanceTester $I)
		{
		}

		public function prioritizedMessageHasDifferentColor(AcceptanceTester $I)
		{
			$I->amOnPage('/');
			$I->fillField('login', 'sysadmin');
			$I->fillField('passwd', 'sysadminPW0*');
			$I->click('Login');

			$I->amOnPage('/?menuaction=property.uitts.index');
			$I->waitForElement('.' . $this->priorityNames[0], 5);

			$priorityColors = [];

			foreach ($this->priorityNames as $priority) {
				$priorityColors[] = $I->executeInSelenium(function(Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) use ($priority) {
					return $webdriver->findElement(WebDriverBy::className($priority))->getCSSValue('background-color');
				});
			}

			$I->assertNotSame($priorityColors[0], $priorityColors[1]);
			$I->assertNotSame($priorityColors[1], $priorityColors[2]);
		}

		public function messageChangesColorOnSelection(AcceptanceTester $I)
		{
			$I->amOnPage('/');
			$I->fillField('login', 'sysadmin');
			$I->fillField('passwd', 'sysadminPW0*');
			$I->click('Login');

			$I->amOnPage('/?menuaction=property.uitts.index');
			$I->waitForElement('.' . $this->priorityNames[0], 5);

			foreach ($this->priorityNames as $priority) {
				$priorityColorBefore = $I->executeInSelenium(function(Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) use ($priority) {
					return $webdriver->findElement(WebDriverBy::className($priority))->getCSSValue('background-color');
				});

				$I->click(['class' => $priority]);

				$priorityColorAfter = $I->executeInSelenium(function(Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
					return $webdriver->findElement(WebDriverBy::className('priority1'))->getCSSValue('background-color');
				});

				$I->assertNotSame($priorityColorBefore, $priorityColorAfter);
			}
		}

		public function messageRegainOriginalColorOnDeselect(AcceptanceTester $I)
		{
			$I->amOnPage('/');
			$I->fillField('login', 'sysadmin');
			$I->fillField('passwd', 'sysadminPW0*');
			$I->click('Login');

			$I->amOnPage('/?menuaction=property.uitts.index');
			$I->waitForElement('.' . $this->priorityNames[0], 5);

			foreach ($this->priorityNames as $priority) {
				$priorityColorBefore = $I->executeInSelenium(function(Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) use ($priority) {
					return $webdriver->findElement(WebDriverBy::className($priority))->getCSSValue('background-color');
				});

				$I->click(['class' => $priority]);
				$I->click(['class' => $priority]);
				// Click somewhere outside table to not see the :hover color on a row
				$I->click(['id' => 'top']);

				$priorityColorAfter = $I->executeInSelenium(function(Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) use ($priority) {
					return $webdriver->findElement(WebDriverBy::className($priority))->getCSSValue('background-color');
				});

				$I->assertSame($priorityColorBefore, $priorityColorAfter);
			}
		}

	}
