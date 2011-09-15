<?php 	
	//include common logic for all templates
	include("common.php");
?>

<div class="identifier-header">
<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/actions/go-home.png" /> <?php echo lang('Procedure') ?></h1>
</div>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value="<?php if(!empty($procedure)){ echo $procedure->get_id(); } else { echo '0'; }  ?>"/>
				<dl class="proplist-col">
					<dt>
						<label for="title"><?php echo lang('Title') ?></label>
					</dt>
					<dd>
						<input type="text" name="title" id="title" value="" />
					</dd>
					<dt>
						<label for="purpose"><?php echo lang('Purpose') ?></label>
					</dt>
					<dd>
						<textarea id="purpose" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="responsibility"><?php echo lang('Responsibility') ?></label>
					</dt>
					<dd>
						<textarea id="responsibility" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="description"><?php echo lang('Description') ?></label>
					</dt>
					<dd>
						<textarea id="description" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="reference"><?php echo lang('Reference')?></label>
					</dt>
					<dd>
						<input type="text" name="reference" id="reference" value="" />
					</dd>	
					<dt>
					<label for="attachment"><?php echo lang('Attachment')?></label>
					</dt>
					<dd>
						<input type="text" name="attachment" id="attachment" value="" />
					</dd>			
				</dl>
				
				<div class="form-buttons">
					<?php
						echo '<input type="submit" name="save_procedure" value="' . lang('save') . '"/>';
					?>
				</div>
				
			</form>
						
		</div>
</div>