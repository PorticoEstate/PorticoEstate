  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_off']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%" >
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=book&act=view');?>">
       Apache Server Administrator's Handbook
       </a>
    <td width="<?php echo $width[1]?>%" >
       Mohammed J. Kabir
    </td>
    <td width="<?php echo $width[2]?>%" >
	Paperback
    </td>
    <td width="<?php echo $width[3]?>%" >
	2000
    </td>
    <td width="<?php echo $width[5]?>%" >
	Technical
    </td>
    <td width="<?php echo $width[6]?>%" >
	G
    </td>
    <td width="<?php echo $width[7]?>%" >
      <?php media_rating(3);?>
    </td>
    <td width="<?php echo $width[8]?>%" >
	wynns
    </td>
    <td width="<?php echo $width[11]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=book&act=edit')
        .'">' . lang('edit');?></a>
    </td>
    <td width="<?php echo $width[12]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=book&act=request')
        .'">' . lang('Y');?></a>
    </td>
  </tr>
