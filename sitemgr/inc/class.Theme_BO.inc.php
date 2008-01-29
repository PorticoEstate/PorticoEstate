<?php
	class Theme_BO
	{
		var $preferenceso;

		function Theme_BO()
		{
		}

		function getAvailableThemes()
		{
			$sitemgr_dir = $GLOBALS['Common_BO']->sites->current_site['site_dir'];
			$dirname = $sitemgr_dir . SEP . 'templates' . SEP;
			$result_array=array();
			@$handle=opendir($dirname);
		
			if ($handle)
			{
				while (($file = readdir($handle)) !== false)
				{
					if (is_dir($dirname . $file) && file_exists($dirname . $file . SEP . 'main.tpl'))
					{
						$result_array[]=array('value'=>$file,'display'=>$file);
					}	
				}
				closedir($handle);
			}
			return $result_array ? $result_array : array(array('value'=>'','display'=>lang('No templates found.')));
		}
		
	}

?>
