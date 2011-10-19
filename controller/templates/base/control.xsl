<xsl:template name="control" xmlns:php="http://php.net/xsl">

<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
<xsl:variable name="control_area_id"><xsl:value-of select="control/control_area_id"/></xsl:variable>
<xsl:variable name="control_procedure_id"><xsl:value-of select="control/procedure_id"/></xsl:variable>

<div class="yui-content">
	<div id="control_details">
		<form action="index.php?menuaction=controller.uicontrol.index" method="post">
			<input type="hidden" name="control_id" value="{$control_id}" />	
	
			<dl class="proplist-col">
				<dt>
					<label>Kontrollområde</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="control_area_id" name="control_area_id">
							<xsl:for-each select="control_areas_array">
								<xsl:choose>
									<xsl:when test="id != $control_area_id">
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
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
				</dd>
				<dt>
					<label>Prosedyre</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<select id="procedure_id" name="procedure_id">
							<xsl:for-each select="procedures_array">
								<xsl:choose>
									<xsl:when test="id != $control_procedure_id">
										<option value="{id}">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:when>
									<xsl:otherwise>
										<option value="{id}" selected="selected">
											<xsl:value-of disable-output-escaping="yes" select="title"/>
										</option>
									</xsl:otherwise>
								</xsl:choose>								
						    </xsl:for-each>
						</select>
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
						<input type="text" name="title" id="title" value="{control/title}" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/title" />
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
				<dt>
					<label for="start_date">Startdato</label>
				</dt>
				<dd>
					<xsl:value-of disable-output-escaping="yes" select="start_date"/>
				</dd>
				<dt>
					<label for="end_date">Sluttdato</label>
				</dt>
				<dd>
					<xsl:value-of disable-output-escaping="yes" select="end_date"/>
				</dd>
				<dt>
					<label>Frekvenstype</label>
				</dt>
				<dd>
					<select id="repeat_type" name="repeat_type">
						<option value="0">Ikke angitt</option>
						<option value="1">Daglig</option>
						<option value="2">Ukentlig</option>
						<option value="3">Månedlig pr dato</option>
						<option value="4">Månedlig pr dag</option>
						<option value="5">Årlig</option>
					</select>
				</dd>
				<dt>
					<label>Frekvens</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">
						<input size="2" type="text" name="repeat_interval" value="{control/repeat_interval}" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/repeat_interval" />
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