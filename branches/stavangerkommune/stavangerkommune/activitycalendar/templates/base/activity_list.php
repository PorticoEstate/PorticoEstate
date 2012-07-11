<?php
	include("common.php");
?>

<script type="text/javascript">

	YAHOO.util.Event.addListener(
		'ctrl_add_activitycalendar_activity',
		'click',
		function(e)
		{
            YAHOO.util.Event.stopEvent(e);
            window.location = 'index.php?menuaction=activitycalendar.uiactivities.add';
        }
   );
</script>

<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('activities') ?></h1>

<fieldset>
	<input type="submit" name="ctrl_add_activitycalendar_activity" id="ctrl_add_activitycalendar_activity" value="<?php echo lang('f_new_activity') ?>" />
</fieldset>


<?php
 	$list_form = true;
	$list_id = 'all_activities';
	$url_add_on = '&amp;type=all_activities';
	include('activity_list_partial.php');
?>
<!-- 
<hr/>
<div style="float:left" class="field"><input id="btn_cat_id" type="button" name="cat_id" value="Kategori" class="button" tabindex="1"></div>
<div style="float:left" class="field"><input id="btn_district_id" type="button" name="district_id" value="Område" class="button" tabindex="2"></div>

<div style="float:left" class="field"><input id="btn_part_of_town_id" type="button" name="part_of_town_id" value="Bydel" class="button" tabindex="3"></div>
<div style="float:left" class="field"><input id="btn_owner_id" type="button" name="owner_id" value="Filter" class="button" tabindex="4"></div>
<script type="text/javascript">
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
-->