<div class="form-buttons">
	<?php
		if ($editable) {
			echo '<input type="submit" name="save" value="' . lang('rental_rc_save') . '"/>';
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_cancel') . '</a>';
		} else {
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_rc_back') . '</a>';
		}
	?>
</div>