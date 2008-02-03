	<xsl:template match="app_data">
		<center>
		 <b><xsl:value-of select="title"/></b><br/>
		<table>
                    <thead>
			<xsl:apply-templates select="table_head" />
                    </thead>
                    <tbody>
			<xsl:for-each select="table_row">
				<xsl:call-template name="table_row">
				</xsl:call-template>
			</xsl:for-each>
                    </tbody>
		</table>
		<b><xsl:value-of select="msg"/></b><br/>
		</center>
	</xsl:template>
	
	<xsl:template match="table_head">
		<tr class="th">
			<th><xsl:value-of select="lang_ext_user"/> </th>
			<th><xsl:value-of select="lang_location"/> </th>
			<th><xsl:value-of select="lang_auth_type"/> </th>
			<th><xsl:value-of select="lang_allow_deny"/> </th>
			<th><xsl:value-of select="lang_delete"/> </th>
		</tr>
	</xsl:template>

	<xsl:template name="table_row">
		<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				
			<td><xsl:value-of select="ext_user"/> </td>
			<td><xsl:value-of select="location"/> </td>
			<td><xsl:value-of select="auth_type"/> </td>
			<td><a>
				<xsl:attribute name="href"><xsl:value-of select="allow_deny_url"/></xsl:attribute>
				<xsl:value-of select="lang_action"/>
			</a></td> 
			<td><a> 
				<xsl:attribute name="href"><xsl:value-of select="delete_url"/></xsl:attribute>
				<xsl:value-of select="lang_del"/>
			</a></td> 
		</tr>									
	</xsl:template>
