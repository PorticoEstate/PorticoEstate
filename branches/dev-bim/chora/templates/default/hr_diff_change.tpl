<tr>
<?php if (!empty($left)): ?>
  <td class="hr-diff-change">
   <tt><?php echo $left ?></tt>
  </td>
<?php else: ?>
  <td class="hr-diff-nochange">&nbsp;</td>
<?php endif; ?>
<?php if (!empty($right)): ?>
  <td class="hr-diff-change">
   <tt><?php echo $right ?></tt>
  </td>
<?php else: ?>
  <td class="hr-diff-nochange">&nbsp;</td>
<?php endif; ?>
</tr>
