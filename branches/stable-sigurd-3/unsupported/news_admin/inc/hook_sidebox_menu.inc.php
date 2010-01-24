<?php
/**
* TODO Add Header
* Code is GPL
* Written Dave Hall
* (c) 2004 FSF
*/

	$file[] = array
	(
		'text'	=> 'show all news',
		'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'news_admin.uinews.read_news','start' => 0, 'cat_id' => 'all'))
	);

	if ( isset($_GET['cat_id'])
		&& ExecMethod('news_admin.boacl.is_writeable', $_GET['cat_id']))
	{
		$file[] = array
		(
			'text'	=> 'add news item to this category',
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'news_admin.uinews.add', 'cat_id' => $_GET['cat_id']	) )
		);
	}

	display_sidebox('news_admin',lang('news_admin'),$file);
?>
