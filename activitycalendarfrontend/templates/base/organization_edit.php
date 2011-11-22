<?php
	//include common logic for all templates
//	include("common.php");
?>

<script type="text/javascript">
function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"address\" id=\"address\" size\"5\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

function allOK()
{
	if(document.getElementById('title').value == null || document.getElementById('title').value == '')
	{
		alert("Tittel må fylles ut!");
		return false;
	} 
	if(document.getElementById('internal_arena_id').value == null || document.getElementById('internal_arena_id').value == 0)
	{
		if(document.getElementById('arena_id').value == null || document.getElementById('arena_id').value == 0)
		{
			alert("Arena må fylles ut!");
			return false;
		}
	}
	if(document.getElementById('time').value == null || document.getElementById('time').value == '')
	{
		alert("Tid må fylles ut!");
		return false;
	}
	if(document.getElementById('category').value == null || document.getElementById('category').value == 0)
	{
		alert("Kategori må fylles ut!");
		return false;
	}
	if(document.getElementById('office').value == null || document.getElementById('office').value == 0)
	{
		alert("Hovedansvarlig kulturkontor må fylles ut!");
		return false;
	}
	else
		return true;
}

</script>

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
		<h1><?php echo lang('edit_organization') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="organization_id" id="organization_id" value="<?php echo $organization->get_id()?>" />
			<dl class="proplist-col" style="width: 200%">
				<dt>
					<label for="organization_id"><?php echo lang('organization') ?></label>
				</dt>
						<dt><label for="orgname">Organisasjonsnavn</label></dt>
						<dd><input type="text" name="orgname" value="<?php echo $organization->get_name()?>"/></dd>
						<dt><label for="orgno">Organisasjonsnummer</label></dt>
						<dd><input type="text" name="orgno" value="<?php echo $organization->get_organization_number()?>"/></dd>
						<dt><label for="homepage">Hjemmeside</label></dt>
						<dd><input type="text" name="homepage" value="<?php echo $organization->get_homepage()?>"/></dd>
						<dt><label for="email">E-post</label></dt>
						<dd><input type="text" name="email" value="<?php echo $organization->get_email()?>"/></dd>
						<dt><label for="phone">Telefon</label></dt>
						<dd><input type="text" name="phone" value="<?php echo $organization->get_phone()?>"/></dd>
						<dt><label for="street">Gate</label></dt>
						<dd><input type="text" name="address" id="address" value="<?php echo $organization->get_address()?>"/>
						<dt><label for="org_description">Beskrivelse</label></dt>
						<dd><textarea rows="10" cols="100" name="org_description"><?php echo $organization->get_description()?></textarea></dd>
					<hr/>
					<b>Kontaktperson 1</b><br/>
					<dt><label for="contact1_name">Navn</label>
					<input type="text" name="org_contact1_name" value="<?php echo isset($contact1)?$contact1->get_name():''?>"/><br/>
					<dt><label for="contact1_phone">Telefon</label>
					<input type="text" name="org_contact1_phone" value="<?php echo isset($contact1)?$contact1->get_phone():''?>"/><br/>
					<dt><label for="contact1_mail">E-post</label>
					<input type="text" name="org_contact1_email" value="<?php echo isset($contact1)?$contact1->get_email():''?>"/><br/>
					<b>Kontaktperson 2</b><br/>
					<dt><label for="contact2_name">Navn</label>
					<input type="text" name="org_contact2_name" value="<?php echo isset($contact2)?$contact2->get_name():''?>"/><br/>
					<dt><label for="contact2_phone">Telefon</label>
					<input type="text" name="org_contact2_phone" value="<?php echo isset($contact2)?$contact2->get_phone():''?>"/><br/>
					<dt><label for="contact2_mail">E-post</label>
					<input type="text" name="org_contact2_email" value="<?php echo isset($contact2)?$contact2->get_email():''?>"/><br/>
				</dt>
				<div class="form-buttons">
					<input type="submit" name="save_org" value="<?php echo lang('send_change_request') ?>"/>
				</div>
			</dl>
			
		</form>
		
	</div>
</div>