  <!-- $Id$ -->
	<xsl:template name="location_form2">
		<xsl:apply-templates select="location_data2"/>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="location_data2" xmlns:php="http://php.net/xsl">
		<xsl:for-each select="location">
			<div class="pure-control-group">
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
			
				<xsl:choose>
					<xsl:when test="readonly=1">
						<input size="{size}" type="{input_type}" name="{input_name}" value="{value}" onClick="{lookup_function_call}" readonly="readonly">
							<xsl:if test="input_type != 'hidden' and required = 1">
								<xsl:attribute name="title">
									<xsl:value-of select="statustext"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>number</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please select a location !')"/>
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
			</div>
		</xsl:for-each>
	</xsl:template>
