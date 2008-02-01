<br />

<table class="outline-lite" width="100%" border="0" cellspacing="1" cellpadding="1">
<tr><td>
<table width="100%" border="0" cellspacing="1" cellpadding="2">
 <tr class="header">
  <th class="<?php echo $acts['sbt']==CVSLIB_SORT_NAME?'header-sel':'' ?>" align="left">
    <a href="<?php echo $url['name'] ?>"><img src="<?php echo graphic($acts['ord']?'up.gif':'down.gif') ?>" border="0" alt="Sort Order" /></a>&nbsp;
    <a href="<?php echo $url['name'] ?>">File</a>
  </th>
  <th class="<?php echo $acts['sbt']==CVSLIB_SORT_REV?'header-sel':'' ?>" align="left" nowrap="nowrap">
    <a href="<?php echo $url['rev'] ?>"><img src="<?php echo graphic($acts['ord']?'up.gif':'down.gif') ?>" border="0" alt="Sort Order" /></a>&nbsp; 
    <a href="<?php echo $url['rev'] ?>">Rev</a>
  </th>
  <th class="<?php echo $acts['sbt']==CVSLIB_SORT_AUTHOR?'header-sel':'' ?>" align="left" nowrap="nowrap">
    <a href="<?php echo $url['author'] ?>"><img src="<?php echo graphic($acts['ord']?'up.gif':'down.gif') ?>" border="0" alt="Sort Order" /></a>&nbsp; 
    <a href="<?php echo $url['author'] ?>">Author</a>
  </th>
  <th class="<?php echo $acts['sbt']==CVSLIB_SORT_AGE?'header-sel':'' ?>" align="left" nowrap="nowrap">
    <a href="<?php echo $url['age'] ?>"><img src="<?php echo graphic($acts['ord']?'up.gif':'down.gif') ?>" border="0" alt="Sort Order" /></a>&nbsp;
    <a href="<?php echo $url['age'] ?>">Date</a>
  </th>
  <th align="left">Last Log</th>
 </tr>
