 <tr>
  <td class="annotate-author">
   <?php echo $author ?>
  </td>
  <td class="annotate-rev">
   <a href="<?php echo $phpgw->link('/chora/checkout.php','where=' . $where . '&r=' . $rev . '&rt='.$rt) ?>"><?php echo $rev ?></a>
  </td>
  <td>
   <tt><?php echo $line ?></tt>
  </td>
 </tr>
