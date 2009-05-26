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
						<dt><xsl:value-of select="php:function('lang', 'Area')" /></dt>
						<dd><xsl:value-of select="data/area"/> m<sup>2</sup></dd>
						<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
						<dd>
							<xsl:value-of select="data/adresse1"/>
							<xsl:if test="data/adresse2!=''">
								<br /><xsl:value-of select="data/adresse2"/>
							</xsl:if>
							<xsl:if test="data/postnummer!=''">
								<br /><xsl:value-of select="data/postnummer"/>
							</xsl:if>
							<xsl:if test="data/poststed!=''">
								<br /><xsl:value-of select="data/poststed"/>
							</xsl:if>
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
