<?php

	class PropertyMessagesCest
	{
		private $messageListPage = '/?menuaction=property.uitts.index';
		private $priorityNames = [
			'.priority1',
			'.priority2',
			'.priority3'
		];

		public function _before(AcceptanceTester $I)
		{
			$I->login();
			$I->amOnPage($this->messageListPage);
		}

		public function _after(AcceptanceTester $I)
		{
		}

		public function prioritizedMessageHasDifferentColor(AcceptanceTester $I)
		{
			$I->waitForElement($this->priorityNames[0], 5);

			$priorityColors = [];

			foreach ($this->priorityNames as $priority) {
				$priorityColors[] = $I->getCSSValue($priority, 'background-color');
			}

			$I->assertNotSame($priorityColors[0], $priorityColors[1]);
			$I->assertNotSame($priorityColors[1], $priorityColors[2]);
		}

		public function messageChangesColorOnSelection(AcceptanceTester $I)
		{
			$I->waitForElement($this->priorityNames[0], 5);

			foreach ($this->priorityNames as $priority) {
				$priorityColorBefore = $I->getCSSValue($priority, 'background-color');

				$I->click($priority);

				$priorityColorAfter = $I->getCSSValue($priority, 'background-color');

				$I->assertNotSame($priorityColorBefore, $priorityColorAfter);
			}
		}

		public function messageRegainOriginalColorOnDeselect(AcceptanceTester $I)
		{
			$I->waitForElement($this->priorityNames[0], 5);

			foreach ($this->priorityNames as $priority) {
				$priorityColorBefore = $I->getCSSValue($priority, 'background-color');

				$I->click($priority);
				$I->click($priority);
				// Click somewhere outside table to not see the :hover color on a row
				$I->click('#top');

				$priorityColorAfter = $I->getCSSValue($priority, 'background-color');

				$I->assertSame($priorityColorBefore, $priorityColorAfter);
			}
		}

	}
