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

<div class="yui-content" style="width: 100%;">
	<h1><?php echo lang('edit_organization') ?></h1>
	<div id="details">

		<?php if ($message) { ?>
			<div class="success">
				<?php echo $message; ?>
			</div>
		<?php } else if ($error) { ?>
			<div class="error">
				<?php echo $error; ?>
			</div>
		<?php } ?>
	</div>
</div>
</div>