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

		public function __construct( $options = array(), $messages = array() )
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
		protected function configure( $options = array(), $messages = array() )
		{
			parent::configure($options, $messages);
			// also accept 5 digits even if its not a valid organization number
			$this->setOption('pattern', '/(^\d{9}$)|(^\d{6}$)|(^\d{5}$)/');
		}


		/**
		 * @see sfValidatorString
		 */
		protected function doClean( $value )
		{
			$clean = parent::doClean($value);

			if ($clean && strlen($clean) == 9 && !$this->mod11OfNumberWithControlDigit($clean))
			{
				throw new sfValidatorError($this, 'invalid', array('value' => $value));
			}

			return $clean;
		}


		private function mod11OfNumberWithControlDigit( $input )
		{

			if($input == '000000000')
			{
				return true;
			}

			/**
			 * https://web.archive.org/web/20171204223816/https://www.brreg.no/om-oss-nn/oppgavene-vare/registera-vare/om-einingsregisteret/organisasjonsnummeret/
			 */

			$controlNumber = 2;
			$sumForMod = 0;

			$arr = str_split($input);

			$length = strlen($input);

			for ($i = $length - 2; $i >= 0; --$i)
			{
				$sumForMod += $arr[$i] * $controlNumber;
				if (++$controlNumber > 7)
				{
					$controlNumber = 2;
				}
			}
			$_result = (11 - ($sumForMod % 11));
			$result = $_result === 11 ? 0 : $_result;
			$control_digit = end($arr);
			$ret = $result == $control_digit ? true : false;
			return $ret;
		}

	}