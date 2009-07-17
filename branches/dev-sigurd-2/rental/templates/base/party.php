<?php
	//include common logic for all templates 
	include("common.php");	
?>

<script type="text/javascript">
	//javascript specific for this template
	
</script>



<h3><?= lang('rental_common_party') ?>: <?= $party->get_name() ?></h3>

<form action="#" method="post">
	<div id="details">
		<dl class="proplist-col">
			<dt>
				<label for="personal_identification_number"><?= lang('rental_party_ssn') ?> / <?= lang('rental_party_organisation_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="personal_identification_number" id="personal_identification_number" value="<?= $party->get_personal_identification_number() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_personal_identification_number();
				}
				?>
			</dd>
			<dt>
				<label for="firstname"><?= lang('rental_common_firstname') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="firstname" id="firstname" value="<?= $party->get_first_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_first_name();
				}
				?>
			</dd>
			<dt>
				<label for="lastname"><?= lang('rental_common_lastname') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="lastname" id="lastname" value="<?= $party->get_last_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_last_name();
				}
				?>
			</dd>
			<dt>
				<label for="title"><?= lang('rental_common_title') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="title" id="title" value="<?= $party->get_title() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_title();
				}
				?>
			</dd>
			<dt>
				<label for="company_name"><?= lang('rental_common_company') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="company_name" id="company_name" value="<?= $party->get_company_name() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_company_name();
				}
				?>
			</dd>
			<dt>
				<label for="department"><?= lang('rental_common_department') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="department" id="department" value="<?= $party->get_department() ?>" />
				<?php 
				}
				else
				{
					echo $party->get_department();
				}
				?>
			</dd>
			<dt>
				<label for="address1"><?= lang('rental_rc_address') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="address1" id="address1" value="<?= $party->get_address_1() ?>" />
					<br/>
					<input type="text" name="address2" id="address2" value="<?= $party->get_address_2() ?>" />
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
				<label for="postal_code"><?= lang('rental_common_postal_code_place') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="postal_code" id="postal_code" value="<?= $party->get_postal_code() ?>" />
					<input type="text" name="place" id="place" value="<?= $party->get_place() ?>" />
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
				<label for="phone"><?= lang('rental_common_phone') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="phone" id="phone" value="<?= $party->get_phone() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_phone();
				}
				?>
			</dd>
			<dt>
				<label for="fax"><?= lang('rental_common_fax') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="fax" id="fax" value="<?= $party->get_fax() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_fax();
				}
				?>
			</dd>
			<dt>
				<label for="email"><?= lang('rental_common_email') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="email" id="email" value="<?= $party->get_email() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_email();
				}
				?>
			</dd>
			<dt>
				<label for="url"><?= lang('rental_common_url') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="url" id="url" value="<?= $party->get_url() ?>" />
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
				<label for="type_id"><?= lang('rental_common_party_type') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="type_id" id="type_id" value="<?= $party->get_type_id() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_type_id();
				}
				?>
				<!-- TODO:
				<select name="type_id" id="type_id">
					<xsl:if test="../access = 0"><xsl:attribute name="disabled" value="true"/></xsl:if>
					<option value="internal"><xsl:value-of select="php:function('lang', 'rental_party_internal')"/></option>
					<option value="external"><xsl:value-of select="php:function('lang', 'rental_party_external')"/></option>
				</select>
				 -->
			</dd>
			<dt>
				<label for="post_bank_account_number"><?= lang('rental_common_post_bank_account_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="post_bank_account_number" id="post_bank_account_number" value="<?= $party->get_post_bank_account_number() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_post_bank_account_number();
				}
				?>
			</dd>
			<dt>
				<label for="account_number"><?= lang('rental_common_account_number') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="account_number" id="account_number" value="<?= $party->get_account_number() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_account_number();
				}
				?>
			</dd>
			<dt>
				<label for="reskontro"><?= lang('rental_common_reskontro') ?></label>
			</dt>
			<dd>
				<?php 
				if ($editable) 
				{
				?>
					<input type="text" name="reskontro" id="reskontro" value="<?= $party->get_reskontro() ?>" />
				<?php 
				}
				else
				{	
					echo $party->get_reskontro();
				}
				?>
			</dd>
			<dt>
				<label for="is_active"><?= lang('rental_party_active') ?></label>
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
	<div id="party_edit_tabview" class="yui-navset">
		<ul class="yui-nav">
			<li class="selected"><a href="#rental_rc_contracts"><em><?= lang('rental_common_contracts') ?></em></a></li>
			<li><a href="#rental_rc_documents"><em><?= lang('rental_common_documents') ?></em></a></li>
			<li><a href="#rental_rc_comments"><em><?= lang('rental_common_comments') ?></em></a></li>
		</ul>
		
		<div class="yui-content">
			<div id="contracts">
				<?php 
				$url_add_on = "&amp;type=contracts_part&amp;party_id=".$party->get_id();
				include('contract_list_partial.php');
				?>
			</div>
			<div id="documents">
			</div>
			<div id="comments">
			</div>
		</div>
	</div>
	<div class="form-buttons">
		<?php
			if ($editable) {
				echo '<input type="submit" name="save_party" value="' . lang('rental_rc_save') . '"/>';
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_cancel') . '</a>';
			} else {
				echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_back') . '</a>';
			}
		?>
	</div>
</form>

