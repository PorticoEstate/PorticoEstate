<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'Showing')" />: <xsl:value-of select="name"/></h3>
    
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

		<xsl:value-of select="tabs" disable-output-escaping="yes" />
		
		<!--
    <div class="datatable-container">
        <table width="100%" class="datatable" cellpadding="2" cellspacing="2" align="center">
            <tr>
                <th>2</th>
                <th>Bar</th>
            </tr>
            <tr>
                <td>Foo</td>
                <td>Bar</td>
            </tr>
        </table>
    </div>
    
    <div>
    	<p>This will be the frontpage of the app.</p>
    </div>
		-->
</xsl:template>
