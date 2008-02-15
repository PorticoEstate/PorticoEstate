<?


	$phpgw_info["flags"] = array(
		"noheader" => True,
		"nonavbar" => True,
		"currentapp" => "eldaptir"
	);
	include("../header.inc.php");

	$ldap = $phpgw->common->ldapConnect();

	$dentry = urldecode($entry);
	echo $dentry;

	$filtera = split(',',$dentry);
	$filter = $filtera[0];
	$base = $filtera[1].','.$filtera[2].','.$filtera[3];
	echo "<br>Filter: ".$filter;
	echo "<br>Base: ".$base;
	$sr = ldap_list($ldap,$base,$filter);
	$info = ldap_get_entries($ldap, $sr);

	for ($i=0;$i<count($info)-1;$i++)
	{
		for ($j=0;$j<count($info[$i]);$j++)
		{
			#if ($info[$i][$j]<>"") {
				for ($k=0;$k<count($info[$i][$j]);$k++)
				{
					echo "<br>".$info[$i][$j]."=".$info[$i][$info[$i][$j]][$k];
				}
			#}
		}
	}
?>
