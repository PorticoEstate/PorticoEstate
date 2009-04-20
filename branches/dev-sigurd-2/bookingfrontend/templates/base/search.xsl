<xsl:template match="data">
    <div id="content">

        <form action="" method="GET" id="search">
		    <input type="hidden" name="menuaction" value="bookingfrontend.uisearch.index" />
            <input id="search" type="text" name="searchterm" value="Sök hall, klubb eller aktivitet" onclick="value=''" />
            <input type="submit" value="Search"/>
            <div class="hint">
                T.ex "<i>Haukelandshallen, Håndball</i>" eller "<i>Årstad Håndball</i>".
            </div>
        </form>
		
        <div id="result">
		<h5><u><strong>Hittade <xsl:value-of select="search/results/total_records_sum" /> resultat</strong></u></h5>
		<br />
		<br />
        <xsl:for-each select="search/results/results">
        		<div style="margin-bottom: 2em;border: 1px solid #000000;"><span style="font-size: 10px;margin-right: 2em;"><xsl:value-of select="type"/></span>
            <a class="bui_single_view_link">
                <xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
				<h1><xsl:value-of select="name"/></h1>
            </a>
        		<h3><xsl:value-of select="homepage"/></h3></div>
        </xsl:for-each>
        </div>

    </div>
</xsl:template>
