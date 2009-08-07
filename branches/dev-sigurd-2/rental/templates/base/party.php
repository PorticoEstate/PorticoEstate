<?php
	//include common logic for all templates 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');	
	phpgwapi_yui::tabview_setup('party_edit_tabview');
?>

<script type="text/javascript">
	//javascript specific for this template
	
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/custom/contact.png" /><em> <?php echo lang('rental_common_party') ?>: <?php echo $party->get_name() ?></em></h1>

<form action="#" method="post">
	
	<div id="party_edit_tabview" class="yui-navset">
		<ul class="yui-nav">
			<li class="selected"><a href="#details_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/custom/contact.png" alt="icon" /> <?php echo lang('rental_common_details') ?></em></a></li>
			<li><a href="#contracts_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/mimetypes/text-x-generic.png" alt="icon" /> <?php echo lang('rental_common_contracts') ?></em></a></li>
			<li><a href="#documents_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/system-file-manager.png" alt="icon" /> <?php echo lang('rental_common_documents') ?></em></a></li>
			<li><a href="#comments_party"><em><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/16x16/apps/internet-group-chat.png" alt="icon" /> <?php echo lang('rental_common_comments') ?></em></a></li>
		</ul>
		
		<div class="yui-content">
			<div id="details">
		<dl class="proplist-col">
			<dt>
				<label for="personal_identification_number"><?php echo lang('rental_common_ssn') ?> / <?php echo lang('rental_common_organisation_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="personal_identification_number" id="personal_identification_number" value="<?php echo $party->get_personal_identification_number() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_personal_identification_number();
				}
				?>
			</dd>
			<dt>
				<label for="firstname"><?php echo lang('rental_common_firstname') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="firstname" id="firstname" value="<?php echo $party->get_first_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_first_name();
				}
				?>
			</dd>
			<dt>
				<label for="lastname"><?php echo lang('rental_common_lastname') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="lastname" id="lastname" value="<?php echo $party->get_last_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_last_name();
				}
				?>
			</dd>
			<dt>
				<label for="title"><?php echo lang('rental_common_job_title') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="title" id="title" value="<?php echo $party->get_title() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_title();
				}
				?>
			</dd>
			<dt>
				<label for="company_name"><?php echo lang('rental_common_company') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="company_name" id="company_name" value="<?php echo $party->get_company_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_company_name();
				}
				?>
			</dd>
			<dt>
				<label for="department"><?php echo lang('rental_common_department') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="department" id="department" value="<?php echo $party->get_department() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_department();
				}
				?>
			</dd>
			<dt>
				<label for="address1"><?php echo lang('rental_common_address') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="address1" id="address1" value="<?php echo $party->get_address_1() ?>" />
					<br/>
					<input type="text" name="address2" id="address2" value="<?php echo $party->get_address_2() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_address_1();
					echo "<br/>";
					echo $party->get_address_2();
				}
				?>
			</dd>
			<dt>
				<label for="postal_code"><?php echo lang('rental_common_postal_code_place') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="postal_code" id="postal_code" class="postcode" value="<?php echo $party->get_postal_code() ?>" />
					<input type="text" name="place" id="place" value="<?php echo $party->get_place() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_postal_code();
					echo $party->get_place();
				}
				?>
			</dd>
			<dt>
				<label for="phone"><?php echo lang('rental_common_phone') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="phone" id="phone" value="<?php echo $party->get_phone() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_phone();
				}
				?>
			</dd>
			<dt>
				<label for="fax"><?php echo lang('rental_common_fax') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="fax" id="fax" value="<?php echo $party->get_fax() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_fax();
				}
				?>
			</dd>
			<dt>
				<label for="email"><?php echo lang('rental_common_email') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="email" id="email" value="<?php echo $party->get_email() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_email();
				}
				?>
			</dd>
			<dt>
				<label for="url"><?php echo lang('rental_common_url') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="url" id="url" value="<?php echo $party->get_url() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_url();
				}
				?>
			</dd>
		</dl>
		<dl class="proplist-col">
			<dt>
				<label for="post_bank_account_number"><?php echo lang('rental_common_post_bank_account_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="post_bank_account_number" id="post_bank_account_number" value="<?php echo $party->get_post_bank_account_number() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_post_bank_account_number();
				}
				?>
			</dd>
			<dt>
				<label for="account_number"><?php echo lang('rental_common_account_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="account_number" id="account_number" value="<?php echo $party->get_account_number() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_account_number();
				}
				?>
			</dd>
			<dt>
				<label for="reskontro"><?php echo lang('rental_common_account') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="reskontro" id="reskontro" value="<?php echo $party->get_reskontro() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_reskontro();
				}
				?>
			</dd>
			<dt>
				<label for="is_active"><?php echo lang('rental_common_active_party') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="checkbox" name="is_active" id="is_active" disabled="false" <?php if($party->is_active()) { echo "checked='checked'";} ?>/>
				<?php 
				}
				else
				{	
				?>
					<input type="checkbox" name="is_active" id="is_active" disabled="true" <?php if($party->is_active()) { echo "checked='checked'";} ?> />
				<?php 
				}
				?>
			</dd>
		</dl>
	</div>
			<div id="rental_common_contracts">
				<?php 
				$list_form = false;
				$list_id = 'contracts_part';
				$url_add_on = "&amp;type=contracts_part&amp;party_id=".$party->get_id();
				include('contract_list_partial.php');
				?>
			</div>
			<div id="rental_common_documents">
				Documents
			</div>
			<div id="rental_common_comments">
			Comments
			</div>
		</div>
	</div>
	<div class="form-buttons">
		<?php
			if ($editable) {
				echo '<input type="submit" name="save_party" value="' . lang('rental_common_party') . '"/>';
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_cancel') . '</a>';
			} else {
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_back') . '</a>';
			}
		?>
	</div>
</form>

