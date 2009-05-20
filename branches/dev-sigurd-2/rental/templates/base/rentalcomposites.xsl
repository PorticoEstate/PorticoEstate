<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <h3><xsl:value-of select="php:function('lang', 'Rental composites')" /></h3>
    
           <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'From')" /></dt>
            <dd><xsl:value-of select="booking/from_"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'To')" /></dt>
            <dd><xsl:value-of select="booking/to_"/></dd>
        </dl>
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

</xsl:template>
