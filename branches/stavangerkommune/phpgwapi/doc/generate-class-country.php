#!/usr/bin/php
<?php 
	/**
	* @ignore
	*/

	/*
	* Generate a new version of phpgwapi/inc/class.country.inc.php
	*
	* Usage:
	* $ /path/to/phpgroupware/phpgwapi/doc/generate-class-country.php > /path/to/phpgroupware/phpgwapi/inc/class.country.inc.php
	*
	* @auhtor Dave Hall skwashd at phpGroupWare.org
	* @copyright Portions Copyright (C) 2006 Free Software Foundation http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	*/
	echo"<?php\n";
?>
	/**
	* Countries
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @auhtor Dave Hall skwashd at phpGroupWare.org
	* @author Bettine Gille ceb at phpGroupWare.org
	* @copyright Copyright (C) 2001 - 2006 Free Software Foundation http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @interal do not edit, edit and run phpgwapi/doc/generate-class-country.php to regenerate this class
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id$
	*/

	/**
	* Countries
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class country
	{

		/**
		* @var array $continent_array a list of continents
		*/
		var $continent_array = array();
<?php
	// Do not change this, it ensures we are using utf-8 :)
	setlocale(LC_ALL, 'en_US.UTF-8');

	$raw_file = file_get_contents('http://www.iso.ch/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1-semic.txt');
	$cc_list = explode("\r\n", $raw_file);
	if ( count($cc_list) < 3 )
	{
		echo "Invalid country code list, exiting\n";
		exit;
	}
	unset($cc_list[0]);

	echo "\n\t\t/**\n\t\t* @var array \$country_array list of ISO 3166 country codes\n\t\t*/\n\t\tvar \$country_array = array\n\t\t(\n\t\t\t'  '\t=> 'Select One',\n";
	sort($cc_list, SORT_LOCALE_STRING);
	foreach ( $cc_list as $entry )
	{
		if ( !trim($entry) ) //ignore the empties
		{
			continue;
		}

		$country = explode(';', iconv('ISO-8859-1', 'UTF-8', $entry));
		echo "\t\t\t'" . trim($country[1]) . "'\t=> '" . addslashes(trim($country[0])) . "',\n";
	}
	echo "\t\t);\n\n";
?>
		/**
		* @constructor
		*/
		function country()
		{
			$this->continent_array = array
			(
				'africa'		=> lang('africa'),
				'antarctica'	=> lang('antarctica'),
				'asia'			=> lang('asia'),
				'australia'		=> lang('australia'),
				'europe'		=> lang('europe'),
				'northamerica'	=> lang('northamerica'),
				'southamerica'	=> lang('southamerica')
			);
		}

		/**
		* Create a select box filled with countries
		*
		* @param string $selected the currently selected country
		* @param string $name the name of the select box element in the form, used for both the id and name attributes
		* @return string the html for a select box form element
		*/
		function form_select($selected,$name='')
		{
			if($name=='')
			{
				$name = 'country';
			}
			$str = "<select name=\"$name\" id=\"$name\">\n";
			reset($this->country_array);
			while(list($key,$value) = each($this->country_array))
			{
				$str .= ' <option value="'.$key.'"'.($selected == $key?' selected="selected"':'').'>'.$value.'</option>'."\n";
			}
			$str .= '</select>'."\n";
			return $str;
		}

		/**
		* Get the name of a country from the 2 letter iso 3166 country code
		*
		* @param string $code the 2 letter iso 3166 code
		* @return string the country name, empty string if invalid
		*/
		function get_full_name($selected)
		{
			if ( isset($this->country_array[$selected]) )
			{
				return($this->country_array[$selected]);
			}
			return '';
		}

		/**
		* Create an array of continents suitable for use with the XSLT template engine
		*
		* @param string $selected the currently selected country
		* @param string $select_name the name of the select box element in the form
		* @return array list of continents
		*/
		function xslt_continent_select($selected = '',$select_name='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file('countries');

			foreach($this->continent_array as $cname => $ctitle)
			{
				$carray[] = array
				(
					'continent_name' 	=> $cname,
					'continent_title'	=> $ctitle,
					'selected'			=> $cname == $selected ? 'selected' : ''
				);
			}

			return array
			(
				'select_name'				=> $select_name,
				'continent_list'			=> $carray,
				'lang_continent_statustext'	=> lang('select a continent')
			);
		}

		/**
		* Create an array of countries suitable for use with the XSLT template engine
		*
		* @param string $selected the currently selected country
		* @param string $select_name the name of the select box element in the form
		* @return array list of countries
		*/
		function xslt_country_select($selected = '', $select_name='')
		{
			$GLOBALS['phpgw']->xslttpl->add_file('countries');

			foreach($this->country_array as $ccode => $cname)
			{
				$carray[] = array
				(
					'country_code'	=> $ccode,
					'country_name'	=> lang($cname),
					'selected'		=> $ccode == $selected ? 'selected' : ''
				);
			}

			return array
			(
				'select_name'				=> $select_name,
				'country_list'				=> $carray,
				'lang_country_statustext'	=> lang('select a country')
			);
		}
	}
<?php echo '?>';?>
