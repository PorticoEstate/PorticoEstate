<td style="background-color: <?php echo $bg ?>" nowrap="nowrap">
 <a name="rev<?php echo $rev ?>" />
 <a href="<?php echo $phpgw->link('/chora/cvs.php','where=' . $where . '&r=' . $rev . 'rev' . $rev) ?>">
 <span class="title"><?php echo $rev ?></span></a> by <?php echo $author ?> <br />
 <i><?php echo $date ?></i>
<?php if (!empty($lines)): ?>
 <br />Changed: <?php echo $lines ?>
<?php endif ?>
</td>
