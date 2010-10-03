<script>
function show_contactdetails(select)
{
	if(document.contacts.enableDetails.checked)
	{
		parent.show_contactdetails(select.options[select.selectedIndex].value, document.contacts.repository.value, document.contacts.category.value);
	}
}

function selectAll()
{
	entries = document.contacts.contacts.options;
	for(var i = 0; i < entries.length; i++)
	{
		entries[i].selected = true;
	}
}

function addcontact(mode)
{
	entries = document.contacts.contacts.options;
	for(var i = 0; i < entries.length; i++)
	{
		if(entries[i].selected)
		{
			parent.addContact(mode, entries[i]);
		}
	}
}

function pbquicksearch(str)
{
	if(str != '')
	{
		pattern = new RegExp ('^' + str, 'i');
		selectbox = document.contacts.contacts.options;
		
		for (var i = 0; i < this.selectbox.length; i++)
		{
			if(!pattern.test(this.selectbox[i].text))
			{
				//this.selectbox[i].style.visibility = 'hidden';
				document.contacts.contacts.remove(this.selectbox[i].index);
				i--;
			}
			else
			{
				//this.selectbox[i].style.visibility = 'visible';
			}
		}
	}
    //if (pattern1.test (this.selectArr[i].text))
      //document.forms[this.formname][this.selname].options[j++] =
	//this.selectArr[i];
  //document.forms[this.formname][this.selname].options.length = j;
}
</script>
<style type="text/css">
td.pbaddressbookContactsSB select { width: 200px } 
</style>
<div class="bg_color1" style="height: 350px">
<form method="POST" name="contacts">
	<input name="repository" type="hidden" value="{repository}" />
	<input name="category" type="hidden" value="{category}" />
	<input name="prefilter" type="hidden" value="{prefilter}" />
	<table style="width: 250px; border: 0px solid #000000">
		<tr>
			<td colspan="2">
<!--				<input type="text" name="quicksearch" onKeyup="pbquicksearch(this.value)"/> -->
			</td>
		</tr>
		<tr>
			<td class="pbaddressbookContactsSB">
				{contacts}
			</td>
			<td>
				<table>
					<tr>
						<td>
							<input name="to" type="button" value="an" onclick="addcontact(this.name)" />
						</td>
					</tr>
					<tr>
						<td>
							<input name="cc" type="button" value="cc" onclick="addcontact(this.name)" />
						</td>
					</tr>
					<tr>
						<td>
							<input name="bcc" type="button" value="bcc" onclick="addcontact(this.name)" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="button" name="all" value="{all}" onclick="selectAll()" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="checkbox" name="enableDetails" value="1" checked />
				{enable_preview}
			</td>
		</tr>
	</table>
</form>
</div>
<script>
if(parent.document.getElementById('tr_to').style.visibility == 'hidden')
{
	document.contacts.to.style.visibility = 'hidden';
}
if(parent.document.getElementById('tr_cc').style.visibility == 'hidden')
{
	document.contacts.cc.style.visibility = 'hidden';
}
if(parent.document.getElementById('tr_bcc').style.visibility == 'hidden')
{
	document.contacts.bcc.style.visibility = 'hidden';
}
</script>