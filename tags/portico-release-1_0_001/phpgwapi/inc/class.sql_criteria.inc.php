<?php
	/**
	* SQL Generator Criteria - help to create criterias for common queries
	* @author Edgar Antonio Luna Diaz <eald@co.com.mx>
	* @author Alejadro Borges
	* @author Jonathan Alberto Rivera Gomez
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	* @internal Development of this application was funded by http://www.sogrp.com
	* @link http://www.sogrp.com/
	*/

	/**
	* Include SQL class
	*/
	phpgw::import_class('phpgwapi.sql');

	/**
	* SQL Generator Criteria - help to create criterias for common queries
	*
	* This class provide common methods to set, mantain, an retrive the queries 
	* to use in a query (for the where clause).
	* @package phpgwapi
	* @subpackage database
	*/
	class phpgwapi_sql_criteria extends phpgwapi_sql
	{
		/*
		public function __construct()
		{
		}
		*/

		/************************************************************* \
		* Usefull low level functions to create queries logically   *
		\*************************************************************/

		/**
		* Genarete token for equal criteria for sql.
		*
		* @param string $left The left operand of the staement
		* @param string $right The right operand of the statement
		* @return array with an equal criteria tokenized.
		*/
		public static function _equal($left,$right)
		{
			return array($left,$right,'equal');
		}

		/**
		* Generate a token for non equal comparission for sql.
		*
		* @param mixed $left Left operand.
		* @param mixed $right Right operand.
		* @return array token list with elements for `not equal' operation.
		*/
		public static function _not_equal($left, $right)
		{
			return array($left,$right,'not_equal');
		}

		/**
		* Generate token for greater than operator for sql
		*
		* @param mixed $left The left operand of the statement
  		* @param mixed $right The right operand of the statement
		* @return array token list with elements for `greater' operation.
		*/
		public static function _greater($left,$right)
		{
			return array($left,$right,'greater');
		}
		
		/**
		* Generate token for less than operator for sql
		*
		* @param mixed $left The left operand of the statement
		* @param mixed $right The right operand of the statement
		* @return array with `less than' operation tokenized.
		*/
		public static function _less($left,$right)
		{
			return array($left,$right,'less');
		}
		
		/**
		* Generate token for greater than  or equal than operator for sql
		*
		* @param mixed $left The left operand of the statement
  		* @param mixed $right The right operand of the statement
		* @return array token list with elements for `greater or equal than ' operation.
		*/
		public static function _greater_equal($left,$right)
		{
			return array($left,$right,'greater_equal');
		}

		/**
		* Generate token for less thas or equal than operator for sql
		*
		* @param mixed $left The left operand of the statement
  		* @param mixed $right The right operand of the statement
		* @return array token list with elements for `less than or equal than ' operation.
		*/
		public static function _less_equal($left,$right)
		{
			return array($left,$right,'less_equal');
		}

		/**
		* Generate a criteria for search in the content of a field a value for sql.
		*
		* has is a criteria of the form: <code>Field LIKE '%value%'</code>
		* @param mixed $field For search in.
		* @param mixed $value That will search.
		* @return array token list of has criteria
		*/
		public static function token_has($field,$value)
		{
			return array($field,$value,'has');
		}

		/**
		* Generate a criteria to search in the beginning of a field for sql.
		*
		* begin_with is a criteria of the form: <code>Field LIKE 'value%'</code>
		* note the missing % at the begin of value
		* @param mixed $field For search in.
		* @param mixed $value That will search.
		* @return array token list of begin_with criteria
		* @see _has
		*/
		public static function token_begin($field,$value)
		{
			return array($field,$value,'begin_with');
		}

		/**
		* Generate a criteria to search in the ending of a field for sql.
		*
		* end_with is a criteria of the form: <code>Field LIKE '%value'</code>
		* note the missing % at the end of value
		* @param mixed $field For search in.
		* @param mixed $value That will search.
		* @return array token list of end_with criteria
		* @see _has
		*/
		public static function token_end($field,$value) 
		{
			return array($field,$value,'end_with');
		}

		/**
		* Generate an AND conjuction for sql criterias.
		*
		* @param mixed $left Left operand.
		* @param mixed $right Right operand.
		* @return array token list for and_ operand
		* @see and_
		*/
		public static function token_and($left,$right = '')
		{
			return array($left,$right,'and_');
		}

		/**
		* Generate an OR conjuction for sql criterias.
		*
		* @param mixed $left Left operand.
		* @param mixed $right Right operand.
		* @return array token list for or_ operand
		* @see or_
		*/
		public static function token_or($left,$right = '')
		{
			return array($left,$right,'or_');
		}

		/**
		* Generate a is null critieria for sql.
		*
		* @param mixed $data A field name
		* @return array with tokens.
		*/
		public static function _is_null($data)
		{
			return array($data,'is_null');
		}

		/**
		* Generate a `is not null' critieria for sql.
		*
		* @param string $data A field name.
		* @return array with tokens.
		*/
		public static function _not_null($data)
		{
			return array($data,'not_null');
		}

		/**
		* Generate upper function of sql.
		*
		* @param string $data
		* @return array with tokens.
		*/
		public static function _upper($value)
		{
			return array($value,'upper');
		}

		/**
		* Generate lower function of sql.
		*
		* @param string $data
		* @return array with tokens.
		*/
		public static function _lower($value)
		{
			return array($value,'lower');
		}

		/**
		* Generate a IN sql operator
		*
		* @param string $field data with the name of field
		* @param values $values Array with posible values
		* @return string token for IN.
		*/
		public static function _in($field, $values)
		{
			return array($field, $values,'in');
		}

		/**
		* Create a multiple AND conjunction.
		*
		* All and's are in same level.
		* @param mixed $clause tokens with conjuctions.
		* @return string with many and conjuntions at same level.
		*/
		public static function _append_and($clause)
		{
			array_push($clause, 'append_and');
			return $clause;
		}

		/**
		* Create a multiple OR conjunction.
		*
		* All or's are in same level.
		* @param mixed $clause tokens with conjuctions.
		* @return string with many and conjuntions at same level.
		*/
		public static function _append_or($clause)
		{
			array_push($clause, 'append_or');
			return $clause;
		}

		/**
		* Wrapper function to call the right functions
		*
		* This function don't accept tokens in operands.
		* @param string $operator the name of operation that want to apply to operands
		* @param string $operand requiered operand to pass to function.
		* @param string $optional_operand optional operand to pass to function, some operation need it.
		* @return string with and operation created.
		*/
		public static function operate($operator, $operand, $optional_operand = '')
		{
			if (is_array($operand) || $optional_operand == '' || $optional_operand == FALSE)
			{
				return phpgwapi_sql::$operator($operand);
			}
			else
			{
				return phpgwapi_sql::$operator($operand, $optional_operand);
			}
		}

		/**
		* Generate a criteria stirng suitable for SQL queries, update and delete.
		* 
		* @param array $tokens array Multidimensional array created with calls to sql_criteria class methods
		* @return string Return a criteria string based on tokens
		*/
		public static function criteria($tokens)
		{
			$operator = array_pop($tokens);
			if($operator == 'append_or' || $operator == 'append_and')
			{
				return self::$operator($tokens);
			}
			$operand_left = array_shift($tokens);
			// Recursivity if array
			$operand_left = (is_array($operand_left))? self::criteria($operand_left) : $operand_left;
			if(count($tokens) > 0)
			{
				$operand_right = array_shift($tokens);
				// Recursivity if array too
				$operand_right = (is_array($operand_right))? self::criteria($operand_right) : $operand_right;
			}
			else
			{
				$operand_right = phpgwapi_sql::null();
			}
			return self::$operator($operand_left, $operand_right);
		}
	}
