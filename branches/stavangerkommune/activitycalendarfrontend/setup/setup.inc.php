<?php

$setup_info['activitycalendarfrontend']['name'] = 'activitycalendarfrontend';
$setup_info['activitycalendarfrontend']['version'] = '0.1';
$setup_info['activitycalendarfrontend']['app_order'] = 61;
$setup_info['activitycalendarfrontend']['enable'] = 1;
$setup_info['activitycalendarfrontend']['app_group'] = 'office';

$setup_info['activitycalendarfrontend']['description'] = 'Bergen kommune activitycalendarfrontend';

$setup_info['activitycalendarfrontend']['author'][] = array
    (
    'name' => 'Bouvet ASA',
    'email' => 'info@bouvet.no'
);

/* Dependencies for this app to work */
$setup_info['activitycalendarfrontend']['depends'][] = array(
    'appname' => 'phpgwapi',
    'versions' => Array('0.9.17', '0.9.18')
);

$setup_info['activitycalendarfrontend']['depends'][] = array(
    'appname' => 'booking',
    'versions' => Array('0.2.00', '0.2.01', '0.2.02', '0.2.03', '0.2.04', '0.2.05', '0.2.06', '0.2.07', '0.2.08', '0.2.09', '0.2.10', '0.2.11','0.2.12')
);

$setup_info['activitycalendarfrontend']['depends'][] = array(
    'appname' => 'property',
    'versions' => Array('0.9.17')
);

$setup_info['activitycalendarfrontend']['depends'][] = array(
    'appname' => 'activitycalendar',
    'versions' => Array('0.1.3', '0.1.4', '0.1.5', '0.1.6', '0.1.7', '0.1.8', '0.1.9', '0.1.10', '0.1.11')
);

/* The hooks this app includes, needed for hooks registration */
$setup_info['activitycalendarfrontend']['hooks'] = array
    (
    'menu' => 'activitycalendarfrontend.menu.get_menu',
    'config'
);
