<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->

<xsl:template match="view_control_details">

	<fieldset>
		<dl class="proplist-col">
		<dt>
			<label>Kontrollområde</label>
		</dt>
		<dd>
		<xsl:choose>
			<xsl:when test="editable">
				<select id="control_area_id" name="control_area_id">
					<xsl:apply-templates select="control_areas_array2/options"/>
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
					<input type="text" name="title" id="title" value="{control/title}" size="80"/>
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
			<input>
		      <xsl:attribute name="id">start_date</xsl:attribute>
		      <xsl:attribute name="name">start_date</xsl:attribute>
		      <xsl:attribute name="type">text</xsl:attribute>
		      <xsl:if test="control/start_date != ''">
		      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/></xsl:attribute>
		      </xsl:if>
		    </input>
		</dd>
		<dt>
			<label for="end_date">Sluttdato</label>
		</dt>
		<dd>
			<input>
		      <xsl:attribute name="id">end_date</xsl:attribute>
		      <xsl:attribute name="name">end_date</xsl:attribute>
		      <xsl:attribute name="type">text</xsl:attribute>
		      <xsl:if test="control/end_date != 0">
		      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/></xsl:attribute>
		      </xsl:if>
		    </input>
		</dd>
		<dt>
			<label>Frekvenstype</label>
		</dt>
		<dd>
			<select id="repeat_type" name="repeat_type">
				<option value="0">Ikke angitt</option>
				<option value="1">Dag</option>
				<option value="2">Uke</option>
				<option value="3">Måned</option>
				<option value="5">År</option>
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
		<dt>
			<label>Rolle</label>
		</dt>
		<dd>
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
	</fieldset>
	
</xsl:template>
