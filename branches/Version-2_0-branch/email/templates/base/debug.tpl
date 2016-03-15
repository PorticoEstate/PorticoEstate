<!-- begin debug.tpl -->

<style type="text/css">
<!--
	PRE, CODE
	{
		margin-top: 4px;
		margin-right: 4px;
		margin-left: 4px;
		margin-bottom: 4px;
		
		/*font-family: "Courier New", Courier, fixed;*/
		/*font-family: "lucida console", Courier, fixed;*/
		font-family: "lucida", Courier, fixed;
		
		/*font-size: 0.8em;*/
		font-size: 0.9em;
		/*font-size: 1.0em;*/
		
		/* font-weight: bold;*/
	}
-->
</style>

<!-- BEGIN B_before_echo -->
<table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">
<tr>
	<td width="100%">
		<h2>{page_desc}</h2>
		Pick a desired function.
		<ul>
			<li>= = Enviornment Data = =</li>
			<li>{func_E1}</li>
			<li>{func_E2}</li>
			<li>{func_E3}</li>
			<li>= = Info Dumps = =</li>
			<li>{func_D1}</li>
			<li>{func_D2}</li>
			<li>{func_D3}</li>
			<li>{func_D4}</li>
			<li>{func_D5}</li>
			<li>{func_D6}</li>
			<li> = = Inline Docs = =</li>
			<li>{func_I1}</li>
			<li>{func_I2}</li>
			<li>{func_I3}</li>
			<li>{func_I4}</li>
			<li>{func_I5}</li>
			<li>{func_I6}</li>
			<li>{func_I7}</li>
			<li> = = Other Stuff = =</li>
			<li>{func_O1}</li>
			<li>{func_O2}</li>
			<li>{func_O3}</li>
			<li>{func_O4}</li>
			<li>{func_O5}</li>
			<li>{func_O6}</li>
		</ul>
		<br />
		This seta up an echo dump:
	</td>
</tr>
</table>
<!-- END B_before_echo -->

<br />
<!-- if using debug popup window that js will follow here -->
{debugdata}

<!-- BEGIN B_after_echo -->
<table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">
<tr>
	<td width="100%">
		<h2>echo done</h2>
		
	</td>
</tr>
</table>
<!-- END B_after_echo -->

<!-- end debug.tpl -->
