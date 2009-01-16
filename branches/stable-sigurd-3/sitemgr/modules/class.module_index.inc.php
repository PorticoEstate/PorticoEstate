<?php

	class module_index extends Module
	{
		function module_index()
		{
			$this->arguments = array();
			$this->title = "Site Index";
			$this->description = lang('This module provides the site index, it is automatically used by the index GET parameter');
		}

		function get_content(&$arguments,$properties)
		{
			global $objbo;
			$indexarray = $objbo->getIndex();
			$content = "\n".
				'<table border="0" width="100%" align="left" cellspacing="1" cellpadding="0">
				<tr>';
			$catname = '';
			foreach($indexarray as $temppage)
			{
				$buffer = str_pad('', $temppage['catdepth']*24,'&nbsp;');
				if ($catname!=$temppage['catname']) //category name change
				{
					if ($catname!='') //not the first name change
					{
						$content .= '<br><br></td></tr></table></td></tr><tr>';
					}
					$content .= '<td>
					<table border="0" width="100%" cellspacing="0" align="left" cellpadding="0">
						<tr><td>'.$buffer.'</td>
						<td width="100%">';
					$catname = $temppage['catname'];
					if ($temppage['catdepth'])
					{
						$content .= '&middot;&nbsp;';
					}
					//$content .= '<b>'.$catname.'</b> &ndash; <i>'.
					//	$temppage['catdescrip'].'</i>'."\n";
					$content .= "<b>$catname</b>\n";
				}
				$content .= "\n".'<br>&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;'.$temppage['pagelink'];
			}
			$content .= "\n".'</td></tr></table></td></tr></table>';
			if (count($indexarray)==0)
			{
				$content=lang('You do not have access to any content on this site.');
			}
			return $content;
	}
}
?>
