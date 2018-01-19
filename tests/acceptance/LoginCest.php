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
			$I->amOnPage('/');
			$I->fillField('login', 'sysadmin');
			$I->fillField('passwd', 'sysadminPW0*');
			$I->click('Login');
			$I->see('Logg ut');
		}
	}
