<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
	<ul>
		<xsl:foreach select="delegate">
			<li>
				<dl>
					<xsl:value-of select="account_firstname"/>&nbsp;<xsl:value-of select="account_lastname"/>
					(<xsl:value-of select="account_lid"/>)
					<a href="index.php?menuaction=frontend.uidelegate.remove_deletage&account_id={account_id}">Fjern</a>
				</dl>
			</li>
		</xsl:foreach>
	</ul>
</xsl:template>


