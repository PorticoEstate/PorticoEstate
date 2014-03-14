<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category utilities
 	 * @version $Id: class.wiki2html.inc.php 10127 2012-10-07 17:06:01Z sigurdne $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Document me!
	*
	* @package phpgwapi
	* @subpackage utilities
	*/

	/**
	* Include the Textile class
	* @see wiki2html
	*/

	
	class phpgwapi_wiki2html
	{
		protected $syntax = 'textile';

		public function __construct()
		{

		}

		public function set_syntax($syntax)
		{
			if($syntax)
			{
				$this->syntax = $syntax;
			}
		}

		public function process($content, $lite = '', $encode = '', $noimage = '', $strict = '', $rel = '')
		{
			// Convert the raw content to wiki content
			switch ($this->syntax)
			{
				case 'markdown':
					require_once PHPGW_API_INC . '/wiki2html/markdown/markdown.php';
			        $html = Markdown($content);
					break;
				default:
					require_once PHPGW_API_INC . '/wiki2html/textile/Textile.php';
					$textile = new Textile();
					$html = $textile->TextileThis($content);
			}
			return $html;
		}
	}
