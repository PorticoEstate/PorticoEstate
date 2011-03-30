<?php
?>
<table>
	<tr>
		<th>Navn</th><th>bydel</th><th>kategori</th><th>målgruppe</th><th>arena</th><th>kontor</th><th>epost</th><th>dato oppdatert</th>
	</tr>
	<tr>
		<td>test</td><td>Fana</td><td>idrett</td><td>alle</td><td>Fana bydelshus</td><td>Fana</td><td>test@test.no</td><td>2011-03-24</td>
	</tr>
</table>
<hr/>

<div class="toolbar-container"><div class="toolbar"><form method="POST" action="/pe/index.php?menuaction=property.uilocation.index&amp;type_id=1&amp;district_id=&amp;part_of_town_id=&amp;cat_id=&amp;click_history=ed9d0b13fdf51556bfabd136e6d73aee">
<div style="float:left" class="field"><input id="btn_cat_id" type="button" name="cat_id" value="Kategori" class="button" tabindex="1"></div>
<div style="float:left" class="field"><input id="btn_district_id" type="button" name="district_id" value="Område" class="button" tabindex="2"></div>

<div style="float:left" class="field"><input id="btn_part_of_town_id" type="button" name="part_of_town_id" value="Bydel" class="button" tabindex="3"></div>
<div style="float:left" class="field"><input id="btn_owner_id" type="button" name="owner_id" value="Filter" class="button" tabindex="4"></div>
<div style="float:right" class="field"><a id="btn_columns" href="#" onclick="Javascript:window.open('/pe/index.php?menuaction=property.uilocation.columns&amp;type_id=1&amp;click_history=ed9d0b13fdf51556bfabd136e6d73aee','','width=300,height=600,scrollbars=1')" tabindex="9">kolonner</a></div>
<div style="float:right" class="field"><input id="btn_export" type="button" name="" value="Last ned" class="button" tabindex="8"></div>
<div style="float:right" class="field"><input id="type_id" type="hidden" name="" value="1" class="hidden"></div>
<div style="float:right" class="field"><input id="btn_search" type="button" name="search" value="Søk" class="button" tabindex="6"></div>
<div style="float:right" class="field"><input id="txt_query" type="text" name="query" value="" class="text" size="28" tabindex="5" onkeypress="return pulsar(event)"></div>
<div style="float:right" class="field"><input id="btn_new" type="button" name="" value="Legg til" class="button" tabindex="7"></div>
</form></div></div><script type="text/javascript">
					function Exchange_values(data)
					{

					}
				</script><br><div id="message"></div><div id="paging"></div><div class="datatable-container"></div><div id="datatable-detail" style="background-color:#000000;color:#FFFFFF;display:none">
<div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center"> Record Detail </div>
<div class="bd" style="text-align:center;"></div>
</div><div id="footer"></div>
<script type="text/javascript">
		var allow_allrows = "1";

  		var property_js = "/pe/property/js/yahoo/property.js";

		var base_java_url = "{menuaction:'property.uilocation.index',type_id:'1',query:'',district_id: '',part_of_town_id:'',lookup:'',second_display:1,lookup_tenant:'',lookup_name:'',cat_id:'',status:'',location_code:'',block_query:''}";
 
		
  				var json_data = {"recordsReturned":"10","totalRecords":626,"startIndex":0,"sort":"loc1","dir":"asc","records":[],"integrationurl":"","hidden":{"dependent":[{"id":"","value":"#!no part of town@1#ARNA BYDEL@7#\u00c5RSTAD BYDEL@8#\u00c5SANE BYDEL@2#BERGENHUS BYDEL@3#FANA BYDEL@4#FYLLINGSDALEN BYDEL@5#LAKSEV\u00c5G  BYDEL@9#\u00d8VRIGE@6#YTREBYGDA  BYDEL@"}]},"rights":[{"my_name":"view","text":"Kontrakter","action":"\/pe\/index.php?menuaction=rental.uicontract.index&search_type=location_id&contract_status=all&populate_form=yes&click_history=ed9d0b13fdf51556bfabd136e6d73aee","parameters":{"parameter":[{"name":"search_for","source":"location_code"}]}},{"my_name":"view","text":"Vis","action":"\/pe\/index.php?menuaction=property.uilocation.view&click_history=ed9d0b13fdf51556bfabd136e6d73aee","parameters":{"parameter":[{"name":"location_code","source":"location_code"}]}},{"my_name":"view","text":"\u00c5pne visning i nytt vindu","action":"\/pe\/index.php?menuaction=property.uilocation.view&target=_blank&click_history=ed9d0b13fdf51556bfabd136e6d73aee","parameters":{"parameter":[{"name":"location_code","source":"location_code"}]}}]};
			

		var myColumnDefs = [
			
				{
					key: "location_code",
					label: "dummy",
					resizeable:true,
					sortable: false,
					visible: false,
					format: "hidden",
					formatter: "",
					source: "",
					className: ""
				},
				{
					key: "loc1",
					label: "Eiendom",
					resizeable:true,
					sortable: true,
					visible: true,
					format: "number",
					formatter: "",
					source: "fm_location1.loc1",
					className: ""
				},
				{
					key: "loc1_name",
					label: "Eiendom Navn",
					resizeable:true,
					sortable: false,
					visible: true,
					format: "varchar",
					formatter: "",
					source: "",
					className: ""
				},
				{
					key: "adresse1",
					label: "Adresse1",
					resizeable:true,
					sortable: true,
					visible: true,
					format: "varchar",
					formatter: "",
					source: "adresse1",
					className: ""
				},
				{
					key: "postnummer",
					label: "Postnummer",
					resizeable:true,
					sortable: true,
					visible: true,
					format: "number",
					formatter: "",
					source: "postnummer",
					className: ""
				},
				{
					key: "poststed",
					label: "Poststed",
					resizeable:true,
					sortable: true,
					visible: true,
					format: "varchar",
					formatter: "",
					source: "poststed",
					className: ""
				}
		];

		var values_combo_box = [
			
				{
					id: "values_combo_box_0",
					value: "#Kategori ikke valgt@1#ADM-BYGG@11#AN01 - ANDRE BYGG@12#AN02 - ANDRE BYGG@9#ANNEN EIENDOM @2#BARNEHAGE@13#BO01 - FESTET GRUNN@7#BRANNSTASJON@14#BY01- BYFJELLENE@23#FACILIT-IMPORT@15#FR01 - FRIAREAL@16#GÅ01 - GÅRDBRUK@4#HELSE@5#IDRETT@10#INNLEIEOBJEKT@6#KULTUR@8#OFF. TILFLUKTSROM@22#ØY01 - ØYRANE@17#PA01 - PARKERINGSPLASSER@18#SJ01 - SJØGRUNN@3#SKOLE@99#SOLGT/SAMMENFØYDE@19#TO01 - TOMT@20#TR01 - TRANSFORMATORKIOSK @21#VE01 - VEIGRUNN@"
				},
				{
					id: "values_combo_box_1",
					value: "#Distrikt ikke valgt@1#Bergenhus/Årstad@2#Arna/Åsane@3#Fana/Ytrebygda@4#Laksevåg/Fyllingen@5#Øvrige@"
				},
				{
					id: "values_combo_box_2",
					value: "#Bydel ikke valgt@1#ARNA BYDEL@7#ÅRSTAD BYDEL@8#ÅSANE BYDEL@2#BERGENHUS BYDEL@3#FANA BYDEL@4#FYLLINGSDALEN BYDEL@5#LAKSEVÅG  BYDEL@9#ØVRIGE@6#YTREBYGDA  BYDEL@"
				},
				{
					id: "values_combo_box_3",
					value: "#vis alle@1#BKB@2#Ekstern@"
				}
		];


	</script>