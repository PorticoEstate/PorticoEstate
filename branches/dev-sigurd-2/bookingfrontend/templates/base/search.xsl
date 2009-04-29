<xsl:template match="data" xmlns:php="http://php.net/xsl">
  <div id="content">

    <form action="" method="GET" id="search">
      <input type="hidden" name="menuaction" value="bookingfrontend.uisearch.index" />
      <xsl:choose>
        <xsl:when test="string-length(search/searchterm) &gt; 1">
          <input id="search" type="text" name="searchterm">
          <xsl:attribute name="value"><xsl:value-of select="search/searchterm" /></xsl:attribute>
        </input>
        </xsl:when>
        <xsl:otherwise>
          <input id="search" type="text" name="searchterm" value="Sök hall, klubb eller aktivitet" onclick="value=''" />
        </xsl:otherwise>
      </xsl:choose>
      <input type="submit" value="Search"/>
      <div class="hint">
        T.ex "<i>Haukelandshallen, Håndball</i>" eller "<i>Årstad Håndball</i>".
      </div>
    </form>

    <div id="result">
      <h5>
        <u><strong><xsl:value-of select="php:function('lang', 'Hittade %1 resultat', search/results/total_records_sum)" /></strong></u>
      </h5>
      <br />
      <br />
      <xsl:if test="search/results/total_records_sum &gt; 0">
        <ol id="result">
          <xsl:for-each select="search/results/results">
            <li>
              <div class="header">
                <a class="bui_single_view_link"><xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:value-of select="name"/></a>
                (<xsl:value-of select="type"/>)
              </div>
              <div class="details">
                <div class="col col3">

                  <dl>
                    <dt><h4><xsl:value-of select="php:function('lang', 'Description')" /></h4></dt>
                    <dd class="description">
                      <xsl:choose>
                        <xsl:when test="string-length(description) &gt; 1">
                          <xsl:choose>
                            <xsl:when test="string-length(description) &gt; 100">
                              <xsl:value-of select="substring(description, 0, 97)"/>...
                            </xsl:when>
                            <xsl:otherwise>
                              <xsl:value-of select="description" disable-output-escaping="yes"/>
                            </xsl:otherwise>
                          </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                          <xsl:value-of select="php:function('lang', 'No description yet')" />
                        </xsl:otherwise>
                      </xsl:choose>
                    </dd>
                    <xsl:if test="string-length(homepage) &gt; 1">
                      <dt><h4><xsl:value-of select="php:function('lang', 'Homepage')" /></h4></dt>
                      <dd class="description">
                        <a><xsl:attribute name="href"><xsl:value-of select="homepage"/></xsl:attribute><xsl:value-of select="homepage"/></a>
                      </dd>
                    </xsl:if>
                  </dl>
                  <div class="moreInfo">
                    <a class="bui_single_view_link"><xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:value-of select="php:function('lang', 'More info')" /></a>
                  </div>
                </div>
                <div class="clr"></div>
              </div>
            </li>

          </xsl:for-each>
        </ol>
      </xsl:if>
    </div>

  </div>
</xsl:template>
