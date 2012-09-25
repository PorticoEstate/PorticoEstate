<!-- $Id$ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>


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
					<xsl:value-of select="calendar_for_year_url"/>
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
				Kontrollplan for bygg/eiendom (år)
			</a>
			<a class="last">
				<xsl:attribute name="href">
					<xsl:value-of select="calendar_for_month_url"/>
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
		<form id="frm_add_check_list" action="{action_url}" method="post">
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			<input type="hidden" name="control_id" value="{$control_id}" />
			<xsl:variable name="type"><xsl:value-of select="type"/></xsl:variable>
			<input type="hidden" name="type" value="{$type}" />

			<xsl:variable name="location_code"><xsl:value-of select="location_array/location_code"/></xsl:variable>		

			<xsl:choose>
				<xsl:when test="type = 'component'">
					<xsl:variable name="location_id"><xsl:value-of select="check_list/location_id"/></xsl:variable>
					<input type="hidden" name="location_id" value="{$location_id}" />
					<xsl:variable name="component_id"><xsl:value-of select="check_list/component_id"/></xsl:variable>
					<input type="hidden" name="component_id" value="{$component_id}" />
					<input type="hidden" name="location_code" value="{$location_code}" />
				</xsl:when>
				<xsl:otherwise>
					<input type="hidden" name="location_code" value="{$location_code}" />
				</xsl:otherwise>
			</xsl:choose>
			
			<fieldset>
				<!-- STATUS -->
				<div class="row">
				<xsl:if test="check_list/error_msg_array/status != ''">
					  <xsl:variable name="error_msg"><xsl:value-of select="check_list/error_msg_array/status" /></xsl:variable>
						<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
					</xsl:if>
					<label>Status</label>
					<xsl:variable name="status"><xsl:value-of select="check_list/status"/></xsl:variable>
					<select id="status" name="status">
						<xsl:choose>
							<xsl:when test="check_list/status = 0">
								<option value="1">Utført</option>
								<option value="0" SELECTED="SELECTED" >Ikke utført</option>
							</xsl:when>
							<xsl:when test="check_list/status = 1">
								<option value="1" SELECTED="SELECTED">Utført</option>
								<option value="0">Ikke utført</option>
							</xsl:when>
							<xsl:otherwise>
								<option value="0">Ikke utført</option>
								<option value="1">Utført</option>
							</xsl:otherwise>
						</xsl:choose>
					</select>
				</div>
				<!-- DEADLINE -->
				<div class="row">
					<xsl:if test="check_list/error_msg_array/deadline != ''">
						<xsl:variable name="error_msg"><xsl:value-of select="check_list/error_msg_array/deadline" /></xsl:variable>
						<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
					</xsl:if>
					<label>Fristdato</label>
					<input type="text" id="deadline_date" name="deadline_date" class="date">
				      <xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/></xsl:attribute>
				  </input>
				</div>
				<!-- PLANNED DATE -->
				<div class="row">
					<label>Planlagt dato</label>
					<input type="text" id="planned_date" name="planned_date" class="date">
				  	<xsl:if test="check_list/planned_date != 0 and check_list/planned_date != ''">
			   	  	<xsl:attribute name="value">
			   	  		<xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/>
			   	  	</xsl:attribute>
			    	</xsl:if>
			    </input>
			  </div>
			  <!-- COMPLETED DATE -->
		    <div class="row">
				  <xsl:if test="check_list/error_msg_array/completed_date != ''">
					  <xsl:variable name="error_msg"><xsl:value-of select="check_list/error_msg_array/completed_date" /></xsl:variable>
						<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
				  </xsl:if>
				  <label>Utført dato</label>
				  <input type="text" id="completed_date" name="completed_date" class="date">
				  	<xsl:if test="check_list/completed_date != 0 and check_list/completed_date != ''">
			   	  	<xsl:attribute name="value">
			   	  		<xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/>
			   	  	</xsl:attribute>
			    	</xsl:if>
			    </input>
		    </div>
			</fieldset>
			 <!-- COMMENT -->
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
