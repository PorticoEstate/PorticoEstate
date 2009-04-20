<xsl:template match="data">
    <div id="content">
        <div id="result">
		<br />
		<br />
        <xsl:for-each select="search/results">
        		<div style="margin-bottom: 2em;border: 0px solid #000000;"><span style="font-size: 10px;margin-right: 2em;"><xsl:value-of select="type"/></span>
				<a class="Tillbaka"><xsl:attribute name="href"><xsl:value-of select="start"/></xsl:attribute>Tillbaka</a>
            	<h2><xsl:value-of select="name"/></h2>
				<h4>Description:</h4>
				<h4>Activities:</h4>
				<h4>Booking resources:</h4>
				<h4>Contact information:</h4>
				<p>
				<span style="margin-left: 1em;">Phone</span><br />
				<span style="margin-left: 2em;"><xsl:value-of select="phone"/></span><br />
				<span style="margin-left: 1em;">Email</span><br />
				<span style="margin-left: 2em;"><xsl:value-of select="email"/></span><br />
				<span style="margin-left: 1em;">Homepage</span><br />
				<span style="margin-left: 2em;">
				<a class="homepage"><xsl:attribute name="href"><xsl:value-of select="homepage"/></xsl:attribute><xsl:value-of select="homepage"/></a></span>
				</p>
				</div>
        </xsl:for-each>
        </div>

    </div>
</xsl:template>
