<?php
	/**
	* Filemanager - test
	*
	* @copyright Copyright (C) 2002-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package filemanager
	* @subpackage test
	* @version $Id$
	*/


$phpgw_info["flags"] = array("currentapp" => "filemanager",
                                "noheader" => False,
                                "noappheader" => False,
                                "enable_vfs_class" => True);

/**
 * Include phpgroupware header
 */
include("../../header.inc.php");

  function getmicrotime()
  { 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
   } 
   
	function stats($array)
	{
   		$mean = array_sum($array)/count($array);
   		$a = 0;
   		foreach ($array as $value)
   		{
   			$a += ($value - $mean)*($value - $mean);
   		}
   		$std = sqrt($a/count($array));
   		$error = $std/sqrt(count($array));
   		echo "mean time: $mean error: +-$error";
	}
    echo '<b>Benchmarking vfs::ls</b><br>';
    $times = array(); 
	$phpgw->vfs->cd();
	for ($i=0;$i<20; $i++)
	{
		$phpgw->vfs->dav_client->cached_props = array();
		$time1 = getmicrotime();
		$result = $phpgw->vfs->ls (array ('string' => ''));
		$time = getmicrotime() - $time1;
		$times[] = $time;
		echo "run $i: $time<br>";
		//sleep(1);
		flush();
	}
	stats($times);
	
	echo '<br><b>Benchmarking dav_client::get_properties</b><br>';
    $times = array(); 
	$phpgw->vfs->cd();
	for ($i=0;$i<20; $i++)
	{
		$phpgw->vfs->dav_client->cached_props = array();
		$time1 = getmicrotime();
		$result = $phpgw->vfs->dav_client->get_properties('/home/sim');
		$time = getmicrotime() - $time1;
		$times[] = $time;
		echo "run $i: $time<br>";
		flush();
	}
	stats($times);
?>
