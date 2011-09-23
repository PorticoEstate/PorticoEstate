<div class="form-buttons">
	<?php
		if ($editable) {
			echo '<input type="submit" name="save" value="' . lang('save') . '"/>';
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('cancel') . '</a>';
		} else {
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('back') . '</a>';
		}
	?>
</div>