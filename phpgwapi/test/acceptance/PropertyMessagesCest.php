<?php

	class PropertyMessagesCest
	{

		public function _before(AcceptanceTester $I, \Page\PropertyMessageList $messageListPage)
		{
			$I->login();
			$I->amOnPage($messageListPage::$URL);
			$I->waitForElement($messageListPage::$priorityCSSClass[0], 5);
		}

		public function _after(AcceptanceTester $I)
		{
		}

		public function priorityLevelsDifferInColor(AcceptanceTester $I, \Page\PropertyMessageList $messageListPage)
		{
			$priorityColors = [];

			foreach ($messageListPage::$priorityCSSClass as $priority) {
				$priorityColors[] = $I->getCSSValue($priority, 'background-color');
			}

			$I->assertNotSame($priorityColors[0], $priorityColors[1]);
			$I->assertNotSame($priorityColors[1], $priorityColors[2]);
		}

		public function messageChangesColorOnSelection(AcceptanceTester $I, \Page\PropertyMessageList $messageListPage)
		{

			foreach ($messageListPage::$priorityCSSClass as $priority) {
				$priorityColorBefore = $I->getCSSValue($priority, 'background-color');

				$messageListPage->selectRow($priority);

				$priorityColorAfter = $I->getCSSValue($priority, 'background-color');

				$I->assertNotSame($priorityColorBefore, $priorityColorAfter);
			}
		}

		public function messageRegainOriginalColorOnDeselect(AcceptanceTester $I, \Page\PropertyMessageList $messageListPage)
		{

			foreach ($messageListPage::$priorityCSSClass as $priority) {
				$priorityColorBefore = $I->getCSSValue($priority, 'background-color');

				$messageListPage->selectRow($priority);
				$messageListPage->deSelectRow($priority);
				// Click somewhere outside table to not see the :hover color on a row
				$messageListPage->moveCursor();

				$priorityColorAfter = $I->getCSSValue($priority, 'background-color');

				$I->assertSame($priorityColorBefore, $priorityColorAfter);
			}
		}

	}
