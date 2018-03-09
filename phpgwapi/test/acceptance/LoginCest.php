<?php

	class LoginCest
	{
		public function _before(AcceptanceTester $I)
		{
		}

		public function _after(AcceptanceTester $I)
		{
		}

		public function frontPageWorks(AcceptanceTester $I)
		{
			$I->amOnPage('/');
			$I->see('PORTICO ESTATE LOGIN');
		}

		public function loginWorks(AcceptanceTester $I)
		{
			$I->dontSee('Logg ut');
			$I->login();
			$I->see('Logg ut');
		}
	}
