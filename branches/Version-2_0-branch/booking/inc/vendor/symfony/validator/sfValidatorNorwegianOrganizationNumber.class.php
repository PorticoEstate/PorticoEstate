<?php

/**
 * sfValidatorNorwegianOrganizationNumber validates the basic format of an Norwegian organization number 
 * (see http://www.brreg.no/english/coordination/number.html).
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfValidatorNorwegianOrganizationNumber extends sfValidatorRegex
{
	public function __construct($options = array(), $messages = array())
	{
		if (!isset($messages['invalid']))
		{
			$messages['invalid'] = '%field% contains an invalid organization number';
		}
		
		parent::__construct($options, $messages);
	}
	
  /**
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {	
    parent::configure($options, $messages);
    // also accept 5 digits even if its not a valid organization number
    $this->setOption('pattern', '/(^\d{9}$)|(^\d{6}$)|(^\d{5}$)/');
  }
}
