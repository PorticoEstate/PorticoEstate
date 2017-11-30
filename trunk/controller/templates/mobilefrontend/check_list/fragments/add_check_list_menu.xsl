<xsl:template name="add_check_list_menu" xmlns:php="http://php.net/xsl">
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<li class="pure-menu-item pure-menu-disabled">
		<a href="#" class="pure-menu-link">Vis detaljer for sjekkliste</a>
	</li>
	<li class="pure-menu-item pure-menu-disabled">
		<a href="#" class="pure-menu-link">Vis saker</a>
	</li>
	<li class="pure-menu-item pure-menu-disabled">
		<a href="#" class="pure-menu-link">Vis info om kontroll</a>
	</li>
	<li class="pure-menu-item pure-menu-disabled">
		<a href="#" class="pure-menu-link">
			<xsl:value-of select="php:function('lang', 'add case')"/>
		</a>
	</li>
	<li class="pure-menu-item pure-menu-disabled">
		<a href="#" class="pure-menu-link">
			<xsl:value-of select="php:function('lang', 'add ticket')"/>
		</a>
	</li>

</xsl:template>
