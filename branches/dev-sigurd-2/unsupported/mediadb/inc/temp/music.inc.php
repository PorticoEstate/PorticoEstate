  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_off']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%" >
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=music&act=view');?>">
       Pure Country
       </a>
    </td>
    <td width="<?php echo $width[1]?>%" >
       George Strait
    </td>
    <td width="<?php echo $width[2]?>%" >
	cassette
    </td>
    <td width="<?php echo $width[3]?>%" >
	1996
    </td>
    <td width="<?php echo $width[5]?>%" >
	Country
    </td>
    <td width="<?php echo $width[6]?>%" >
	G
    </td>
    <td width="<?php echo $width[7]?>%" >
      <?php media_rating(5);?>
    </td>
    <td width="<?php echo $width[8]?>%" >
	tooldev
    </td>
    <td width="<?php echo $width[11]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=music&act=edit')
        .'">' . lang('edit');?></a>
    </td>
    <td width="<?php echo $width[12]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=music&act=request')
        .'">' . lang('Y');?></a>
    </td>
  </tr>
