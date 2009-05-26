<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'Showing')" />: <xsl:value-of select="data/name"/></h3>

		<div id="composite_edit_tabview" class="yui-navset">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				
				<div id="details">
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
						<dd><xsl:value-of select="data/name"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'GAB')" /></dt>
						<dd><xsl:value-of select="data/gab_id"/></dd>
					</dl>
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd>
							<xsl:value-of select="data/adresse1"/><br />
							<xsl:value-of select="data/address_2"/>
						</dd>
					</dl>
				</div>
				
				<div id="elements">
					<p>elementer</p>
				</div>
				
				<div id="contracts">
					<p>kontrakter</p>
				</div>
				
				<div id="documents">
					<p>dokumenter</p>
				</div>
			</div>
		</div>
</xsl:template>
