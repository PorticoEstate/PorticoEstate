<script>
function changeRepo(SBRepo)
{
	parent.changeRepo(SBRepo.options[SBRepo.selectedIndex].value);
	SBRepo.form.submit();
}

function changeCategory(SBCategory)
{
	parent.changeCat(SBCategory.options[SBCategory.selectedIndex].value, document.repository.prefilter.value);
}
</script>

<style type="text/css">
	td.pbaddressbookCategoriesSB select { width: 230px } 
</style>


<div class="bg_color1" style="width: 250px; height: 450px">
<form method="POST" name="repository">
	<table>
		<tr>
			<td align="center">
				{SBRepos}
			</td>
		</tr>		
		<tr>
			<td style="height: 40px; vertical-align: center">
				<input type="text" id="prefilter" name="prefilter" />&nbsp;{prefilter}
			</td>
		</tr>
		<tr>
			<td class="pbaddressbookCategoriesSB">
				{categories}
			</td>
		</tr>
	</table>
</form>
</div>