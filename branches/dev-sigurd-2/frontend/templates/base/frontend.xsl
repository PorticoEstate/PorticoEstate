
<xsl:template match="header">
	<div id="building_selector">
        <form action="" method="get">
            <label>Velg bygg</label>
            <br/>
            <select multiple="multiple" size="7">
                <option value="-1">Her skal det optimalt sett komme en liste med bygg</option>
            </select>
        </form>
    </div>
</xsl:template>


<xsl:template match="tabs">
	<xsl:value-of disable-output-escaping="yes" select="." />
</xsl:template>

