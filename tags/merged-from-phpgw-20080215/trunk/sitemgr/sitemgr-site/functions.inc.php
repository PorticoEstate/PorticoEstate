<?php
/*******************************************************\
* This file is for global functions needed by the       *
* sitemgr-site program.  This includes:                 *
*    - phpgw_link($url, $extravars)                     *
*    - sitemgr_link2($url, $extravars)                  *
\*******************************************************/

	function phpgw_link($url, $extravars = '')
	{
		return $GLOBALS['phpgw']->session->link($url, $extravars);
	} 

	function sitemgr_link2($url, $extravars = '')
	{
		//I remove the URL argument for sitemgr_link,since it should always be index.php
		//which shouldn't be needed since the webserver interprets '/' as '/index.php'.
		return sitemgr_link($extravars);
	}

	function sitemgr_link($extravars = '')
	{
		$kp3 = $_GET['kp3'] ? $_GET['kp3'] : $_COOKIE['kp3'];

		if (! $kp3)
		{
			$kp3 = $GLOBALS['phpgw_info']['user']['kp3'];
		}

		// Change http://xyz/index.php?page_name=page1 to
		// http://xyz/page1/ if the htaccess stuff is enabled
		$page_name = '';
		if (!is_array($extravars))
		{
			parse_str($extravars,$extravarsnew);
			$extravars = $extravarsnew;
		}
		$page_name = $extravars['page_name'];

		if (!$page_name == '' &&
			$GLOBALS['sitemgr_info']['htaccess_rewrite'])
		{
			$url = '/'.$page_name;
			$newextravars=array();
			while (list($key,$value) = each($extravars))
			{
				if ($key != 'page_name')
				{
					$newextravars[$key]=$value;
				}
			}
			$extravars = $newextravars;
		}

		// In certain instances (wouldn't it be better to fix these instances? MT)
		// a url may look like this: 'http://xyz//hi.php' or
		// like this: '//index.php?blahblahblah' -- so the code below will remove
		// the inappropriate double slashes and leave appropriate ones
		$url = $GLOBALS['sitemgr_info']['site_url'] . $url;
		$url = substr(ereg_replace('([^:])//','\1/','s'.$url),1);

		// build the extravars string from a array
			
		if (is_array($extravars))
		{
			foreach($extravars as $key => $value)
			{
				if (!empty($new_extravars))
				{
					$new_extravars .= '&';
				}
				$new_extravars .= (($value == '') ? $key : ($key . '=' . urlencode($value)) );
			}
			// This needs to be explictly reset to a string variable type for PHP3
			$extravars = $new_extravars;
		}
		if (isset($GLOBALS['phpgw_info']['server']['usecookies']) && $GLOBALS['phpgw_info']['server']['usecookies'])
		{
			if ($extravars)
			{
				$url .= '?' . $extravars;
			}
		}
		else
		{
			$sessionID  = 'sessionid=' . @$GLOBALS['phpgw_info']['user']['sessionid'];
			$sessionID .= '&kp3=' . $kp3;
			$sessionID .= '&domain=' . @$GLOBALS['phpgw_info']['user']['domain'];
			// This doesn't belong in the API.
			// Its up to the app to pass this value. (jengo)
			// Putting it into the app requires a massive number of updates in email app. 
			// Until that happens this needs to stay here (seek3r)
			if (isset($GLOBALS['phpgw_info']['flags']['newsmode']) && 
				$GLOBALS['phpgw_info']['flags']['newsmode'])
			{
				$url .= '&newsmode=on';
			}
			if ($extravars)
			{
				$url .= '?' . $extravars . '&' . $sessionID;
			}
			else
			{
				$url .= '?' . $sessionID;
			}
		}
		return $url;
	}
?>
