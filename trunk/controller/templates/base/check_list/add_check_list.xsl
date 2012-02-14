<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>


<script>
	$(function() {
		$( "#planned_date" ).datepicker({ 
			monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
			dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
			dateFormat: 'dd/mm-yy',
			changeMonth: true,
			changeYear: true
		});
		$( "#completed_date" ).datepicker({ 
			monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
			dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
			dateFormat: 'dd/mm-yy',
			changeMonth: true,
			changeYear: true 
		});
	});
</script>
		

<div id="main_content">
	
	<h1>Registrere sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>

	<div id="edit_check_list_menu" class="hor_menu">
		<a href="#" class="active">
			Vis detaljer for sjekkliste
		</a>
		<a href="#">
			Vis saker
		</a>			
		<a href="#">
			Vis info om kontroll
		</a>
	</div>
	
	
	<h3 class="box_header">Sjekklistedetaljer</h3>
	<fieldset class="check_list_details">
		<form id="frm_add_check_list" action="index.php?menuaction=controller.uicheck_list.save_check_list" method="post">
					
			<div id="calendar_dates">
				<xsl:for-each select="calendar_array">
					<xsl:variable name="cal_date"><xsl:value-of select="."/></xsl:variable>
						
					<span><xsl:value-of select="php:function('date', $date_format, number( $cal_date ) )"/></span>
				</xsl:for-each>
			</div>
		
			<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
		
			<input type="hidden" name="control_id" value="{$control_id}" />
			<input type="hidden" name="location_code" value="{$location_code}" />

			<fieldset class="add_check_list">
				<div>
					<label>Status</label>
					<select name="status">
						<option value="0" SELECTED="SELECTED">Ikke utført</option>
						<option value="1" >Utført</option>
					</select>
				</div>
				<div>
					<label>Fristdato</label>
					<input>
				      <xsl:attribute name="id">deadline_date</xsl:attribute>
				      <xsl:attribute name="name">deadline_date</xsl:attribute>
				      <xsl:attribute name="type">text</xsl:attribute>
				      <xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(deadline))"/></xsl:attribute>
				    </input>
			    </div>
				<div>
					<label>Planlagt dato</label>
					<input>
				      <xsl:attribute name="id">planned_date</xsl:attribute>
				      <xsl:attribute name="name">planned_date</xsl:attribute>
				      <xsl:attribute name="type">text</xsl:attribute>
				      <xsl:attribute name="value"></xsl:attribute>
				    </input>
			    </div>
			    <div>
					<label>Utført dato</label>
					<input>
				      <xsl:attribute name="id">completed_date</xsl:attribute>
				      <xsl:attribute name="name">completed_date</xsl:attribute>
				      <xsl:attribute name="type">text</xsl:attribute>
					  <xsl:if test="check_list/completed_date != ''">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
			    </div>
				<div><label>Utstyr</label><input name="equipment_id" /></div>
				<div>
					<label class="comment">Kommentar</label>
					<textarea>
					  <xsl:attribute name="name">comment</xsl:attribute>
					  <xsl:value-of select="check_list/comment"/>
					</textarea>
				</div>
			</fieldset>
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input style="width: 170px;" class="btn not_active" type="submit" name="save_control" value="Lagre detaljer" />
			</div>
		</form>
	</fieldset>
	
	 </div>
</xsl:template>
