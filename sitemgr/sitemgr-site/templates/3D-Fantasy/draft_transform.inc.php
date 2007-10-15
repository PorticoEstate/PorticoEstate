<?php 
class draft_transform
{
	function apply_transform($title,$content)
	{
		return '
<div class="draft">' .
				$content . '
</div>';
	}
}