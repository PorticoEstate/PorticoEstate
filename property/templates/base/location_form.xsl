
<!-- $Id$ -->
<xsl:template name="location_form">
	<xsl:apply-templates select="location_data"/>
</xsl:template>

<!-- New template-->
<xsl:template match="location_data" xmlns:php="http://php.net/xsl">
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
			<div class="pure-g pure-custom">
				<div>
					<xsl:if test="input_type != 'hidden'">
						<xsl:attribute name="class">
							<xsl:text>pure-u-1-5</xsl:text>
						</xsl:attribute>
					</xsl:if>
					<xsl:if test="class != ''">
						<xsl:attribute name="class">
							<xsl:value-of select="class"/>
						</xsl:attribute>
					</xsl:if>
					<input size="{size}" type="{input_type}" id="{input_name}" name="{input_name}" value="{value}" onClick="{lookup_function_call}">
						<xsl:if test="input_type != 'hidden'">
							<xsl:attribute name="class">
								<xsl:text>pure-input-1</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:if test="input_name = 'contact_phone'">
							<xsl:attribute name="maxlength">20</xsl:attribute>
						</xsl:if>
						<xsl:if test="readonly=1">
							<xsl:attribute name="class">
								<xsl:text>readonly</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:if test="input_type != 'hidden' and required = 1">
							<xsl:attribute name="title">
								<xsl:value-of select="statustext"/>
							</xsl:attribute>
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please select a location !')"/>
							</xsl:attribute>
						</xsl:if>
					</input>
				</div>
				<xsl:for-each select="extra">
					<div>
						<xsl:if test="input_type != 'hidden'">
							<xsl:attribute name="class">
								<xsl:text>pure-u-3-4</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<xsl:if test="class != ''">
							<xsl:attribute name="class">
								<xsl:value-of select="class"/>
							</xsl:attribute>
						</xsl:if>
						<input size="{size}" type="{input_type}" id="{input_name}" name="{input_name}" value="{value}" onClick="{lookup_function_call}">
							<xsl:if test="input_type != 'hidden'">
								<xsl:attribute name="class">
									<xsl:text>pure-input-1</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:if test="is_entity=1">
								<xsl:attribute name="class">
									<xsl:text>pure-input-2-3</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:if test="readonly=1">
								<xsl:attribute name="readonly">
									<xsl:text>readonly</xsl:text>
								</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="title">
								<xsl:value-of select="statustext"/>
							</xsl:attribute>
						</input>
						<xsl:choose>
							<xsl:when test="is_entity=1">
								<a href="javascript:blank_entity_values();">
									<xsl:value-of select="php:function('lang', 'delete')"/>
								</a>
								<!--input type="checkbox" name="clear_{input_name}_box" onClick="blank_entity_values();">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'delete')"/>
									</xsl:attribute>
									<xsl:attribute name="readonly">
										<xsl:text>readonly</xsl:text>
									</xsl:attribute>
								</input-->
							</xsl:when>
						</xsl:choose>
					</div>
				</xsl:for-each>
			</div>
		</div>
	</xsl:for-each>
</xsl:template>
