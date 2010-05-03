<xsl:template match="delegate_data" xmlns:php="http://php.net/xsl">
	<xsl:copy-of select="."/>
	<ul>
		<xsl:foreach select="delegate">
			<li>
					<xsl:value-of select="account_firstname"/>&amp;nbsp;<xsl:value-of select="account_lastname"/>
					(<xsl:value-of select="account_lid"/>)
						<!-- <a href="index.php?menuaction=frontend.uidelegate.remove_deletage&account_id={account_id}">Fjern</a> -->
				
			</li>
		</xsl:foreach>
	</ul>
</xsl:template>


