<?php

/**
 * sfValidatorEmail validates norwegian style Social Security Numbers
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfValidatorNorwegianSSN extends sfValidatorRegex
{
	public function __construct($options = array(), $messages = array())
	{
		if (!isset($messages['invalid']))
		{
			$messages['invalid'] = '%field% contains an invalid social security number';
		}
		
		parent::__construct($options, $messages);
	}
	
  /**
   * Valid format for a norwegian SSN is DDMMYY\d\d\d\d\d
   *
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {	
    parent::configure($options, $messages);
    $this->setOption('pattern', '/^(0[1-9]|[12]\\d|3[01])([04][1-9]|[15][0-2])\\d{7}$/');
  }
}
