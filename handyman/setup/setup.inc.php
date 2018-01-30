<?php

    $setup_info['handyman']['name'] = 'handyman';
    $setup_info['handyman']['version'] = '0.0.1';

    // Usure about this app order number
    $setup_info['handyman']['app_order'] = 85; 
    $setup_info['handyman']['enable'] = 1;
    $setup_info['handyman']['app_group'] = 'office';
    $setup_info['handyman']['license'] = 'GPL';

    $setup_info['handyman']['hooks'] = array(
        'menu' => 'phpgwapi.menu_apps.get_menu',
        'login' => 'phpgwapi.menu.clear'
    );

    $setup_info['handyman']['depends'][] = array(
        'appname' => 'phpgwapi',
        'versions' => array('0.9.17', '0.9.18')
    );

    $setup_info['handyman']['depends'][] = array(
        'appname' => 'property',
        'versions' => Array('0.9.17')
    );

    $setup_info['handyman']['tables'] = array();
