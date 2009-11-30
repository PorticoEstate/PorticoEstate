<?php 
class edit_transform
{
	var $block_id;

	function apply_transform($title,$content)
	{
		$link_data['menuaction'] = 'sitemgr.Content_UI.manage';
		$link_data['block_id'] = $this->block_id;
		return $script . '<span class="edit"><a target="editwindow" href="' . phpgw_link('/index.php',$link_data)  
			. '">' . lang('Edit this block') . '</a></span>' .
			$content;
	}
}