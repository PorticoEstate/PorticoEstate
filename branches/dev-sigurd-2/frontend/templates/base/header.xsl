<xsl:template match="header" xmlns:php="http://php.net/xsl">
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

