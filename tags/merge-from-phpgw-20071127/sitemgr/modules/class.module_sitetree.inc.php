<?php 

class module_sitetree extends Module
{
	function module_sitetree()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->cookie = array('menutree');
		$this->title = lang('Site Tree Menu');
		$this->description = lang('This block displays a javascript based tree menu');
		$this->expandedcats;
	}

	function showcat($cats)
	{
		while(list($cat_id,$cat) = each($cats))
		{
			$status = in_array($cat_id,$this->expandedcats);
			$childrenandself = array_keys($GLOBALS['objbo']->getCatLinks($cat_id));
			$childrenandself[] = $cat_id;
			$catcolour = in_array($GLOBALS['page']->cat_id,$childrenandself) ? "red" : "black";
			$tree .= "\n" . 
				'<tr><td width="10%">' . 
				'<img src="images/tree_' .
				($status ? "collapse" : "expand") .
				'.gif" onclick="toggle(this, \'' . 
				$cat_id . 
				'\')"></td><td><b title="' .
				$cat['description'] .
				'" style="color:' .
				$catcolour .
				'">'.
				$cat['name'] . 
				'</b></td></tr>' . 
				"\n";
			$subcats = $GLOBALS['objbo']->getCatLinks($cat_id,False);
			$pages = $GLOBALS['objbo']->getPageLinks($cat_id);
			if ($subcats || $pages)
			{
				$tree .= '<tr><td></td><td><table style="display:' .
					($status ? "block" : "none") .
					'" border="0" cellspacing="0" cellpadding="0" width="100%" id="'.
					$cat_id .
					'">';
				while(list($page_id,$page) = @each($pages))
				{
					//we abuse the subtitle in a nonstandard way: we want it to serve as a *short title* that is displayed
					//in the tree menu, so that we can have long titles on the page that would not be nice in the tree menu
					$title = $page['subtitle'] ? $page['subtitle'] : $page['title'];
					$tree .= '<tr><td colspan="2">' . 
						(($page_id == $GLOBALS['page']->id) ? 
							('<span style="color:red">' . $title . '</span>') :
							('<a href="' . sitemgr_link('page_name='. $page['name']) . '">' . $title . '</a>')
						) . 
						'</td></tr>';
				}
				if ($subcats)
				{
					$tree .= $this->showcat($subcats);
				}

				$tree .= '</table></td></tr>';
			}
		}
		return $tree;
	}

	function get_content(&$arguments,$properties)
	{
		$title = '';
		if ($arguments['menutree'])
		{
			$this->expandedcats = array_keys($arguments['menutree']);
		}
		else
		{
			$this->expandedcats = Array();
		}
		$topcats = $GLOBALS['objbo']->getCatLinks(0,False);

		$content = "<script type='text/javascript'>
// the whole thing only works in a DOM capable browser or IE 4*/

function add(catid)
{
	document.cookie = 'block[" . $this->block->id . "][menutree][' + catid + ']=';
}

function remove(catid)
{
	var now = new Date();
	document.cookie = 'block[" . $this->block->id . "][menutree][' + catid + ']=; expires=' + now.toGMTString();
}

function toggle(image, catid)
{
	if (document.getElementById)
	{ //DOM capable
		styleObj = document.getElementById(catid);
	}
	else //we're helpless
	{
	return 
	}

	if (styleObj.style.display == 'none')
	{
		add(catid);
		image.src = 'images/tree_collapse.gif';
		styleObj.style.display = 'block';
	}
	else
	{
		remove(catid);
		image.src = 'images/tree_expand.gif';
		styleObj.style.display = 'none';
	}
}
</script>";

		if (count($topcats)==0)
		{
			$content=lang('You do not have access to any content on this site.');
		}
		else
		{
			$content .= "\n" . 
				'<table border="0" cellspacing="0" cellpadding="0" width="100%">' .
				$this->showcat($topcats) .
				'</table>' .
				"\n";
			$content .= '<br><a href="'.sitemgr_link('toc=1').'"><font size="1">(' . lang('Table of contents') . ')</font></a>';
		}
		return $content;
	}
}
