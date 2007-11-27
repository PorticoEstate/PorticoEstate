<?php
	/**************************************************************************\
	* phpGroupWare - News                                                      *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.boacl.inc.php 17992 2007-02-24 20:42:12Z sigurdne $ */

	/**
	* Access Controls class
	*
	* @package news_admin
	*/
	class boacl
	{
		/**
		* @var bool $debug enable debugging in this class
		*/
		var $debug = false;

		/**
		* @var array list of permissions for the current user
		* @access private
		*/
		var $_permissions;

		/**
		* @constructor
		*/
		function boacl()
		{
			$this->_permissions = $this->_get_permissions();
		}

		/**
		* Checks if the user has the required rights level
		*
		* @param int $cat the category to be checked
		* @param int $right the rights level the user must have
		* @return bool does the user have the required rights level
		*/
		function is_permitted($cat_id, $right)
		{
			if ( isset($this->_permissions["L{$cat_id}"]) )
			{
				return !! ($this->_permissions['L'.$cat_id] & $right);
			}
			return false;
		}

		/**
		* Checks to see if the user has read access to a category
		*
		* @param int $cat_id the category to check
		* @return bool does the user have read access?
		*/
		function is_readable($cat_id)
		{
			return $this->is_permitted($cat_id,PHPGW_ACL_READ);
		}

		/**
		* Checks to see if the user has write access to a category
		*
		* @param int $cat_id the category to check
		* @return bool does the user have write access?
		*/
		function is_writeable($cat_id)
		{
			return $this->is_permitted($cat_id,PHPGW_ACL_ADD);
		}

		/**
		* Get the access permissions for current user
		*
		* @access private
		* @internal /This could probably be improved, I am open to suggestions, I just hate mod'ing the API ACL class - skwashd 200608
		*/
		function _get_permissions()
		{
			$rights = array();
			$read_rights = $GLOBALS['phpgw']->acl->get_location_list('news_admin', PHPGW_ACL_READ); 
			$add_rights = $GLOBALS['phpgw']->acl->get_location_list('news_admin', PHPGW_ACL_ADD); 

			foreach ( $read_rights as $loc )
			{
				$rights[$loc] = PHPGW_ACL_READ;
			}

			if(is_array($add_rights))
			{
				foreach ( $add_rights as $loc )
				{
					if ( !isset($rights) )
					{
						$rights[$loc] = PHPGW_ACL_ADD;
					}
					else
					{
					$rights[$loc] |= PHPGW_ACL_ADD;
					}
				}
			}
			return $rights;
		}
	}
?>
