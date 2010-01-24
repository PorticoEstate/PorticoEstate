<?php
/*
 * This file holds the English translation of PHP Weather. To use it,
 * just include it in the main phpweather.inc file.
 *
 * Author: Martin Geisler <gimpster@gimpster.com>
 */

	/* Unsets old language variables and loads new ones. */
	if (isset($strings))
	{
		/* The strings array is loaded - assume the same for the rest. */
		unset($strings);
		unset($wind_dir_text_short_array);
		unset($wind_dir_text_array);
		unset($weather_array);
		unset($cloud_condition_array);
	}
	/* Load the new strings */

	$strings = array(
		'no_data'               => '<blockquote><p>Sorry! There is <b>no data</b> available.</p></blockquote>',
		'mm_inches'             => '<b>%s</b> mm (<b>%s</b> inches)',
		'precip_a_trace'        => 'a trace',
		'precip_there_was'      => 'There was %s of precipitation ',
		'sky_str_format1'       => 'The sky was <b>%s</b> at a height of <b>%s</b> meters (<b>%s</b> feet)',
		'sky_str_clear'         => 'The sky was <b>clear</b>',
		'sky_str_format2'       => ', <b>%s</b> at a height of <b>%s</b> meters (<b>%s</b> feet) and <b>%s</b> at a height of <b>%s</b> meters (<b>%s</b> feet)',
		'sky_str_format3'       => ' and <b>%s</b> at a height of <b>%s</b> meters (<b>%s</b> feet)',
		'sky_cavok'             => 'There are no clouds below <b>1,524</b> meters (<b>5,000</b> feet)',
		'clouds'                => ' clouds',
		'clouds_cb'             => ' cumulonimbus clouds',
		'clouds_tcu'            => ' towering cumulus clouds',
		'visibility_format'     => 'The visibility was <b>%s</b> kilometers (<b>%s</b> miles).',
		'wind_str_format1'      => 'blowing at a speed of <b>%s</b> meters per second (<b>%s</b> miles per hour)',
		'wind_str_format2'      => ', with gusts to <b>%s</b> meters per second (<b>%s</b> miles per hour),',
		'wind_str_format3'      => ' from the <b>%s</b>',
		'wind_str_calm'         => '<b>calm</b>',
		'wind_vrb_long'         => 'variable directions',
		'wind_vrb_short'        => 'VAR',
		'precip_last_hour'      => 'in the last hour. ',
		'precip_last_6_hours'   => 'in the last 3 to 6 hours. ',
		'precip_last_24_hours'  => 'in the last 24 hours. ',
		'precip_snow'           => 'There is <b>%s</b> mm (<b>%s</b> inches) of snow on the ground. ',
		'temp_min_max_6_hours'  => 'The maximum and minimum temperatures over the last 6 hours were <b>%s</b> and <b>%s</b> degrees Celsius (<b>%s</b> and <b>%s</b> degrees Fahrenheit).',
		'temp_max_6_hours'      => 'The maximum temperature over the last 6 hours was <b>%s</b> degrees Celsius (<b>%s</b> degrees Fahrenheit). ',
		'temp_min_6_hours'      => 'The minimum temperature over the last 6 hours was <b>%s</b> degrees Celsius (<b>%s</b> degrees Fahrenheit). ',
		'temp_min_max_24_hours' => 'The maximum and minimum temperatures over the last 24 hours were <b>%s</b> and <b>%s</b> degrees Celsius (<b>%s</b> and <b>%s</b> degrees Fahrenheit). ',
		'runway_vis'            => 'The visibility for runway <b>%s</b> is <b>%s</b> meters (<b>%s</b> feet).',
		'runway_vis_min_max'    => 'The visibility for runway <b>%s</b> varies between <b>%s</b> meters (<b>%s</b> feet) and <b>%s</b> meters (<b>%s</b> feet).',
		'light'                 => 'Light ',
		'moderate'              => 'Moderate ',
		'Heavy'                 => 'Heavy ',
		'nearby'                => 'Nearby ',
		'current_weather'       => 'Current weather is <b>%s</b>. ',
		'pretty_print_metar'    => '<b>%s</b> minutes ago, at <b>%s</b>, the wind was %s. The temperature was <b>%s</b> degrees Celsius (<b>%s</b> degrees Fahrenheit), and the pressure was <b>%s</b> hPa (<b>%s</b> inHg). The relative humidity was <b>%s%%</b>. %s %s %s %s %s %s'
	);

	$wind_dir_text_short_array = array(
		'N',
		'N/NE',
		'NE',
		'E/NE',
		'E',
		'E/SE',
		'SE',
		'S/SE',
		'S',
		'S/SW',
		'SW',
		'W/SW',
		'W',
		'W/NW',
		'NW',
		'N/NW',
		'N'
	);

	$wind_dir_text_array = array(
		'North',
		'North/Northeast',
		'Northeast',
		'East/Northeast',
		'East',
		'East/Southeast',
		'Southeast',
		'South/Southeast',
		'South',
		'South/Southwest',
		'Southwest',
		'West/Southwest',
		'West',
		'West/Northwest',
		'Northwest',
		'North/Northwest',
		'North'
	);

	$weather_array = array(
		'PR' => 'Partial ',
		'BC' => 'Patches ',
		'DR' => 'Low Drifting ',
		'BL' => 'Blowing ',
		'SH' => 'Shower(s) ',
		'TS' => 'Thunderstorm ',
		'FZ' => 'Freezing',
		'DZ' => 'Drizzle ',
		'RA' => 'Rain ',
		'SN' => 'Snow ',
		'SG' => 'Snow Grains ',
		'IC' => 'Ice Crystals ',
		'PE' => 'Ice Pellets ',
		'GR' => 'Hail ',
		'GS' => 'Small Hail and/or Snow Pellets ',
		'UP' => 'Unknown ',
		'BR' => 'Mist ',
		'FG' => 'Fog ',
		'FU' => 'Smoke ',
		'VA' => 'Volcanic Ash ',
		'DU' => 'Widespread Dust ',
		'SA' => 'Sand ',
		'HZ' => 'Haze ',
		'PY' => 'Spray',
		'PO' => 'Well-Developed Dust/Sand Whirls ',
		'SQ' => 'Squalls ',
		'FC' => 'Funnel Cloud Tornado Waterspout ',
		'SS' => 'Sandstorm/Duststorm '
	);

	$cloud_condition_array = array(
		'SKC' => 'clear',
		'CLR' => 'clear',
		'VV'  => 'vertical visibility',
		'FEW' => 'a few',
		'SCT' => 'scattered',
		'BKN' => 'broken',
		'OVC' => 'overcast'
	);
?>
