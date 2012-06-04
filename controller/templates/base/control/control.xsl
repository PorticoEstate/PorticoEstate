<!-- $Id$ -->
<xsl:template name="control" xmlns:php="http://php.net/xsl">

<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
<xsl:variable name="control_area_id"><xsl:value-of select="control/control_area_id"/></xsl:variable>
<xsl:variable name="control_procedure_id"><xsl:value-of select="control/procedure_id"/></xsl:variable>
<xsl:variable name="control_repeat_type"><xsl:value-of select="control/repeat_type"/></xsl:variable>
<xsl:variable name="control_role"><xsl:value-of select="control/responsibility_id"/></xsl:variable>
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<script>
		$(function() {
			$( "#start_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'd/m-yy',
				changeMonth: true,
				changeYear: true 
			});
			$( "#end_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'd/m-yy',
				changeMonth: true,
				changeYear: true
			});	
		});
	</script>
<div class="yui-content">
	<div id="control_details">
		<form id="frm_save_control_details" action="index.php?menuaction=controller.uicontrol.save_control_details" method="post">
			<input type="hidden" name="control_id" value="{$control_id}" />
			<input type="hidden" name="saved_control_area_id" value="{$control_area_id}" />	
	
			<dl class="proplist-col">
				<dt>
					<label>Kontrollområde</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select class="required" id="control_area_id" name="control_area_id">
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
						<span class="help_text">Angi hvilket kontrollområde kontrollen skal gjelde for</span>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/control_area_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label>Prosedyre</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="procedure_id" name="procedure_id">
							<option value="">Velg prosedyre</option>
							<xsl:for-each select="procedures_array">
								<xsl:choose>
									<xsl:when test="id = $control_procedure_id">
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
							</xsl:for-each>
						</select>
						<span class="help_text">Angi hvilken prosedyre som ligger til grunn for kontrollen</span>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/procedure_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>	
				<dt>
					<label for="title">Tittel</label>
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<input class="required" type="text" name="title" id="title" value="{control/title}" size="80"/>
							<div class="help_text">Angi tittel på kontrollen</div>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control/title" />
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					<label for="start_date">Startdato</label>
				</dt>
				<dd>
					<input class="required" id="start_date" name="start_date" type="text">
				      <xsl:if test="control/start_date != ''">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
				    <span class="help_text">Angi startdato for kontrollen</span>
				</dd>
				<dt>
					<label for="end_date">Sluttdato</label>
				</dt>
				<dd>
					<input id="end_date" name="end_date" type="text">
				      <xsl:if test="control/end_date != 0">
				      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/></xsl:attribute>
				      </xsl:if>
				    </input>
				    <span class="help_text">Angi sluttdato for kontrollen. Hvis kontrollen ikke har sluttdato, lar du feltet være tomt</span>
				</dd>
				<dt>
					<label>Frekvenstype</label>
				</dt>
				<dd>
					<select class="required" id="repeat_type" name="repeat_type">
						<option value="">Velg frekvenstype</option>
						<xsl:for-each select="repeat_type_array">
							<xsl:choose>
								<xsl:when test="id = $control_repeat_type">
									<option value="{id}" selected="selected">
										<xsl:value-of disable-output-escaping="yes" select="value"/>
									</option>
								</xsl:when>
								<xsl:otherwise>
									<option value="{id}">
										<xsl:value-of disable-output-escaping="yes" select="value"/>
									</option>
								</xsl:otherwise>
							</xsl:choose>								
						</xsl:for-each>
					</select>
					<span class="help_text">Angi hvilken frekvenstype kontrollen skal ha</span>
				</dd>
				<dt>
					<label>Frekvens</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<input class="required" id="repeat_interval" size="2" type="text" name="repeat_interval" value="{control/repeat_interval}" />
						<span class="help_text">Angi hvilket frekvensintervall kontrollen skal ha. Hvis du velger 2, betyr det at kontrollen skal gjennomføres annenhver dag</span>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/repeat_interval" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label>Tildelt rolle</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select class="required" id="responsibility_id" name="responsibility_id">
							<xsl:for-each select="role_array">
								<xsl:choose>
									<xsl:when test="id = $control_role">
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="name"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
							</xsl:for-each>
						</select>
						<span class="help_text">Angi hvilken rolle som skal ha ansvar for å gjennomføre kontrollen på de ulike byggene</span>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/role_name" />
					</xsl:otherwise>
				</xsl:choose>
				</dd>
				<dt>
					<label for="description">Beskrivelse</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<textarea cols="70" rows="5" name="description" id="description"><xsl:value-of select="control/description" /></textarea>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/description" disable-output-escaping="yes"/>
					</xsl:otherwise>
				</xsl:choose>
				</dd>
			</dl>
			
			<div class="form-buttons">
				<xsl:choose>
					<xsl:when test="editable">
						<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
						<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
						<input type="submit" name="edit_control" value="{$lang_edit}" title = "{$lang_edit}" />
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</form>					
	</div>
</div>
</xsl:template>