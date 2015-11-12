<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--xsl:call-template name="yui_booking_i18n"/-->
	<div id="content">
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="resource/building_name"/>
				</a>
			</li>
			<li>
                <xsl:value-of select="resource/name"/>
			</li>
		</ul>
		<div>
        	<button onclick="window.location.href='{resource/schedule_link}'"><xsl:value-of select="php:function('lang', 'Resource schedule')" /></button>
            - Søk ledig tid/informasjon om hva som skjer
		</div>
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-1-2">
                <dl class="proplist-col main">
                    <xsl:if test="resource/description and normalize-space(resource/description)">
                        <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
                        <dd><xsl:value-of disable-output-escaping="yes" select="resource/description"/></dd>
                    </xsl:if>
                    <xsl:if test="resource/activity_name and normalize-space(resource/activity_name)">
                        <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
                        <dd><xsl:value-of select="resource/activity_name"/></dd>
                    </xsl:if>
                    <dt>
                        <xsl:value-of select="php:function('lang', 'Resource Type')" />
                    </dt>
                    <dd>
                        <xsl:value-of select="php:function('lang', string(resource/type))"/>
                    </dd>
                    <h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
                    <div id="documents_container"/>
                </dl>
            </div>
            <div class="pure-u-1 pure-u-md-1-2">
                <dl class="proplist-col images">
                    <div id="images_container">
                    </div>
                </dl>
           </div>
        </div>        
        <div class="pure-g">
            <div class="pure-u-1 pure-u-lg-1-2"></div>
            <div class="pure-u-1 pure-u-lg-1-2">
                <dl class="proplist-col images map">
                    <!--div id="images_container"></div-->
                    <xsl:if test="resource/building/street and normalize-space(resource/building/street)">
                        <div class="gmap-container">
                            <iframe width="500" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="googlemapiframe" src=""></iframe>
                        </div>
                        <small><a href="" id="googlemaplink" style="color:#0000FF;text-align:left" target="_new">Vis større kart</a></small>
                    </xsl:if>
                </dl>
            </div>
        </div>        
	</div>
	<script type="text/javascript">        
		var resource_id = <xsl:value-of select="resource/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'category', 'Activity')"/>;
		var address = '<xsl:value-of select="resource/building/street"/>, <xsl:value-of select="resource/building/zip_code"/>, <xsl:value-of select="resource/building/city"/>';

        <![CDATA[
        var documentsResourceURL = 'index.php?menuaction=bookingfrontend.uidocument_resource.index&sort=name&no_images=1&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
        var documentsResourceImagesURL = 'index.php?menuaction=bookingfrontend.uidocument_resource.index_images&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
		var iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + address;
		var linkurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&geocode=&q=' + address;
         ]]>

        var colDefsDocumentsResource = [{key: 'name', label: lang['Name'], formatter: genericLink}];

        createTable('documents_container', documentsResourceURL, colDefsDocumentsResource);
        $(window).load(function(){
			JqueryPortico.booking.inlineImages('images_container', documentsResourceImagesURL);

			// Load Google map
			if( iurl.length > 0 ) {
				$("#googlemapiframe").attr("src", iurl);
				$("#googlemaplink").attr("href", linkurl);
			}

        });

    /*
    <![CDATA[
        YAHOO.util.Event.addListener(window, "load", function() {

        var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index&sort=name&no_images=1&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
        var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
        YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);

        var url = 'index.php?menuaction=bookingfrontend.uidocument_resource.index_images&sort=name&filter_owner_id=' + resource_id + '&phpgw_return_as=json&';
        YAHOO.booking.inlineImages('images_container', url);
    });
    ]]>
    */
	</script>
</xsl:template>
