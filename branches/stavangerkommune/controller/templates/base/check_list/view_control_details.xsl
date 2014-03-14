<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>	
	<h2>Kontrolldetaljer</h2>
	
	<fieldset id="control_details">
		<div class="row">
			<label>Kontrollområde</label>
			
			<xsl:choose>
			<xsl:when test="editable">
				<select id="control_area_id" name="control_area_id">
						<option value="">Velg kontrollområde</option>
							<xsl:for-each select="control_areas_array">
								<xsl:choose>
									<xsl:when test="cat_id = $control_area_id">
										<option value="{cat_id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{cat_id}">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
							</xsl:for-each>
						</select>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="control/control_area_name" />
			</xsl:otherwise>
		</xsl:choose>
		</div>
		<div class="row">
			<label>Prosedyre</label>
			<xsl:value-of select="control/procedure_name" />
		</div>
		<div class="row">
			<label for="title">Tittel</label>
			<xsl:choose>
				<xsl:when test="editable">
					<input type="text" name="title" id="title" value="{control/title}" size="80"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="control/title" />
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="row">
			<label for="start_date">Startdato</label>
			<xsl:choose>
		      <xsl:when test="not(control/start_date = '0') or not(control/start_date = '')">
		      	<xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/>
		      </xsl:when>
		      <xsl:otherwise>
		      	Dato ikke angitt
		      </xsl:otherwise>
	      </xsl:choose>
		</div>
		<div class="row">
			<label for="end_date">Sluttdato</label>
			<xsl:choose>
		      <xsl:when test="not(control/end_date = '0') or not(control/end_date = '')">
		      	Løpende
		      </xsl:when>
		      <xsl:otherwise>
		      	<xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/>
		      </xsl:otherwise>
	      </xsl:choose>
		</div>
		<div class="row frequency">
			<label>Frekvens</label>
			<xsl:choose>
	 			<xsl:when test="control/repeat_interval = 1">
	     		<span class="pre">Hver</span>
	     	</xsl:when>
	     	<xsl:when test="control/repeat_interval = 2">
	     		<span class="pre">Annenhver</span>
	     	</xsl:when>
	     	<xsl:when test="control/repeat_interval > 2">
	     		<span class="pre">Hver</span><span><xsl:value-of select="control/repeat_interval"/>.</span>
	     	</xsl:when>
	     </xsl:choose>
	     <span class="val"><xsl:value-of select="control/repeat_type_label"/></span>
		</div>
		<div class="row">
			<label>Rolle</label>
			<xsl:choose>
				<xsl:when test="editable">
					<select id="responsibility_id" name="responsibility_id">
						<xsl:for-each select="role_array">
							<option value="{id}">
								<xsl:value-of disable-output-escaping="yes" select="name"/>
							</option>
						</xsl:for-each>
					</select>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="control/responsibility_name" />
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="row">
			<label for="description">Beskrivelse</label>
			<xsl:choose>
				<xsl:when test="editable">
					<textarea cols="70" rows="5" name="description" id="description"><xsl:value-of select="control/description" /></textarea>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="control/description" disable-output-escaping="yes"/>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</fieldset>
	
</xsl:template>
