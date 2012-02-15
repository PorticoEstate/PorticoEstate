<?php
	//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>
<style>
dl.proplist,
dl.proplist-col {
    margin: 1em 0;
    padding-left: 2em;
}
dl.proplist dt,
dl.proplist-col dt { 
    font-style: italic; 
    font-weight: bolder; 
    font-size: 90%; 
    margin: .8em 0 .1em 0;
}

dl.proplist-col,
dl.form-col {
    width: 18em;
    float: left;
    text-align: left;
}

#frontend dl.proplist-col {
    width: 600px; !important
}

table#header {
	margin: 2em;
	
	}

div#unit_selector {
	
}

div#all_units_key_data {
	padding-left: 2em;
	}

div#unit_image {
	margin-left: 2em;
	}

div#unit_image img {
	height:170px;
}

div.yui-navset {
	padding-left: 2em;
	padding-right: 2em;
	}
	
div#contract_selector {
	padding-left: 1em;
	padding-top: 1em;
	}
	
img.list_image {
	margin-right: 5px;
	float:left;
	}

a.list_image {
	float:left;
	display:inline;
	}

ol.list_image {
	float: left;
	}
	
ol.list_image li {
	padding: 1px;
}
	
dl#key_data  {
	padding: 2px;
	}
	
	
dl#key_data dd {
	padding-bottom: 1em;
}

table#key_data td {
	padding-right: 1em;
	padding: 5px;
	}


.user_menu {
	list-style:none;
	height: 100%;
	padding: 2px;
	border-style: none none none solid;
	border-width: 1px;
	border-color: grey;
	padding-left: 5px;
}

.user_menu li {
	margin: 13px;
	}
	
#area_and_price {
	list-style:none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#area_and_price li {
	margin: 13px;
	}
	
#org_units {
	list-style: none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#org_units li {
	margin: 13px;
	}
	
#information {
	list-style:none;
	height: 100%;
	padding: 2px;
	padding-left: 5px;
	float:right;
	padding:0.5em 1em 0 0;
}

#information li {
	margin: 13px;
	}

a.header_link {
	text-decoration: none;
	float: none;
	}
	
#logo_holder {
	border: 0 none;
	font-family:Arial,sans-serif;
font-size:65%;
line-height:1.166;
position: absolute;
padding:2em;
}

em#bold {
	font-weight: bold;
	}

div#header a {
	float: none;
}

.yui-skin-sam .yui-navset .yui-nav, .yui-skin-sam .yui-navset .yui-navset-top .yui-nav {
	border-color: #BF0005;
	border-width:0 0 2px;
	}
	
.yui-skin-sam .yui-navset .yui-content {
	background: none repeat scroll 0 0 #F4F2ED;
}

.yui-skin-sam .yui-navset .yui-nav .selected a, .yui-skin-sam .yui-navset .yui-nav .selected a:focus, .yui-skin-sam .yui-navset .yui-nav .selected a:hover {
	background:url("../../../../assets/skins/sam/sprite.png") repeat-x scroll left -1400px #2647A0;
	}
	
div.tickets {
	margin-top: 1em;
	}

em.select_header {
	font-size: larger;
	padding-top: 10px;
	}

#contract_price_and_area {
	float: left;
	margin: 1em 2em 0 0;
}

#contract_price_and_area li {
		margin-bottom: 1em;
	}

#contract_essentials {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
#composites {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
	
#comment {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
	#contract_essentials li {
		margin-bottom: 1em;
	}
	
#contract_parts {
	float: left;
	margin: 1em 2em 0 2em;
	}
	
div.toolbar {
background-color:#EEEEEE;
border:1px solid #BBBBBB;
float:left;
width:100%;
}

div.toolbar_manual {
background-color:#EEEEEE;
border:1px solid #BBBBBB;
float:left;
width:100%;
}

.yui-pg-container {
	white-space: normal;
	}
	
li.ticket_detail {
	padding: 5px;
	margin-left: 5px;
	}

div.success {
	font-weight: normal;
	margin:10px;
	padding:5px;
	font-size:1.1em;
	text-align: left;
	background-color: green;
	border:1px solid #9F6000;
	color: white;
}

div.error {
	font-weight: normal;
	margin:10px;
	padding:5px;
	font-size:1.1em;
	text-align: left;
	background-color: red;
	border:1px solid #9F6000;
	color: white;
}
</style>
<div class="yui-content" style="width: 100%;">
	<div id="details">
	
	<?php if($message){?>
	<div class="success">
		<?php echo $message;?>
	</div>
	<?php }else if($error){?>
	<div class="error">
		<?php echo $error;?>
	</div>
	<?php }?>
	</div>
		<h1><?php echo lang('activity') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col" style="width: 60%">
				<h2><?php echo lang('what')?></h2>
				<dt>
					<label for="title"><?php echo lang('title') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_title();?>
				</dd>
				<dt>
					<label for="description"><?php echo lang('description') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_description(); ?>
				</dd>
				
				<dt>
					<label for="category"><?php echo lang('category') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_category()){
							echo $act_so->get_category_name($activity->get_category());
						}
					?>
				</dd>
				<dt>
					<label for="target"><?php echo lang('target') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_target()){
							$current_target_ids = $activity->get_target();
							$current_target_id_array=explode(",", $current_target_ids);
							foreach($current_target_id_array as $curr_target)
							{
								echo $act_so->get_target_name($curr_target).'<br/>';
							}
						}
					?>
				</dd>
				<dt>
					<label for="district"><?php echo lang('district') ?></label>
				</dt>
				<dd>
					<?php
					if($activity->get_district()){
						$current_district_ids = $activity->get_district();
						$current_district_id_array=explode(",", $current_district_ids);
						foreach($current_district_id_array as $curr_district)
						{
							echo $act_so->get_district_name($curr_district).'<br/>';
						}
					}
					?>
				</dd>
				<dt>
					<label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
				<dd>
					<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> disabled="disabled" />
				</dd>
				<hr />
				<h2><?php echo lang('where_when')?></h2>
				<dt>
					<?php if($activity->get_internal_arena()) { ?>
					<label for="arena"><?php echo lang('building') ?></label>
					<?php }?>
				</dt>
				<dd>
					<?php
						if($activity->get_internal_arena()){
							echo activitycalendar_soarena::get_instance()->get_building_name($activity->get_internal_arena());
						}
					?>
				</dd>
				<dt>
					<?php if($activity->get_arena()) { ?>
					<label for="arena"><?php echo lang('arena') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						if($activity->get_arena()){
							echo activitycalendar_soarena::get_instance()->get_arena_name($activity->get_arena());
						}
					?>
				</dd>
				<dt>
					<label for="time"><?php echo lang('time') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_time();?>
				</dd>
				<dt>
					<label for="office"><?php echo lang('office') ?></label>
				</dt>
				<dd>
					<?php
						if($activity->get_office()){
							echo $act_so->get_office_name($activity->get_office());
						}
					?>
				</dd>
				<hr />
				<h2><?php echo lang('who')?></h2>
				<dt>
					<label for="organization_id"><?php echo lang('organization') ?></label>
				</dt>
				<dd>
					<?php echo $organization->get_name();?>
					<a href="index.php?menuaction=activitycalendarfrontend.uiactivity.edit_organization_values&amp;organization_id=<?php echo $organization->get_id();?>"><?php echo lang('edit_organization');?></a>
				</dd>
				<dt>
					<label for="group_id" id="group_label"><?php echo lang('group') ?></label>
				</dt>
				<dd>
					<?php 
					if($activity->get_group_id()){
						echo $group->get_name();?>
				<?php }
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_1()) { ?>
					<label for="contact_person_1"><?php echo lang('contact_person_1') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<label for="contact1_name">Navn</label>
					<?php echo isset($contact1)?$contact1->get_name():''?><br/>
					<label for="contact1_phone">Telefon</label>
					<?php echo isset($contact1)?$contact1->get_phone():''?><br/>
					<label for="contact1_mail">E-post</label>
					<?php echo isset($contact1)?$contact1->get_email():''?><br/>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2()) { ?>
					<label for="contact_person_2"><?php echo lang('contact_person_2') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<label for="contact2_name">Navn</label>
					<?php echo isset($contact2)?$contact2->get_name():''?><br/>
					<label for="contact2_phone">Telefon</label>
					<?php echo isset($contact2)?$contact2->get_phone():''?><br/>
					<label for="contact2_mail">E-post</label>
					<?php echo isset($contact2)?$contact2->get_email():''?><br/>
				</dd>
			    
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_activity" value="' . lang('save') . '" onclick="return allOK();"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>