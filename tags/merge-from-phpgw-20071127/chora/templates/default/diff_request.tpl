<a name="diff"></a>
<br />
<table width="100%" cellspacing="0" cellpadding="4">
<tr class="diff-request"><td>
This form allows you to request diff's between any two
revisions of a file.  You may select a symbolic revision
name using the selection box or you may type in a numeric
name using the type-in text box.
</td></tr>
<tr class="diff-request"><td>
<form method="POST" action="<?php echo $phpgw->link('/chora/diff.php','rt='.$rt.'&where=' . $where); ?>">
<?php echo generateHiddens() ?>
<table cellspacing="0" cellpadding="1" border="0">
<tr>
<td>
Retrieve diffs between:
</td>
<td>
<select name="r1">
  <option value="0" selected="selected">Use Text Field</option>
  <?php echo $sel ?>
</select>
<input type="text" size="12" name="tr1" value="<?php echo $diffValueLeft ?>" />
</td>
<td>Type:</td>
<td>
<select name="f">
 <option value="h" selected="selected">Human Readable</option>
 <option value="u">Unified</option>
 <option value="c">Context</option>
 <option value="s">Side-by-Side</option>
 <option value="e">Ed Script</option>
</select> &nbsp;
</td>
</tr>
<tr>
<td>and:</td>
<td>
<select name="r2">
  <option value="0" selected="selected">Use Text Field</option>
  <?php echo $sel ?>
</select>
<input type="text" size="12" name="tr2" value="<?php echo $diffValueRight ?>" />
</td>
<td>&nbsp;</td>
<td><input class="button" type="submit" value="Get Diffs" /></td>
</tr>
</table>
</form>
</td></tr>
<tr class="diff-request"><td>
<form method="POST" action="<?php echo $phpgw->link('/chora/cvs.php','rt='.$rt.'&where=' . $where) ?>">
<?php echo generateHiddens() ?>
<table cellspacing="0" cellpadding="1" border="0">
<tr><td>View revisions on:</td>
<td>
<select name="onb">
  <option value="0" <?php if (!isset($onb) || !$onb) echo ' selected="selected"' ?>>All Branches</option>
  <?php echo $selAllBranches ?>
</select> &nbsp;
<input class="button" type="submit" value="View Branch" />
</td></tr>
</table>
</form>
</td></tr>
</table>
