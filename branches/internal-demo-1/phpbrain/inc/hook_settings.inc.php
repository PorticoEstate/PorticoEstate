<?php
	/**
	* Knowledge Base
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpbrain
	* @subpackage hooks
	* @version $Id: hook_settings.inc.php 16714 2006-05-09 05:49:38Z skwashd $
	*/

	$show_tree = array(
		'all' => 'The present category and all subcategories under it',
		'only_cat' => 'The present category only'
	);

	$num_lines = array
	(
		'3'  => '3',
		'5'  => '5',
		'10' => '10',
		'15' => '15'
	);

	$num_comments = array
	(
		'5'   => '5',
		'10'  => '10',
		'15'  => '15',
		'20'  => '20',
		'All' => 'All'
	);
	
	 create_select_box('Show articles belonging to', 'show_tree', $show_tree);
	 create_select_box('Maximum number of most popular articles, latest articles and unanswered questions to show in the main view', 'num_lines', $num_lines);
	 create_select_box('Maximum number of comments to show', 'num_comments', $num_comments);
/*
	$GLOBALS['settings'] = array(
		'show_tree' => array(
			'type'    => 'select',
			'label'   => 'Show articles belonging to:',
			'name'    => 'show_tree',
			'values'  => $show_tree,
			'help'    => 'When navigating through categories, choose whether the list of articles shown corresponds only to the present category, or the present category and all categories under it.',
			'default' => 'all'
		),
		'num_lines' => array(
			'type'    => 'select',
			'label'   => 'Maximum number of most popular articles, latest articles and unanswered questions to show in the main view:',
			'name'    => 'num_lines',
			'values'  => $num_lines,
			'default' => '',
			'size'    => '3'
		),
		'num_comments' => array(
			'type'    => 'select',
			'label'   => 'Maximum number of comments to show:',
			'name'    => 'num_comments',
			'values'  => $num_comments,
			'default' => '',
			'size'    => '5'
		)
	);
*/
?>