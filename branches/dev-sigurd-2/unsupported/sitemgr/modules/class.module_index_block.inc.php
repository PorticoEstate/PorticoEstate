<?php

class module_index_block extends Module
{
	function module_index_block()
	{
		$this->arguments = array();
		$this->title = "Root Site Index";
		$this->description = "This module displays the root categories, meant for side areas";
	}

	function get_content(&$arguments,$properties)
	{
		global $objbo;
		$indexarray = $objbo->getIndex(false,true);
		$content = "\n".'<table border="0" cellspacing="0" cellpadding="0" width="100%">';
		$catname = '';
		foreach($indexarray as $page)
		{
			if ($catname!=$page['catname']) //category name change
			{
				if ($catname=='')
				{
					$break = '';
				}
				else
				{
					$break = '<br>';
				}
				$catname = $page['catname'];
				$content.="\n".'<tr><td width="15%" colspan="2">'.$break.'&nbsp;<b>'.
					$page['catlink'].'</b></td></tr>'."\n";
			}
			if (!$page['hidden'])
			{
				$content .= "\n".'<tr><td align="right" valign="top" width="15%">'.
					'&middot;&nbsp;</td><td>'.$page['pagelink'].'</td></tr>';
			}
		}
		$content .= "\n</table>";
		$content .= '<br>&nbsp;&nbsp;<i><a href="'.sitemgr_link2('/index.php','index=1').'"><font size="1">(' . lang('View full index') . ')</font></a></i>';
		if (count($indexarray)==0)
		{
			$content=lang('You do not have access to any content on this site.');
		}
		return $content;
	}
}
?>
