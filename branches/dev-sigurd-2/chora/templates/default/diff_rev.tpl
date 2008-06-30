<a name="rev<?php echo $rev ?>" />
<table width="100%" cellspacing="0" cellpadding="3" class="diff-back">
<tr class="diff-back"><td width="100%"> 
<table width="100%" cellspacing="0" cellpadding="4" class="diff-back">
<tr class="diff-header"><td align="left">
<a href="<?php echo $textURL ?>"><span class="title"><?php echo $rev ?></span></a> by <?php echo $author ?>
</td><td align="right">
<?php echo $commitDate ?> <i>(<?php echo $readableDate ?> ago)</i>
</td></tr>
<tr valign="top" class="diff-back">
<td width="35%">
<?php if (!empty($commitTags)): ?>
CVS Tags: <b><?php echo $commitTags ?></b>
<br />
<?php endif; ?>
<?php if (!empty($branchPoints)): ?>
Branch Point for: <b><?php echo $branchPoints ?></b>
<br />
<?php endif; ?>
<?php if (!empty($prevRevision)): ?>
Changed since <b><?php echo $prevRevision ?></b>: <?php echo $changedLines ?>
<br />
<a href="<?php echo $diffURL ?>">Diffs to version <?php echo $prevRevision ?></a>
(<a href="<?php echo $uniDiffURL ?>">unified</a>)
<br />
<?php endif; ?>
<a href="<?php echo $phpgw->link('/chora/history.php','rt='.$rt.'&where=' . $where . '&rev' . $rev) ?>">Visual Branch View</a>
<br />
<a href="<?php echo $phpgw->link('/chora/annotate.php','rt='.$rt.'&where=' . $where . '&rev=' . $rev) ?>">Annotate</a>
</td><td class="diff-log" width="65%">
<?php echo $logMessage ?>
</td></tr>
</table>
</td></tr>
</table>
