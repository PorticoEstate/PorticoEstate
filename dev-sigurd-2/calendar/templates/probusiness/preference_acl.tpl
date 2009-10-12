
  {errors}
  {title}

	<table align="center">
	 <tr>
  	{nml}
	  <td>
  	 <div class="center">
    	<form method="POST" action="{action_url}">
	     {common_hidden_vars}
  	   <input type="text" name="query" value="{search_value}" />
    	 <input type="submit" name="search" value="{search}" />
	    </form>
  	 </div>
	  </td>
  	{nmr}
	 </tr>
	</table>
	<form method="POST" action="{action_url}">
  		<table align="center">
		  	<tr><td class="center">
			  	<table cols="4">
			     {row}
 					</table>
				</td></tr>
			</table>
		{common_hidden_vars_form}
 		<input type="hidden" name="processed" value="{processed}" />
 		<div class="center">
 			<input type="submit" name="submit" value="{submit_lang}" />
 		</div>
	</form>

