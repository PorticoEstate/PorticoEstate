<table border="0" align="center" width="500" cellpadding="2" cellspacing="4">
<tr><td colspan="2" class="header"><b><?php echo "Some of Chora's configuration files are missing:" ?></td></tr>

<?php if (!@is_readable('./config/conf.php')): ?>
<tr>
  <td align="right" class="smallheader"><b>conf.php</b></td>
  <td class="light"><?php echo 'This is the main Chora configuration file. It contains paths and options for all Chora scripts.' ?></td>
</tr>
<?php endif; ?>

<?php if (!@is_readable('./config/cvsroots.php')): ?>
<tr>
  <td align="right" class="smallheader"><b>cvsroots.php</b></td>
  <td class="light"><?php echo 'This file defines all of the cvsroots that you wish Chora to display.'; ?></td>
</tr>
<?php endif; ?>

<?php if (!@is_readable('./config/html.php')): ?>
<tr>
  <td align="right" class="smallheader"><b>html.php</b></td>
  <td class="light"><?php echo 'This file controls the stylesheet that is used to set colors and fonts in addition to or overriding Horde defaults.' ?></td>
</tr>
<?php endif; ?>

<?php if (!@is_readable('./config/mime.php')): ?>
<tr>
  <td align="right" class="smallheader"><b>mime.php</b></td>
  <td class="light"><?php echo 'This file defines how Chora recognizes MIME file types.' ?></td>
</tr>
<?php endif; ?>

</table>
