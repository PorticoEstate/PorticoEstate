<dt>
	Organisasjonstilknytning
</dt>
<dd>
	<?php
		if ($editable)
		{
			?>
			<select name="org_enhet_id" id="org_enhet_id">
				<option value=""><?php echo lang('no_party_location')?></option>
				<?php
				$result_units = rental_bofellesdata::get_instance()->get_result_units();
				$party_org_enhet_id = $party->get_org_enhet_id();
				foreach ($result_units as $result_unit)
				{
					if ($result_unit['ORG_UNIT_ID'] == $party_org_enhet_id)
					{
						echo "<option value='{$result_unit['ORG_UNIT_ID']}' selected=selected >{$result_unit['UNIT_ID']} - {$result_unit['ORG_UNIT_NAME']}</option>";
					}
					else
					{
						echo "<option value='{$result_unit['ORG_UNIT_ID']}'>{$result_unit['UNIT_ID']} - {$result_unit['ORG_UNIT_NAME']}</option>";
					}
				}
				?>
			</select>
			<?php
		}
		else
		{
			$party_org_enhet_id = $party->get_org_enhet_id();
			if (isset($party_org_enhet_id) && is_numeric($party_org_enhet_id))
			{
				$result_unit = rental_bofellesdata::get_instance()->get_result_unit($party_org_enhet_id);
				echo $result_unit['ORG_NAME'];
			}
			else
			{
				echo lang('no_party_location');
			}
		}
	?>
</dd>