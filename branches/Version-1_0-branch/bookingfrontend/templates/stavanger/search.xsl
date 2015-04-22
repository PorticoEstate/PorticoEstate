<xsl:template name="strip-tags" xmlns:php="http://php.net/xsl">
	<xsl:param name="text"/>
	<xsl:choose>
		<xsl:when test="contains($text, '&lt;')">
			<xsl:value-of select="substring-before($text, '&lt;')"/>
			<xsl:call-template name="strip-tags">
				<xsl:with-param name="text" select="concat(' ', substring-after($text, '&gt;'))"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$text"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
  <div id="content">
    <form action="" method="GET" id="search">
      <input type="hidden" name="menuaction" value="bookingfrontend.uisearch.index" />
      <xsl:choose>
        <xsl:when test="search and string-length(search/searchterm) &gt; 0">
          <input id="search" type="text" name="searchterm" value="{search/searchterm}"/>
        </xsl:when>
        <xsl:otherwise>
          <input id="search" type="text" name="searchterm" value="Søk hall, klubb eller aktivitet" onclick="value=''" />
        </xsl:otherwise>
      </xsl:choose>
      <xsl:text> </xsl:text><input type="submit" value="{php:function('lang', 'Search')}"/>
      <div class="hint">
        F.eks. "<i>Stavanger Idrettshall</i>", "<i>Svømmehall</i>", "<i>idrett</i>" eller "<i>volleyball</i>".
      </div>
    </form>
	
	<xsl:if test="not(search)">	
		<div id="cloud">
		<div class="frontpagetext"><xsl:value-of select="frontpagetext"/></div>
		
		</div>
		<div style="text-align:center;">
			<img alt="" >
				<xsl:attribute name="src"><xsl:value-of select="baseurl"/>/phpgwapi/templates/stavanger/images/newlayout/forsidebilde.jpg</xsl:attribute>
			</img>
		</div>	

	</xsl:if>
	
	<xsl:if test="search">	
 	   <div id="result">
	      <h5>
	        <u><strong><xsl:value-of select="php:function('lang', 'Found %1 results', search/results/total_records_sum)" /></strong></u>
	      </h5>
	      <br />
	      <br />
	      <xsl:if test="search/results/total_records_sum &gt; 0">
	        <ol id="result">
	          <xsl:for-each select="search/results/results">
	            <li>
	              <div class="header">
	                <a class="bui_single_view_link"><xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:value-of select="name"/></a>
	                (<xsl:value-of select="php:function('lang', string(type))"/>)
	              </div>
	              <div class="details">
	                <div>
	                  <dl>
	                    <dt><h4><xsl:value-of select="php:function('lang', 'Description')" /></h4></dt>
	                    <dd class="description">
							<xsl:variable name="tag_stripped_description">
								<xsl:call-template name="strip-tags">
									<xsl:with-param name="text" select="description"/>
								</xsl:call-template>
							</xsl:variable>
							
	                      <xsl:choose>
	                        <xsl:when test="string-length($tag_stripped_description) &gt; 1">
	                          <xsl:choose>
	                            <xsl:when test="string-length($tag_stripped_description) &gt; 100">
								  <xsl:value-of select="substring($tag_stripped_description, 0, 97)"/>...
	                            </xsl:when>
	                            <xsl:otherwise>
	                              <xsl:value-of select="$tag_stripped_description"/>
	                            </xsl:otherwise>
	                          </xsl:choose>
	                        </xsl:when>
	                        <xsl:otherwise>
	                          <xsl:value-of select="php:function('lang', 'No description yet')" />
	                        </xsl:otherwise>
	                      </xsl:choose>
						<div id="{img_container}"/>
						<script type="text/javascript">
						YAHOO.util.Event.addListener(window, "load", function() {
							YAHOO.booking.inlineImages('<xsl:value-of select="img_container"/>', '<xsl:value-of select="img_url"/>');
						});
						</script>

	                    </dd>
	                    <xsl:if test="string-length(homepage) &gt; 1">
	                      <dt><h4><xsl:value-of select="php:function('lang', 'Homepage')" /></h4></dt>
	                      <dd class="description">
	                        <a><xsl:attribute name="href"><xsl:value-of select="homepage"/></xsl:attribute><xsl:value-of select="homepage"/></a>
	                      </dd>
	                    </xsl:if>
	                  </dl>
	                </div>
	                <div class="clr"></div>
	              </div>
	            </li>

	          </xsl:for-each>
	        </ol>
	      </xsl:if>
	    </div>
	</xsl:if>
  </div>
</xsl:template>
