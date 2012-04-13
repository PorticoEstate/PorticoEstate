<?php
	//include common logic for all templates
//	include("common.php");
?>

<script type="text/javascript">
function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	//url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;
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