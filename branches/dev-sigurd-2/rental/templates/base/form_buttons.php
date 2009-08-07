<div class="form-buttons">
	<?php
		if ($editable) {
			echo '<input type="submit" name="save" value="' . lang('rental_common_party') . '"/>';
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_cancel') . '</a>';
		} else {
			echo '<a class="cancel" href="' . $cancel_link . '">' . lang('rental_common_back') . '</a>';
		}
	?>
</div>