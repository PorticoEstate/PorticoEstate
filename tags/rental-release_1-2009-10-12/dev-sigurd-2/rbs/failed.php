<?php
 $phpgw_info["flags"]["currentapp"] = "rbs";
 include "config.inc";
 include "functions.inc";
 include "connect.inc";
include "mrbs_auth.inc";
include "mrbs_sql.inc";
?>

<H1><?php echo $lang[accessdenied]?></H1>
<P>
  <?php echo $lang[norights]?>
</P>
<P>
<?php
  echo "<a href=".$phpgw->link("index.php").">".$lang[returnprev]."</a>"; 
        $phpgw->common->phpgw_footer();	
?>
</P></BODY></HTML>
