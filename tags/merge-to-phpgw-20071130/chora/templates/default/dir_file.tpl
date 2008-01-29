<tr class="<?php echo $attic?'attic':"item$dirrow" ?>">
 <td nowrap="nowrap">
   <a href="<?php echo $url ?>">
<?php if ($attic) : ?>
   <img src="<?php echo graphic('deleted.gif') ?>" border="0" width="16" height="16" alt="Deleted File" />
<?php else: ?>
   <img src="<?php echo graphic('text.gif') ?>" border="0" width="16" height="16" alt="File" />
<?php endif; ?>
   <?php echo $name ?></a>
 </td>
 <td>
   &nbsp;<b><a href="<?php echo $phpgw->link('/chora/checkout.php', 'where=' . $fileName . '&r=' . $head . '&rt='.$rt) ?>"><?php echo $head ?></a></b>
 </td>
<td>
   &nbsp;<?php echo $author ?>
</td>
 <td nowrap="nowrap">
   &nbsp;
   <i><?php echo $readableDate ?></i>
 </td>
 <td nowrap="nowrap">
   &nbsp;
   <?php if (!empty($log)) : ?>
   <?php echo htmlspecialchars($shortLog) ?>
   <?php endif; ?>
 </td>
</tr>
