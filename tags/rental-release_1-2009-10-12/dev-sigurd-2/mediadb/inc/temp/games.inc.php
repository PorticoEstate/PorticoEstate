  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_off']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%">
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=game&act=view');?>">
       Ecco The Dolphin: Defender of the Future
       </a>
    </td>
    <td width="<?php echo $width[1]?>%" >
       Sega
    </td>
    <td  width="<?php echo $width[2]?>%" >
       Sega Dreamcast
    </td>
    <td  width="<?php echo $width[3]?>%">
	2000
    </td>
    <td  width="<?php echo $width[5]?>%">
	Adventure
    </td>
    <td  width="<?php echo $width[6]?>%">
	E
    </td>
    <td width="<?php echo $width[7]?>%" >
      <?php media_rating(4);?>
    </td>
    <td width="<?php echo $width[8]?>%" >
	tooldev
    </td>
    <td width="<?php echo $width[11]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=game&act=edit')
        .'">' . lang('edit');?></a>
    </td>
    <td width="<?php echo $width[12]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=game&act=request')
        .'">' . lang('Y');?></a>
    </td>
  </tr>
