<?php
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
	class phpgwapi_country
	{

		/**
		* @var array $continent_array a list of continents
		*/
		public static $continent_array = array
		(
			'africa'		=> 'Africa',
			'antarctica'	=> 'Antarctica',
			'asia'			=> 'Asia',
			'australia'		=> 'Australia',
			'europe'		=> 'Europe',
			'northamerica'	=> 'North America',
			'southamerica'	=> 'South America'
		);

		/**
		* @var array $country_array list of ISO 3166 country codes
		*/
		public static $country_array = array
		(
			'AF'	=> 'AFGHANISTAN',
			'AL'	=> 'ALBANIA',
			'DZ'	=> 'ALGERIA',
			'AS'	=> 'AMERICAN SAMOA',
			'AD'	=> 'ANDORRA',
			'AO'	=> 'ANGOLA',
			'AI'	=> 'ANGUILLA',
			'AQ'	=> 'ANTARCTICA',
			'AG'	=> 'ANTIGUA AND BARBUDA',
			'AR'	=> 'ARGENTINA',
			'AM'	=> 'ARMENIA',
			'AW'	=> 'ARUBA',
			'AU'	=> 'AUSTRALIA',
			'AT'	=> 'AUSTRIA',
			'AZ'	=> 'AZERBAIJAN',
			'BS'	=> 'BAHAMAS',
			'BH'	=> 'BAHRAIN',
			'BD'	=> 'BANGLADESH',
			'BB'	=> 'BARBADOS',
			'BY'	=> 'BELARUS',
			'BE'	=> 'BELGIUM',
			'BZ'	=> 'BELIZE',
			'BJ'	=> 'BENIN',
			'BM'	=> 'BERMUDA',
			'BT'	=> 'BHUTAN',
			'BO'	=> 'BOLIVIA',
			'BA'	=> 'BOSNIA AND HERZEGOVINA',
			'BW'	=> 'BOTSWANA',
			'BV'	=> 'BOUVET ISLAND',
			'BR'	=> 'BRAZIL',
			'IO'	=> 'BRITISH INDIAN OCEAN TERRITORY',
			'BN'	=> 'BRUNEI DARUSSALAM',
			'BG'	=> 'BULGARIA',
			'BF'	=> 'BURKINA FASO',
			'BI'	=> 'BURUNDI',
			'KH'	=> 'CAMBODIA',
			'CM'	=> 'CAMEROON',
			'CA'	=> 'CANADA',
			'CV'	=> 'CAPE VERDE',
			'KY'	=> 'CAYMAN ISLANDS',
			'CF'	=> 'CENTRAL AFRICAN REPUBLIC',
			'TD'	=> 'CHAD',
			'CL'	=> 'CHILE',
			'CN'	=> 'CHINA',
			'CX'	=> 'CHRISTMAS ISLAND',
			'CC'	=> 'COCOS (KEELING) ISLANDS',
			'CO'	=> 'COLOMBIA',
			'KM'	=> 'COMOROS',
			'CG'	=> 'CONGO',
			'CD'	=> 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
			'CK'	=> 'COOK ISLANDS',
			'CR'	=> 'COSTA RICA',
			'CI'	=> 'COTE D\'IVOIRE',
			'HR'	=> 'CROATIA',
			'CU'	=> 'CUBA',
			'CY'	=> 'CYPRUS',
			'CZ'	=> 'CZECH REPUBLIC',
			'DK'	=> 'DENMARK',
			'DJ'	=> 'DJIBOUTI',
			'DM'	=> 'DOMINICA',
			'DO'	=> 'DOMINICAN REPUBLIC',
			'EC'	=> 'ECUADOR',
			'EG'	=> 'EGYPT',
			'SV'	=> 'EL SALVADOR',
			'GQ'	=> 'EQUATORIAL GUINEA',
			'ER'	=> 'ERITREA',
			'EE'	=> 'ESTONIA',
			'ET'	=> 'ETHIOPIA',
			'FK'	=> 'FALKLAND ISLANDS (MALVINAS)',
			'FO'	=> 'FAROE ISLANDS',
			'FJ'	=> 'FIJI',
			'FI'	=> 'FINLAND',
			'FR'	=> 'FRANCE',
			'GF'	=> 'FRENCH GUIANA',
			'PF'	=> 'FRENCH POLYNESIA',
			'TF'	=> 'FRENCH SOUTHERN TERRITORIES',
			'GA'	=> 'GABON',
			'GM'	=> 'GAMBIA',
			'GE'	=> 'GEORGIA',
			'DE'	=> 'GERMANY',
			'GH'	=> 'GHANA',
			'GI'	=> 'GIBRALTAR',
			'GR'	=> 'GREECE',
			'GL'	=> 'GREENLAND',
			'GD'	=> 'GRENADA',
			'GP'	=> 'GUADELOUPE',
			'GU'	=> 'GUAM',
			'GT'	=> 'GUATEMALA',
			'GG'	=> 'GUERNSEY',
			'GW'	=> 'GUINEA-BISSAU',
			'GN'	=> 'GUINEA',
			'GY'	=> 'GUYANA',
			'HT'	=> 'HAITI',
			'HM'	=> 'HEARD ISLAND AND MCDONALD ISLANDS',
			'VA'	=> 'HOLY SEE (VATICAN CITY STATE)',
			'HN'	=> 'HONDURAS',
			'HK'	=> 'HONG KONG',
			'HU'	=> 'HUNGARY',
			'IS'	=> 'ICELAND',
			'IN'	=> 'INDIA',
			'ID'	=> 'INDONESIA',
			'IR'	=> 'IRAN, ISLAMIC REPUBLIC OF',
			'IQ'	=> 'IRAQ',
			'IE'	=> 'IRELAND',
			'IM'	=> 'ISLE OF MAN',
			'IL'	=> 'ISRAEL',
			'IT'	=> 'ITALY',
			'JM'	=> 'JAMAICA',
			'JP'	=> 'JAPAN',
			'JE'	=> 'JERSEY',
			'JO'	=> 'JORDAN',
			'KZ'	=> 'KAZAKHSTAN',
			'KE'	=> 'KENYA',
			'KI'	=> 'KIRIBATI',
			'KP'	=> 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF',
			'KR'	=> 'KOREA, REPUBLIC OF',
			'KW'	=> 'KUWAIT',
			'KG'	=> 'KYRGYZSTAN',
			'AX'	=> 'ALAND ISLANDS',
			'LA'	=> 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',
			'LV'	=> 'LATVIA',
			'LB'	=> 'LEBANON',
			'LS'	=> 'LESOTHO',
			'LR'	=> 'LIBERIA',
			'LY'	=> 'LIBYAN ARAB JAMAHIRIYA',
			'LI'	=> 'LIECHTENSTEIN',
			'LT'	=> 'LITHUANIA',
			'LU'	=> 'LUXEMBOURG',
			'MO'	=> 'MACAO',
			'MK'	=> 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
			'MG'	=> 'MADAGASCAR',
			'MW'	=> 'MALAWI',
			'MY'	=> 'MALAYSIA',
			'MV'	=> 'MALDIVES',
			'ML'	=> 'MALI',
			'MT'	=> 'MALTA',
			'MH'	=> 'MARSHALL ISLANDS',
			'MQ'	=> 'MARTINIQUE',
			'MR'	=> 'MAURITANIA',
			'MU'	=> 'MAURITIUS',
			'YT'	=> 'MAYOTTE',
			'MX'	=> 'MEXICO',
			'FM'	=> 'MICRONESIA, FEDERATED STATES OF',
			'MD'	=> 'MOLDOVA, REPUBLIC OF',
			'MC'	=> 'MONACO',
			'MN'	=> 'MONGOLIA',
			'ME'	=> 'MONTENEGRO',
			'MS'	=> 'MONTSERRAT',
			'MA'	=> 'MOROCCO',
			'MZ'	=> 'MOZAMBIQUE',
			'MM'	=> 'MYANMAR',
			'NA'	=> 'NAMIBIA',
			'NR'	=> 'NAURU',
			'NP'	=> 'NEPAL',
			'AN'	=> 'NETHERLANDS ANTILLES',
			'NL'	=> 'NETHERLANDS',
			'NC'	=> 'NEW CALEDONIA',
			'NZ'	=> 'NEW ZEALAND',
			'NI'	=> 'NICARAGUA',
			'NG'	=> 'NIGERIA',
			'NE'	=> 'NIGER',
			'NU'	=> 'NIUE',
			'NF'	=> 'NORFOLK ISLAND',
			'MP'	=> 'NORTHERN MARIANA ISLANDS',
			'NO'	=> 'NORWAY',
			'OM'	=> 'OMAN',
			'PK'	=> 'PAKISTAN',
			'PW'	=> 'PALAU',
			'PS'	=> 'PALESTINIAN TERRITORY, OCCUPIED',
			'PA'	=> 'PANAMA',
			'PG'	=> 'PAPUA NEW GUINEA',
			'PY'	=> 'PARAGUAY',
			'PE'	=> 'PERU',
			'PH'	=> 'PHILIPPINES',
			'PN'	=> 'PITCAIRN',
			'PL'	=> 'POLAND',
			'PT'	=> 'PORTUGAL',
			'PR'	=> 'PUERTO RICO',
			'QA'	=> 'QATAR',
			'RE'	=> 'REUNION',
			'RO'	=> 'ROMANIA',
			'RU'	=> 'RUSSIAN FEDERATION',
			'RW'	=> 'RWANDA',
			'SH'	=> 'SAINT HELENA',
			'KN'	=> 'SAINT KITTS AND NEVIS',
			'LC'	=> 'SAINT LUCIA',
			'PM'	=> 'SAINT PIERRE AND MIQUELON',
			'VC'	=> 'SAINT VINCENT AND THE GRENADINES',
			'WS'	=> 'SAMOA',
			'SM'	=> 'SAN MARINO',
			'ST'	=> 'SAO TOME AND PRINCIPE',
			'SA'	=> 'SAUDI ARABIA',
			'SN'	=> 'SENEGAL',
			'RS'	=> 'SERBIA',
			'SC'	=> 'SEYCHELLES',
			'SL'	=> 'SIERRA LEONE',
			'SG'	=> 'SINGAPORE',
			'SK'	=> 'SLOVAKIA',
			'SI'	=> 'SLOVENIA',
			'SB'	=> 'SOLOMON ISLANDS',
			'SO'	=> 'SOMALIA',
			'ZA'	=> 'SOUTH AFRICA',
			'GS'	=> 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
			'ES'	=> 'SPAIN',
			'LK'	=> 'SRI LANKA',
			'SD'	=> 'SUDAN',
			'SR'	=> 'SURINAME',
			'SJ'	=> 'SVALBARD AND JAN MAYEN',
			'SZ'	=> 'SWAZILAND',
			'SE'	=> 'SWEDEN',
			'CH'	=> 'SWITZERLAND',
			'SY'	=> 'SYRIAN ARAB REPUBLIC',
			'TW'	=> 'TAIWAN, PROVINCE OF CHINA',
			'TJ'	=> 'TAJIKISTAN',
			'TZ'	=> 'TANZANIA, UNITED REPUBLIC OF',
			'TH'	=> 'THAILAND',
			'TL'	=> 'TIMOR-LESTE',
			'TG'	=> 'TOGO',
			'TK'	=> 'TOKELAU',
			'TO'	=> 'TONGA',
			'TT'	=> 'TRINIDAD AND TOBAGO',
			'TN'	=> 'TUNISIA',
			'TR'	=> 'TURKEY',
			'TM'	=> 'TURKMENISTAN',
			'TC'	=> 'TURKS AND CAICOS ISLANDS',
			'TV'	=> 'TUVALU',
			'UG'	=> 'UGANDA',
			'UA'	=> 'UKRAINE',
			'AE'	=> 'UNITED ARAB EMIRATES',
			'GB'	=> 'UNITED KINGDOM',
			'UM'	=> 'UNITED STATES MINOR OUTLYING ISLANDS',
			'US'	=> 'UNITED STATES',
			'UY'	=> 'URUGUAY',
			'UZ'	=> 'UZBEKISTAN',
			'VU'	=> 'VANUATU',
			'VE'	=> 'VENEZUELA',
			'VN'	=> 'VIET NAM',
			'VG'	=> 'VIRGIN ISLANDS, BRITISH',
			'VI'	=> 'VIRGIN ISLANDS, U.S.',
			'WF'	=> 'WALLIS AND FUTUNA',
			'EH'	=> 'WESTERN SAHARA',
			'YE'	=> 'YEMEN',
			'ZM'	=> 'ZAMBIA',
			'ZW'	=> 'ZIMBABWE',
		);

		/**
		* Translate the list of countries
		*
		* @return array list of translated country names iso_code => (translated) official name
		*/
		public static function get_translated_list()
		{
			static $translated_list = null;
			if ( is_null($translated_list) )
			{
				 $translated_list = array();
				 foreach ( phpgwapi_country::$country_array as $code => $name )
				 {
				 	$translated = lang($name);
					if ( $translated == "{$name}*" )
					{
						$translated = $name;
					}
					$translated_list[$code] = ltrim($translated,'!');
				 }
				 asort($translated_list);
			}
			return $translated_list;
		}

		/**
		* Get the name of a country from the 2 letter iso 3166 country code
		*
		* @param string $code the 2 letter iso 3166 code
		* @return string the country name, empty string if invalid
		*/
		function get_full_name($selected)
		{
			if ( isset(self::$country_array[$selected]) )
			{
				return(self::$country_array[$selected]);
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

			foreach(self::continent_array as $cname => $ctitle)
			{
				$carray[] = array
				(
					'continent_name' 	=> $cname,
					'continent_title'	=> lang($ctitle),
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

			foreach(self::$country_array as $ccode => $cname)
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
?>
