<?php

	class rental_validator
	{

		public static function valid_required( $value, &$message = null )
		{
			if (!isset($value) || $value === '')
			{
				return false;
			}

			return true;
		}

		public static function valid_type( $type, $value, &$message )
		{
			switch ($type)
			{
				case 'longtext':
				case 'text':
				case 'varchar':
					return is_string($value);

				case 'int':
					if (!is_int($value))
					{
						$message = lang('messages_isint');
						return false;
					}
					break;

				case 'char':
					return is_string($value) && (strlen($value) == 1);

				case 'date':
				case 'timestamp':
					if (!is_numeric($value))
					{
						$message = lang('messages_not_valid_date');
						return false;
					}
					break;

				case 'decimal':
				case 'float':
					if (!is_numeric($value))
					{
						$message = lang('messages_isnumeric');
						return false;
					}
					break;

				case 'bool':
					if (!is_bool($value) && !(is_numeric($value) && ($value == 1 || $value == 0)))
					{
						$message = lang('messages_general');
						return false;
					}
					break;

				// Do not check these types
				case 'auto':
				case 'blob':
				default:
					return true;
			}

			return true;
		}

		public static function valid_length( $type, $precision, $value, &$message )
		{
			switch ($type)
			{
				case 'longtext':
				case 'text':
				case 'varchar':
					if (strlen($value) > $precision)
					{
						$message = lang('messages_string_too_long');
						return false;
					}

				case 'int':
					$valid_exponent = 0;

					switch ($precision)
					{
						case 2:
							$exponent = 15;
							return ($value >= pow(-2, 15)) && ($value <= pow(2, 14) - 1);
						case 4:
							$exponent = 31;
							return ($value >= pow(-2, 31)) && ($value <= pow(2, 31) - 1);
						case 8:
							$exponent = 63;
							return ($value >= pow(-2, 63)) && ($value <= pow(2, 63) - 1);
					}

					if (($value < pow(-2, $exponent)) || ($value > pow(2, $exponent) - 1))
					{
						$message = lang('messages_number_out_of_range');
						return false;
					}
					break;

				case 'char':
					return (strlen($value) == 1);

				// Do not check these types
				case 'decimal':
				case 'float':
				case 'bool':
				case 'auto':
				case 'blob':
				case 'date':
				case 'timestamp':
				default:
					return true;
			}

			return true;
		}

		/**
		 * Example: Agresso_ID must be 9 characters long.  This is not an actual rule,
		 * just an example of custom validation from objects.  @see rental_price_item
		 *
		 * @param $value
		 * @param $message
		 * @return unknown_type
		 */
		public static function valid_agresso_id( $value, &$message )
		{
			if (strlen($value) != 9)
			{
				$message = lang('messages_agresso_id_length');
				return false;
			}

			return true;
		}
	}