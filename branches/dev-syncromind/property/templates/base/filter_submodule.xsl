  <!-- $Id$ -->
	<xsl:template name="filter_submodule">
		<xsl:variable name="select_action">
			<xsl:value-of select="select_action"/>
		</xsl:variable>
		<xsl:variable name="select_name_submodule">
			<xsl:value-of select="select_name_submodule"/>
		</xsl:variable>
		<xsl:variable name="lang_submit">
			<xsl:value-of select="lang_submit"/>
		</xsl:variable>
		<form method="post" action="{$select_action}">
			<xsl:for-each select="hidden_vars">
				<xsl:variable name="name">
					<xsl:value-of select="name"/>
				</xsl:variable>
				<xsl:variable name="value">
					<xsl:value-of select="value"/>
				</xsl:variable>
				<input type="hidden" name="{$name}" value="{$value}"/>
			</xsl:for-each>
			<select name="{$select_name_submodule}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_submodule_statustext"/>
				</xsl:attribute>
				<option value="">
					<xsl:value-of select="lang_no_submodule"/>
				</option>
				<xsl:apply-templates select="submodule_list"/>
			</select>
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"/>
			</noscript>
		</form>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="submodule_list">
		<xsl:variable name="id">
			<xsl:value-of select="id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}">
					<xsl:value-of disable-output-escaping="yes" select="descr"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
