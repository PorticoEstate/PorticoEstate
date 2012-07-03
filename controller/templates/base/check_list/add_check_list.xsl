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

<!-- ==================  ADD CHECKLIST  ========================= -->

<div id="main_content" class="medium">
	<div id="check-list-heading">
		<div class="box-1">
			<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
			<xsl:choose>
				<xsl:when test="type = 'component'">
					<h2><xsl:value-of select="component_array/xml_short_desc"/></h2>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="location_level = 1">
							<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
						</xsl:when>
						<xsl:otherwise>
								<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="box-2 select-box">
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_year</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:choose>
					  <xsl:when test="type = 'component'">
						  <xsl:value-of select="building_location_code"/>
						</xsl:when>
						<xsl:otherwise>
						  <xsl:value-of select="location_array/location_code"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				Kontrolplan for bygg/eiendom (år)
			</a>
			<a class="last">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicalendar.view_calendar_for_month</xsl:text>
					<xsl:text>&amp;year=</xsl:text>
					<xsl:value-of select="current_year"/>
					<xsl:text>&amp;month=</xsl:text>
					<xsl:value-of select="current_month_nr"/>
					<xsl:text>&amp;location_code=</xsl:text>
					<xsl:choose>
					  <xsl:when test="type = 'component'">
						  <xsl:value-of select="building_location_code"/>
						</xsl:when>
						<xsl:otherwise>
						  <xsl:value-of select="location_array/location_code"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				Kontrolplan for bygg/eiendom (måned)
			</a>
		</div>
	</div>
		
	<div id="check_list_menu">
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
	
	<!-- ==================  CHECKLIST DETAILS  ===================== -->
	<div id="check_list_details">
		<h3 class="box_header">Sjekklistedetaljer</h3>
		<form id="frm_add_check_list" action="index.php?menuaction=controller.uicheck_list.save_check_list" method="post">
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			<input type="hidden" name="control_id" value="{$control_id}" />
			<xsl:variable name="type"><xsl:value-of select="type"/></xsl:variable>
			<input type="hidden" name="type" value="{$type}" />
			
			<xsl:choose>
				<xsl:when test="type = 'component'">
					<xsl:variable name="location_id"><xsl:value-of select="check_list/location_id"/></xsl:variable>
					<input type="hidden" name="location_id" value="{$location_id}" />
					<xsl:variable name="component_id"><xsl:value-of select="check_list/component_id"/></xsl:variable>
					<input type="hidden" name="component_id" value="{$component_id}" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>
					<input type="hidden" name="location_code" value="{$location_code}" />
				</xsl:otherwise>
			</xsl:choose>
			
			<fieldset>
				<div class="row">
					<label>Status</label>
					<select id="status" name="status">
						<option value="0" SELECTED="SELECTED">Ikke utført</option>
						<option value="1" >Utført</option>
					</select>
				</div>
				<div class="row">
					<label>Fristdato</label>
					<input type="text" id="deadline_date" name="deadline_date" class="date">
				      <xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/></xsl:attribute>
				    </input>
			    </div>
				<div class="row">
					<label>Planlagt dato</label>
					<input type="text" id="planned_date" name="planned_date" class="date" value="" />
			    </div>
			    <div class="row">
					<label>Utført dato</label>
					<input type="text" id="completed_date" name="completed_date" class="date">
					  <xsl:if test="check_list/completed_date != ''">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
			    </div>
				<!-- 
					div><label>Utstyr</label><input name="equipment_id" /></div>
				 -->
			</fieldset>
			<div class="comment">
				<label>Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">comment</xsl:attribute>
				  <xsl:value-of select="check_list/comment"/>
				</textarea>
			</div>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input class="btn not_active" type="submit" value="Lagre detaljer" />
			</div>
		</form>	
	 </div>
</div>
</xsl:template>
