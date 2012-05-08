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
	<xsl:variable name="resource"><xsl:value-of select="resource" /></xsl:variable>
  <div id="content">
    <form action="" method="GET" id="search">
      <input type="hidden" name="menuaction" value="bookingfrontend.uisearch.index" />
      <xsl:choose>
        <xsl:when test="search and string-length(search/searchterm) &gt; 0">
          <input id="searchterm" type="text" name="searchterm" value="{search/searchterm}"/>
        </xsl:when>
        <xsl:otherwise>
          <input id="searchterm" type="text" name="searchterm" value="Søk leirplass, hytte, utstyr eller aktivitet" onclick="value=''" />
        </xsl:otherwise>
      </xsl:choose>
      <xsl:text> </xsl:text><input type="submit" value="{php:function('lang', 'Search')}"/>
      <div class="hint" id="hint">
        F.eks. "<i>Solstølen</i>", "<i>Tredalen</i>", "<i>kano</i>" eller "<i>leir</i>" 
      </div>
	  <div class="settings" id="regions">
			<select name='regions' id='field_regions'>
			<option value=''><xsl:value-of select="php:function('lang', 'Select Area')" />...</option>
				<xsl:for-each select="resource/regions/*">
					<option value="{local-name()}">
						<xsl:if test="../../region = local-name()">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="string(node())"/>
					</option>
				</xsl:for-each>
			</select>
			<input name="fylke" id='field_fylke' type='hidden' value='{resource/fylke}' />			
			<select name='fylker' id='field_fylker'>
			<option value=''><xsl:value-of select="php:function('lang', 'Select County')" />...</option>
				<xsl:for-each select="resource/fylker/*">
					<option value="{local-name()}">
						<xsl:if test="../../fylke = local-name()">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="string(node())"/>
					</option>
				</xsl:for-each>
			</select>
	 </div>
	<div class="settings" id="extrafields">
		<select name='res' id='field_type'>
		<option value=''><xsl:value-of select="php:function('lang', 'Select Type')" />...</option>
			<xsl:for-each select="resource/types/*">
				<option value="{local-name()}">
					<xsl:if test="../../res = local-name()">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="php:function('lang', string(node()))"/>
				</option>
			</xsl:for-each>
		</select>
		<div id="field_campsites">
            <span id="field_campsites2">
		        Minimum antall plasser: 
            </span>
            <span id="field_meetingroom">
    		    Antall deltakere: 
            </span>
		<input type="text" size="3" id="campsites" name="campsites">
			<xsl:if test="resource/campsite">
				<xsl:attribute name="value"><xsl:value-of select="resource/campsite"/></xsl:attribute>
			</xsl:if>
		</input>						
		</div>
		<select name='bedspaces' id='field_bedspaces'>
			<option value=''><xsl:value-of select="php:function('lang', 'Select bedspaces')" />...</option>
			<xsl:for-each select="resource/bedspaces/*">
				<option value="{local-name()}">
					<xsl:if test="../../beds = local-name()">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="string(node())"/>
				</option>
			</xsl:for-each>
		</select>
	</div>
    </form>
	
	<xsl:if test="not(search)">	
		<div id="cloud">
		<div>Velkommen til Norges speiderforbunds bookingsystem for eiendommer og utstyr. Her finner du oversikt over og informasjon om hytter, leirplasser og andre speidereiendommer, samt utstyr som kanoer o.l. som også leies ut.
		</div>
		</div>
		<div style="text-align:center;">
			<xsl:choose>
				<xsl:when test="frontimages">
					<div id="frontimagesbox">
						<xsl:for-each select="frontimages">
							<a>
								<xsl:attribute name="href">index.php?menuaction=bookingfrontend.uiresource.show&amp;id=<xsl:value-of select="owner_id"/></xsl:attribute>
								<div class="frontimagebox">
									<div class="frontimage">
										<xsl:attribute name="style">
											background-image: url( 'index.php?menuaction=bookingfrontend.uidocument_resource.download&amp;id=<xsl:value-of select="id"/>' );
										</xsl:attribute>
									</div>
									<xsl:value-of disable-output-escaping="yes" select="description"/>
								</div>
							</a>
						</xsl:for-each>
						<div class="clr"></div>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<img alt="" >
						<xsl:attribute name="src">
							<xsl:value-of select="frontimage"/> 
						</xsl:attribute>
					</img>
				</xsl:otherwise>
			</xsl:choose>
		</div>	
	</xsl:if>
	<xsl:variable name="layout"><xsl:value-of select="layout" /></xsl:variable>
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
					<xsl:if test="$layout='bergen'">
	                	(<xsl:value-of select="php:function('lang', string(type))"/>)
					</xsl:if>
	              </div>
	              <div class="details">
	                <div>
	                  <dl>
						<xsl:if test="$layout='bergen'">
		                    <dt><h4><xsl:value-of select="php:function('lang', 'Description')" /></h4></dt>
						</xsl:if>
	                    <dd class="description">
							<xsl:variable name="tag_stripped_description">
								<xsl:call-template name="strip-tags">
									<xsl:with-param name="text" select="description"/>
								</xsl:call-template>
							</xsl:variable>
							
	                      <xsl:choose>
	                        <xsl:when test="string-length($tag_stripped_description) &gt; 1">
	                          <xsl:choose>
	                            <xsl:when test="string-length($tag_stripped_description) &gt; 100 and $layout='bergen'">
								  <xsl:value-of select="substring($tag_stripped_description, 0, 97)"/>...
	                            </xsl:when>
	                            <xsl:when test="string-length($tag_stripped_description) &gt; 280 and $layout='nsf'">
								  <xsl:value-of select="substring($tag_stripped_description, 0, 277)"/>aaa...
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

						<div class="image_container" id="{img_container}"/>
						<script type="text/javascript">
						YAHOO.util.Event.addListener(window, "load", function() {
							YAHOO.booking.inlineImages('<xsl:value-of select="img_container"/>', '<xsl:value-of select="img_url"/>');
						});
						</script>

	                    </dd>
	                    <xsl:if test="string-length(homepage) &gt; 1 and $layout='bergen'">
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
