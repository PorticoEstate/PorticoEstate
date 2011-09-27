<xsl:template name="control" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
	<xsl:value-of select="php:function('lang', 'Control')" />
</h1>
</div>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value = "{value_id}" />
	
			<dl class="proplist-col">
				<dt>
					<label>Kontrollområde</label>
				</dt>
				<dd>
				<xsl:choose>
					<xsl:when test="editable">					
						<select id="control_area_id" name="control_area_id">
							<xsl:for-each select="control_area_options/options">
								<option value="{id}">
									<xsl:if test="selected != 0">
										<xsl:attribute name="selected" value="selected" />
									</xsl:if>
									<xsl:value-of disable-output-escaping="yes" select="title"/>
								</option>
						    </xsl:for-each>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/control_area_id" />
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
							<xsl:for-each select="procedure_options/options">
								<option value="{id}">
									<xsl:if test="selected != 0">
										<xsl:attribute name="selected" value="selected" />
									</xsl:if>
									<xsl:value-of disable-output-escaping="yes" select="title"/>
								</option>
						    </xsl:for-each>
						</select>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="control/procedure_id" />
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