<?php
	//include common logic for all templates
	include("common.php");
?>

<script type="text/javascript">

function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	url = "index.php?menuaction=activitycalendar.uiarena.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"address\" id=\"address\" size\"5\">";
var divcontent_end = "</select>";
var divcontent_number = "&nbsp;&nbsp;<label for=\"address_number\"><?php echo lang('address_number') ?></label><input type=\"text\" name=\"address_no\" id=\"address_no\" size=\"6\"/>"
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end + divcontent_number; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}
</script>
<?php echo activitycalendar_uicommon::get_page_message($message) ?>
<div class="identifier-header">
	<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('arena') ?></h1>
	<div>
		<label><?php echo lang('name'); ?></label>
		 <?php if($arena->get_arena_name()){ echo $arena->get_arena_name(); } else { echo lang('no_value'); }?>
	</div>
</div>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($arena->get_id()){ echo $arena->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt><?php if($editable){
						echo lang('arena_helptext');
						}
				?>
				</dt>
				<dt>
					<?php if($arena->get_arena_name() || $editable) { ?>
					<label for="name"><?php echo lang('name') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="arena_name" id="arena_name" value="<?php echo $arena->get_arena_name() ?>" />
					<?php
					}
					else
					{
						echo $arena->get_arena_name();
					}
					?>
				</dd>
				<dt>
					<?php if($arena->get_address() || $editable) { ?>
					<label for="address"><?php echo lang('address') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
					 	<input type="text" name="address" id="address_txt" value="<?php echo $arena->get_address() ?>" onkeyup="javascript:get_address_search()"/>
					 	<div id="address_container"></div>
					<?php
					}
					else
					{
						echo $arena->get_address();
					}
					?>
				</dd>
				<?php if($editable) {?>
					<dt>
						<label for="arena_active"><?php echo lang('active_arena') ?></label>
					</dt>
					<dd>
						<select name="arena_active" id="arena_active">
							<option value="yes" <?php if($arena->is_active()) { echo "selected";} ?>><?php echo lang('active')?></option>
							<option value="no" <?php if(!$arena->is_active()) { echo "selected";} ?>><?php echo lang('inactive')?></option>
						</select>
					</dd>
				<?php 
				}else{ 
				?>
					<dt><label><?php if($arena->is_active()){?><font style="color: green;"><?php echo lang('active_arena');?></font><?php }else{ ?><font style="color: red;"><?php echo lang('inactive_arena');?></font><?php } ?></label></dt>
					<dd>&nbsp;</dd>
				<?php }?>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_arena" value="' . lang('save') . '"/>';
						echo '<a href="'.$cancel_link.'">' . lang('cancel') . '</a>';
					}
					else
					{
						echo '<input type="submit" name="edit_arena" value="' . lang('edit') . '"/>';
						echo '<a href="'.$cancel_link.'">' . lang('back') . '</a>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>

