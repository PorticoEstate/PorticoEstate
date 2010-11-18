<?php

	$antall = 2000;
	echo "forventet: {$antall}</br>";
	if(isset($_POST) && $_POST)
	{
		echo 'totalt: ' . count($_POST['values']) . '</br>';

		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
	}

	$html = <<<HTML
	<html>
		<body>
			<form  method="post" action="">
			<table>
			<tr>
			<td>
				<input type="submit" value="send" />
			</td>
			</tr>
HTML;


	for ($i=0;$i<$antall;$i++)
	{
		$html .= <<<HTML
		<tr>
			<td align="left"><input type="checkbox" name="values[]" value="1" checked="checked"></td>
		</tr>
HTML;
	}

	$html .= <<<HTML
			</table>
			</form>
		</body>
	</html>
HTML;
		echo $html;
?>
