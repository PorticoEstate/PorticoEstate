<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:phpgw="http://dtds.phpgroupware.org/phpgw.dtd"
	xmlns:phpgwapi="http://dtds.phpgroupware.org/phpgwapi.dtd"
	xmlns:communik8r="http://dtds.phpgroupware.org/communik8r.dtd">
	<xsl:output method="xml" indent="yes" />
	<xsl:template match="/">

		<xsl:variable name="base_url" 
			select="concat(/phpgw:response/phpgwapi:info/phpgwapi:base_url, '/communik8r/')"/>
		<xsl:variable name="img_url" 
			select="concat($base_url, 'templates/default/images/')"/>

		<div>
			<div id="headers">
				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_from">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_from']"/>:</span> 
					<span class="header_value">
						<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_from">
							<xsl:call-template name="email_address"/>
						</xsl:for-each>
					</span><br />
				</xsl:if>

				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_reply_to">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_reply_to']"/>:</span> 
					<span class="header_value">
						<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_reply_to">
							<xsl:call-template name="email_address"/>
						</xsl:for-each>
					</span><br />
				</xsl:if>
					
				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_to">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_to']"/>:</span> 
					<span class="header_value">
						<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_to">
							<xsl:call-template name="email_address"/>
						</xsl:for-each>
					</span><br />
				</xsl:if>

				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_cc">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_cc']"/>:</span> 
					<span class="header_value">
						<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_cc">
							<xsl:call-template name="email_address"/>
						</xsl:for-each>
					</span><br />
				</xsl:if>

				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_subject">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_subject']"/>:</span> 
					<span class="header_value"><xsl:value-of select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_subject"/></span><br />
				</xsl:if>

				<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_date">
					<span class="header_label"><xsl:value-of select="/phpgw:response/phpgwapi:info/phpgwapi:lang[@id='phpgwapi_lang_date']"/>:</span>
					<span class="header_value"><xsl:value-of select="/phpgw:response/communik8r:response/communik8r:message/communik8r:headers/communik8r:message_date"/></span><br />
				</xsl:if>
			</div>
			<pre>
				<xsl:value-of disable-output-escaping="yes" select="/phpgw:response/communik8r:response/communik8r:message/communik8r:body"/>
			</pre>

			<xsl:if test="/phpgw:response/communik8r:response/communik8r:message/communik8r:parts">
				<xsl:for-each select="/phpgw:response/communik8r:response/communik8r:message/communik8r:parts/communik8r:part">
					<xsl:call-template name="message_parts">
						<xsl:with-param name="msg_id" select="/phpgw:response/communik8r:response/communik8r:message/@id"/>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:if>
		</div>
	</xsl:template>

	<xsl:template name="message_parts">
		<xsl:param name="msg_id" />
		<xsl:variable name="filename"> 
			<xsl:choose>
				<xsl:when test=".">
					<xsl:value-of select="." />&#160;(<xsl:value-of select="@size" />)
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@mimetype" />&#160;(<xsl:value-of select="@size" />)
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<div id="{concat('part_', @id)}" class="msg_attachment">
			<img src="{@icon}" alt="{@mimetype}"
				title="{.}" onClick="showAttachment('{$msg_id}', '{@id}', '{@inline}');"/>&#160;<xsl:value-of select="$filename" />
		</div>
	</xsl:template>

	<xsl:template name="email_address">
		<xsl:value-of select="."/>
	</xsl:template>
</xsl:stylesheet>
