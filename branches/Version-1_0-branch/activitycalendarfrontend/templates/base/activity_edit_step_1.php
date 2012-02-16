<?php ?>
<script type="text/javascript">
function isOK()
{
	if(document.getElementById('activity_id').value == null || document.getElementById('activity_id').value == '' || document.getElementById('activity_id').value == 0)
	{
		alert("Du må velge en aktivitet som skal endres!");
		return false;
	}
	else
	{
		return true;
	}
}
function get_activities()
{
	var org_id = document.getElementById('organization_id').value;
	var div_select = document.getElementById('activity_select');

	url = "/aktivby/registreringsskjema/ny/index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_activities&amp;phpgw_return_as=json&amp;orgid=" + org_id;
	//url = "index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_activities&amp;phpgw_return_as=json&amp;orgid=" + org_id;

var divcontent_start = "<select name=\"activity_id\" id=\"activity_id\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_select.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}

YAHOO.util.Event.onDOMReady(function()
{
	get_activities();
});
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
	<h1><?php echo lang('edit_activity');?></h1>
	<form action="#" method="post">
		<dl class="proplist-col" style="width: 200%">
			<dt>
				<?php echo lang('activity_edit_helptext_step1')?><br/><br/>
				<?php echo lang('activity_org_helptext_step1')?><br/><br/>
			</dt>
			<dd>
			<?php if($message){?>
			<?php echo $message;?>
			<?php }else{?>
				<select name="organization_id" id="organization_id" onchange="javascript: get_activities();">
					<option value="">Ingen organisasjon valgt</option>
					<?php
					foreach($organizations as $organization)
					{
						echo "<option value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
					}
					?>
				</select>
				<br/><br/>
			</dd>
			<dt>
				<?php echo lang('activity_edit_helptext');?><br/><br/>
			</dt>
			<dd>
				<div id="activity_select">
					<select name="activity_id" id="activity_id">
						<option value="0">Ingen aktivitet valgt</option>
					</select>
				</div>
				<br/><br/>
			<?php }?>
			</dd>
			<?php if(!$message){?>
			<div class="form-buttons">
				<input type="submit" name="step_1" value="<?php echo lang('send_change_request') ?>" onclick="return isOK();"/>
			</div>
			<?php }?>
		</dl>
		
	</form>
</div>