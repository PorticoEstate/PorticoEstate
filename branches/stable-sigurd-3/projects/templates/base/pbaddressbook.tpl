<script>
function changeRepo(repo)
{
	contactsform = document.getElementById('contactsframe').contentWindow.document.contacts;
	contactsform.repository.value = repo;
}

function changeCat(cat, preFilter)
{
	document.main.category.value = cat;
	if(!document.getElementById('contactsframe').contentWindow)
	{
		contactsform = document.getElementById('contactsframe').document.contacts;
	}
	else
	{
		contactsform = document.getElementById('contactsframe').contentWindow.document.contacts;
	}
	contactsform.category.value = cat;
	contactsform.prefilter.value = preFilter;
	contactsform.submit();
}

function show_contactdetails(id, repository, category)
{
	cdform = document.getElementById('contactsDetailframe').contentWindow.document.contactsdetail;
	cdform.id.value = id;
	cdform.repository.value = repository;
	cdform.category.value = category;
	cdform.submit();
}

function addContact(mode, optionelement)
{
	repository = document.getElementById('contactsframe').contentWindow.document.contacts.repository.value;
	newEntry = new Option(optionelement.text,repository+'_'+optionelement.value,false,false);
	if(mode == 'to')
	{
		document.main.to.options[document.main.to.length] = newEntry;
	}
	if(mode == 'cc')
	{
		document.main.cc.options[document.main.cc.length] = newEntry;
	}
	if(mode == 'bcc')
	{
		document.main.bc.options[document.main.bc.length] = newEntry;
	}
}

function delContact()
{
	SBTO = document.main.to;
  for ( var j=0 ; j < SBTO.length; j++ )
  {
		if(SBTO.options[j].selected)
		{
			SBTO.remove(SBTO.options[j].index);
			j--;
		}
	}
	SBTO = document.main.cc;
  for ( var j=0 ; j < SBTO.length; j++ )
  {
		if(SBTO.options[j].selected)
		{
			SBTO.remove(SBTO.options[j].index);
			j--;
		}
	}
	SBTO = document.main.bc;
  for ( var j=0 ; j < SBTO.length; j++ )
  {
		if(SBTO.options[j].selected)
		{
			SBTO.remove(SBTO.options[j].index);
			j--;
		}
	}
}

function selectAllOptions()
{
  elements = document.getElementsByTagName('Select');
  for ( var j=0 ; j < elements.length; j++ )
  {
    if ( elements.item(j).size > 1 ) {
      for (var i=0; i < elements.item(j).options.length; i++) {
        elements.item(j).options[i].selected = true;
      }
    }
  }
  return true;
}

 document.body.style.overflow='visible';
 
</script>
<form method="POST" name="main" onSubmit="selectAllOptions()">
	<input name="category" id="category" type="hidden" value="" />
	<table align="center">
		<thead class="header">
			<tr>
				<td>
					{l_select_repository}
				</td>
				<td>
					{l_select_contact}
				</td>
				<td>
					{l_edit_recipients}
				</td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3" class="header" style="height: 10px">
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td class="bg_color1" style="height: 450px;">
					<iframe id="repositoryframe" name="repositoryframe" src="{link_repositoryframe}" frameborder="0" style="height: 100%; width: 250px; border: 0px solid #FFFFFF; margin-top: 15px" scrolling="No">Error loading frame</iframe>
				</td>
				<td class="bg_color1" style="height: 450px;">
					<table class="noCollapse" style="height: 100%; width: 100%">
						<tr>
							<td style="height: 100%;">
								<iframe id="contactsframe" name="contactsframe" src="{link_contactsframe}" frameborder="0" style="height: 100%; width: 250px; border: 0px solid #FFFFFF; margin-top: 15px" scrolling="No">
								</iframe>
							</td>
						</tr>
						<tr>
							<td style="height: 100px;" align="center">
								<iframe id="contactsDetailframe" name="contactsDetailframe" src="{link_contactdetailsframe}" frameborder="0" style="height: 105px; width: 200px; border: 1px solid #FFFFFF; border-collapse: collapse; margin: 0px; padding: 0px" scrolling="No">
								</iframe>
							</td>
						</tr>
					</table>
				</td>
				<td class="bg_color1" style="height: 400px; width: 250px;">
					<table style="width: 240px" align="center">
						<tr style="visibility: {to_visibility}">
							<td colspan="2">
								{to}
							</td>
						</tr>
						<tr id="tr_to" style="visibility: {to_visibility}">
							<td colspan="2">
								<select size="7" id="to" name="to[]" style="min-width: 150px" multiple>
								</select>
							</td>
						</tr>
						<tr style="visibility: {cc_visibility}">
							<td colspan="2">
								{cc}
							</td>
						</tr>
						<tr id="tr_cc" style="visibility: {cc_visibility}">
							<td colspan="2">
								<select size="7" id="cc" name="cc[]" style="min-width: 150px" multiple>
								</select>
							</td>
						</tr>
						<tr style="visibility: {bcc_visibility}">
							<td colspan="2">
								{bcc}
							</td>
						</tr>
						<tr id="tr_bcc" style="visibility: {bcc_visibility}">
							<td colspan="2">
								<select size="7" id="bc" name="bc[]" style="min-width: 150px" multiple>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<input type="button" value="Entfernen" onclick="delContact()" />
							</td>
							<td>
								<input type="button" value="Abbrechen" onclick="window.close()" />
							</td>
							<td align="right">
								<input type="submit" value="Speichern" name="save"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>    
	</table>
</form>