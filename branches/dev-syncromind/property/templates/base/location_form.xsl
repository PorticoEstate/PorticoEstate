  <!-- $Id$ -->
	<xsl:template name="location_form">
		<xsl:apply-templates select="location_data"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data" xmlns:php="http://php.net/xsl">
		<xsl:for-each select="location">
			<tr>
				<td class="th_text" width="{with}" align="{align}" title="{statustext}">
					<label>
						<xsl:choose>
							<xsl:when test="lookup_link=1">
								<a href="javascript:{lookup_function_call}" title="{statustext}">
									<xsl:value-of select="name"/>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="name"/>
							</xsl:otherwise>
						</xsl:choose>
					</label>
				</td>
				<td>
					<xsl:choose>
						<xsl:when test="readonly=1">
							<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}" readonly="readonly">
								<xsl:attribute name="title">
									<xsl:value-of select="statustext"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:otherwise>
							<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}">
								<xsl:attribute name="title">
									<xsl:value-of select="statustext"/>
								</xsl:attribute>
							</input>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:for-each select="extra">
						<xsl:choose>
							<xsl:when test="readonly=1">
								<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}">
									<xsl:attribute name="title">
										<xsl:value-of select="statustext"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:choose>
							<xsl:when test="is_entity=1">
								<input type="checkbox" name="clear_{input_name}_box" onClick="blank_entity_values()">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'delete')"/>
									</xsl:attribute>
									<xsl:attribute name="readonly">
										<xsl:text>readonly</xsl:text>
									</xsl:attribute>
								</input>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</td>
			</tr>
		</xsl:for-each>
	</xsl:template>
