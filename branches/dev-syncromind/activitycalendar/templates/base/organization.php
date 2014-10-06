<?php
	//include common logic for all templates
	include("common.php");
?>
<div class="identifier-header">
	<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('organization') ?></h1>
</div>
<?php echo activitycalendar_uicommon::get_page_message($message) ?>
<?php echo activitycalendar_uicommon::get_page_error($error) ?>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($organization->get_id()){ echo $organization->get_id(); } else { echo '0'; }  ?>"/>
			<input type="hidden" name="original_org_id" value="<?php if($organization->get_original_org_id()){ echo $organization->get_original_org_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt><label for="orgname">Organisasjonsnavn</label></dt>
				<dd><?php echo $organization->get_name();?></dd>
				<dt><label for="orgno">Organisasjonsnummer</label></dt>
				<dd>
					<?php
					if($editable){?>
						<input type="text" name="orgno" value="<?php echo $organization->get_organization_number();?>"/><br/>
					<?php
					}else{?>
						<?php echo $organization->get_organization_number();?>
					<?php }?>
				</dd>
				<dt><label for="district">Bydel</label></dt>
				<dd>
				<?php if($editable){?>
				<?php $curr_district = $organization->get_district();
					if(!is_numeric($curr_district))
					{
						$curr_district = activitycalendar_soactivity::get_instance()->get_district_from_name($organization->get_district());
					}
				?>
					<select name="org_district">
						<option value="0">Ingen bydel valgt</option>
					<?php
						foreach($districts as $d){?>
							<option value="<?php echo $d['part_of_town_id']?>" <?php echo ($curr_district == $d['part_of_town_id'])? 'selected' : '' ?>><?php echo $d['name']?></option>
						<?php }?>
					</select>
				<?php }else{
						if($organization->get_change_type() == 'new'){
                                                    if($organization->get_district()){?>
							<?php echo activitycalendar_soactivity::get_instance()->get_district_from_id($organization->get_district());?>
					<?php       }

                                                }else{
							if($organization->get_district() && is_numeric($organization->get_district())){?>
								<?php echo activitycalendar_soactivity::get_instance()->get_district_from_id($organization->get_district());?>
					<?php 	}else{?>
								<?php echo $organization->get_district();?>
					<?php  	}
						  }?>
				<?php }?>
				</dd>
				<dt><label for="homepage">Hjemmeside</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="homepage" value="<?php echo $organization->get_homepage();?>"/><br/>
				<?php }else{?>
					<?php echo $organization->get_homepage();?>
				<?php }?>
				</dd>
				<dt><label for="email">E-post</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="email" value="<?php echo $organization->get_email();?>"/>
				<?php }else{?>
					<?php echo $organization->get_email();?>
				<?php }?>
				</dd>
				<dt><label for="phone">Telefon</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="phone" value="<?php echo $organization->get_phone();?>"/>
				<?php }else{?>
					<?php echo $organization->get_phone();?>
				<?php }?>
				</dd>
				<dt><label for="street">Adresse</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="address" value="<?php echo $organization->get_address();?> <?php echo $organization->get_addressnumber();?>"/>
				<?php }else{?>
					<?php echo $organization->get_address() . ' ' . $organization->get_addressnumber();;?>
				<?php }?>
				</dd>
                                <dt><label for="street">Postnummer/Sted</label></dt>
				<dd>
				<?php if($editable){?>
                                    <input type="text" name="zip_code" value="<?php echo $organization->get_zip_code();?>" size="6"/>&nbsp;&nbsp;<input type="text" name="city" value="<?php echo $organization->get_city();?>"/>
				<?php }else{?>
					<?php echo $organization->get_zip_code() . ' ' . $organization->get_city();?>
				<?php }?>
				</dd>
				<dt><label for="org_description">Beskrivelse</label></dt>
				<dd>
				<?php if($editable){?>
					<textarea rows="10" cols="100" name="org_description"><?php echo $organization->get_description();?></textarea>
				<?php }else{?>
					<?php echo $organization->get_description();?>
				<?php }?>
				</dd>
				<?php if($contactperson1){?>
				<dt><label>Kontaktperson 1</label></dt>
				<dd><input type="hidden" name="contact1_id" value="<?php echo $contactperson1->get_id();?>"/></dd>
				<dt><label for="contact1_name">Navn</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact1_name" value="<?php echo $contactperson1->get_name();?>"/><br/>
				<?php }else{?>
					<?php echo $contactperson1->get_name();?>
				<?php }?>
				</dd>
				<dt><label for="contact1_phone">Telefon</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact1_phone" value="<?php echo $contactperson1->get_phone();?>"/>
				<?php }else{?>
					<?php echo $contactperson1->get_phone();?>
				<?php }?>
				</dd>
				<dt><label for="contact1_mail">E-post</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact1_email" value="<?php echo $contactperson1->get_email();?>"/>
				<?php }else{?>
					<?php echo $contactperson1->get_email();?>
				<?php }?>
				</dd>
				<?php }?>
				<?php if($contactperson2){?>
				<dt><label>Kontaktperson 2</label></dt>
				<dd><input type="hidden" name="contact2_id" value="<?php echo $contactperson2->get_id();?>"/></dd>
				<dt><label for="contact1_name">Navn</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact2_name" value="<?php echo $contactperson2->get_name();?>"/><br/>
				<?php }else{?>
					<?php echo $contactperson2->get_name();?>
				<?php }?>
				</dd>
				<dt><label for="contact1_phone">Telefon</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact2_phone" value="<?php echo $contactperson2->get_phone();?>"/>
				<?php }else{?>
					<?php echo $contactperson2->get_phone();?>
				<?php }?>
				</dd>
				<dt><label for="contact1_mail">E-post</label></dt>
				<dd>
				<?php if($editable){?>
					<input type="text" name="contact2_email" value="<?php echo $contactperson2->get_email();?>"/>
				<?php }else{?>
					<?php echo $contactperson2->get_email();?>
				<?php }?>
				</dd>
				<?php }?>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						if($organization->get_original_org_id() && $organization->get_original_org_id() > 0)
						{
							echo '<input type="submit" name="update_organization" value="' . lang('update_org') . '"/>';
							echo '<input type="submit" name="reject_organization_update" value="' . lang('reject') . '"/>';
						}
						else
						{
							echo '<input type="submit" name="store_organization" value="' . lang('store') . '"/>';
							echo '<input type="submit" name="reject_organization" value="' . lang('reject') . '"/>';
						}
						echo '<a href="' . $cancel_link . '">' . lang('back_to_list') . '</a>';
					}
					else
					{
						if(!$organization->get_transferred())
						{
							echo '<input type="submit" name="edit_organization" value="' . lang('edit') . '"/>';
						}
						echo '<a href="' . $cancel_link . '">' . lang('back_to_list') . '</a>';
					}
				?>
			</div>
		</form>
	</div>
</div>
