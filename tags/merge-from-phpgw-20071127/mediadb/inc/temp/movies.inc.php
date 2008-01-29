  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_off']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%" >
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=movie&act=view');?>">
       Braveheart
       </a>
    </td>
    <td width="<?php echo $width[1]?>%" >
	Mel Gibson
    </td>
    <td width="<?php echo $width[2]?>%" >
	dvd
    </td>
    <td width="<?php echo $width[3]?>%" >
	1996
    </td>
    <td width="<?php echo $width[5]?>%" >
	Adventure
    </td>
    <td width="<?php echo $width[6]?>%" >
	R
    </td>
    <td width="<?php echo $width[7]?>%" >
      <?php media_rating(7);?>
    </td>
    <td width="<?php echo $width[8]?>%" >
	tooldev
    </td>
    <td width="<?php echo $width[10]?>%">
      <a target="_new" 
         href="http://us.imdb.com/Tsearch<?php echo '?title=Braveheart&type=substring&year=1995&tv=off';?>">  
        IMDB
      </a>
    </td>
    <td width="<?php echo $width[11]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=movie&act=edit')
        .'">' . lang("edit");?></a>
    </td>
    <td width="<?php echo $width[12]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=movie&act=request')
        .'">' . lang("Y");?></a>
    </td>
  </tr>
  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_on']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%" >
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=movie&act=view');?>">
       Braveheart
       </a>
    </td>
    <td width="<?php echo $width[1]?>%" >
	Mel Gibson
    </td>
    <td width="<?php echo $width[2]?>%" >
	dvd
    </td>
    <td width="<?php echo $width[3]?>%" >
	1996
    </td>
    <td width="<?php echo $width[5]?>%" >
	Drama
    </td>
    <td width="<?php echo $width[6]?>%" >
	R
    </td>
    <td width="<?php echo $width[7]?>%" >
      <?php media_rating(4.5);?>
    </td>
    <td width="<?php echo $width[8]?>%" >
	wynns
    </td>
    <td width="<?php echo $width[10]?>%">
      <a target="_new" 
         href="http://us.imdb.com/Tsearch<?php echo '?title='.$media_title.'&type=substring&year='.$media_year.'&tv=off';?>">  
        IMDB
      </a>
    </td>
    <td width="<?php echo $width[11]?>%">
      &nbsp;
    </td>
    <td width="<?php echo $width[12]?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=movie&act=request')
        .'">' . lang("Y");?></a>
    </td>
  </tr>
  <tr bgcolor="<?php echo $GLOBALS['phpgw_info']['theme']['row_off']; ?>" align="center">
    <td width="<?php echo $checksize?>%">
	&nbsp;
    </td>
    <td width="<?php echo $width[0]?>%" >
       <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php','cat=movie&act=view');?>">
       Independence Day
       </a>
       <img src="<?php echo $GLOBALS['phpgw']->common->image('mediabd','new'); ?>" alt="" />
    </td>
    <td width="<?php echo $width[1]; ?>%" >
	Will Smith<br>Tommy Lee Jones
    </td>
    <td width="<?php echo $width[2]; ?>%" >
	dvd
    </td>
    <td width="<?php echo $width[3]; ?>%" >
	1996
    </td>
    <td width="<?php echo $width[5]; ?>%" >
	Science Fiction
    </td>
    <td width="<?php echo $width[6]; ?>%" >
	R
    </td>
    <td width="<?php echo $width[7]; ?>%" >
      <?php media_rating(4); ?>
    </td>
    <td width="<?php echo $width[8]; ?>%" >
	tooldev
    </td>
    <td width="<?php echo $width[10]; ?>%">
      <a target="_new" 
         href="http://us.imdb.com/Tsearch<?php echo '?title=' . $media_title . '&type=substring&year=' . $media_year . '&tv=off'; ?>">  
        IMDB
      </a>
    </td>
    <td width="<?php echo $width[11]; ?>%">
      <a href="<?php echo $GLOBALS['phpgw']->link('/mediadb/index.php', 'cat=movie&act=edit')
        .'">' . lang('edit'); ?></a>
    </td>
    <td width="<?php echo $width[12]; ?>%">
      <?php echo lang('N');?>
    </td>
  </tr>
