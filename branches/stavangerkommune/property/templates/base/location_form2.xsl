  <!-- $Id: location_form2.xsl 10555 2012-11-29 13:55:27Z sigurdne $ -->
	<xsl:template name="location_form2">
		<xsl:apply-templates select="location_data2"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data2" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<xsl:for-each select="location">
			<dt>
				<label title="{statustext}">
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
			</dt>
			<dd>
				<xsl:choose>
					<xsl:when test="readonly=1">
						<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}" readonly="readonly">
							<xsl:if test="input_type != 'hidden' and required = 1">
								<xsl:attribute name="title">
									<xsl:value-of select="statustext"/>
								</xsl:attribute>
								<xsl:attribute name="formvalidator:FormField">
									<xsl:text>yes</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="formvalidator:Type">
									<xsl:text>TextBaseField</xsl:text>
								</xsl:attribute>
							</xsl:if>
						</input>
					</xsl:when>
					<xsl:otherwise>
						<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}">
							<xsl:if test="input_type != 'hidden'">
								<xsl:attribute name="title">
									<xsl:value-of select="statustext"/>
								</xsl:attribute>
							</xsl:if>
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
				</xsl:for-each>
			</dd>
		</xsl:for-each>
	</xsl:template>
