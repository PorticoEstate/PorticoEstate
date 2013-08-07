<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	
	<div id="content">
		<ul class="pathway">
			<li><a href="index.php?menuaction=bookingfrontend.uisearch.index"><xsl:value-of select="php:function('lang', 'Home')" /></a></li>
			<li>
				<a href="{resource/building_link}">
					<xsl:value-of select="building/name"/>
				</a>
			</li>
		</ul>

		<xsl:for-each select="building">	

			<xsl:if test="deactivate_calendar=0">
			</xsl:if>

			<dl class="proplist-col main">
				<div>
			     	<button onclick="window.location.href='{schedule_link}'"><xsl:value-of select="php:function('lang', 'Building schedule')" /></button>
					<xsl:if test="not(calendar_text and normalize-space(calendar_text))">
					- Søk ledig tid/informasjon om hva som skjer
					</xsl:if>
					<xsl:if test="calendar_text and normalize-space(calendar_text)">
					- <xsl:value-of select="calendar_text" disable-output-escaping="yes"/>
					</xsl:if>
				</div>
				<xsl:if test="normalize-space(description)">
					<dl class="proplist description">
						<dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
						<dd><xsl:value-of select="description" disable-output-escaping="yes"/></dd>
					</dl>
				</xsl:if>
				
				<xsl:if test="normalize-space(homepage) or normalize-space(email) or normalize-space(phone) or normalize-space(street)">
					<h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3>
			<xsl:if test="deactivate_sendmessage=0">

			<div>
	        	<button onclick="window.location.href='{message_link}'"><xsl:value-of select="php:function('lang', 'Send message')" /></button>
- 				Meldig til saksbehandler for bygg
			</div>
			</xsl:if>


					<dl class="contactinfo">
						<xsl:if test="homepage and normalize-space(homepage)">
							<dt><xsl:value-of select="php:function('lang', 'Homepage')" /></dt>
							<dd><a href="{homepage}"><xsl:value-of select="homepage"/></a></dd>
						</xsl:if>
					
						<xsl:if test="email and normalize-space(email)">
							<dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
							<dd><a href='mailto:{email}'><xsl:value-of select="email"/></a></dd>
						</xsl:if>
					
						<xsl:if test="phone and normalize-space(phone)">
							<dt><xsl:value-of select="php:function('lang', 'Telephone')" /></dt>
							<dd><xsl:value-of select="phone"/></dd>
						</xsl:if>
					
						<xsl:if test="street and normalize-space(street)">
							<dt><xsl:value-of select="php:function('lang', 'Address')" /></dt>
							<dd>
								<xsl:value-of select="street"/><br/>
								<xsl:value-of select="zip_code"/><span>&nbsp; </span>
								<xsl:value-of select="city"/><br/>
								<xsl:value-of select="district"/>
							</dd>
						</xsl:if>

						<xsl:if test="tilsyn_name and normalize-space(tilsyn_name)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></dt>
							<dd><xsl:value-of select="tilsyn_name"/></dd>
						</xsl:if>

						<xsl:if test="tilsyn_email and normalize-space(tilsyn_email)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></dt>
							<dd><a href='mailto:{tilsyn_email}'><xsl:value-of select="tilsyn_email"/></a></dd>
						</xsl:if>
					
						<xsl:if test="tilsyn_phone and normalize-space(tilsyn_phone)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></dt>
							<dd><xsl:value-of select="tilsyn_phone"/></dd>
						</xsl:if>

						<xsl:if test="tilsyn_name and normalize-space(tilsyn_name2)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></dt>
							<dd><xsl:value-of select="tilsyn_name2"/></dd>
						</xsl:if>

						<xsl:if test="tilsyn_email and normalize-space(tilsyn_email2)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></dt>
							<dd><a href='mailto:{tilsyn_email2}'><xsl:value-of select="tilsyn_email2"/></a></dd>
						</xsl:if>
					
						<xsl:if test="tilsyn_phone and normalize-space(tilsyn_phone2)">
							<dt><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></dt>
							<dd><xsl:value-of select="tilsyn_phone2"/></dd>
						</xsl:if>


					</dl>
				</xsl:if>
				
				<h3><xsl:value-of select="php:function('lang', 'Bookable resources')" /></h3>
				<div id="resources_container"/>

				<h3><xsl:value-of select="php:function('lang', 'Building users')" /></h3>
				<div id="building_users_container"/>

				<h3><xsl:value-of select="php:function('lang', 'Documents')" /></h3>
				<div id="documents_container"/>
			</dl>
			<dl class="proplist-col images">	
				<div id="images_container"></div>
				<xsl:if test="street and normalize-space(street)">
					<iframe width="500" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="googlemapiframe" src=""></iframe><br />
					<small><a href="" id="googlemaplink" style="color:#0000FF;text-align:left" target="_new">Vis større kart</a></small>
				</xsl:if>
			</dl>
			
			<script type="text/javascript">
				var building_id = <xsl:value-of select="id"/>;
				var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'category', 'Activity', 'Resource Type')"/>;
				var address = '<xsl:value-of select="street"/>, <xsl:value-of select="zip_code"/>, <xsl:value-of select="city"/>';
				<![CDATA[
				
				YAHOO.util.Event.addListener(window, "load", function() {
				var url = 'index.php?menuaction=bookingfrontend.uiresource.index_json&sort=sort&filter_building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}, {key: 'activity_name', label: lang['Activity']}];
				YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
				});
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index&sort=name&no_images=1&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'description', label: lang['Name'], formatter: YAHOO.booking.formatLink}];
				YAHOO.booking.inlineTableHelper('documents_container', url, colDefs);
				
				var url = 'index.php?menuaction=bookingfrontend.uidocument_building.index_images&sort=name&filter_owner_id=' + building_id + '&phpgw_return_as=json&';
				YAHOO.booking.inlineImages('images_container', url);
				
				var url = 'index.php?menuaction=bookingfrontend.uiorganization.building_users&sort=name&building_id=' + building_id + '&phpgw_return_as=json&';
				var colDefs = [{key: 'name', label: lang['Name'], formatter: YAHOO.booking.formatLink}, {key: 'activity_name', label: lang['Activity']}];
				YAHOO.booking.inlineTableHelper('building_users_container', url, colDefs);

				
				// Load Google map
				YAHOO.util.Event.addListener(window, "load", function() {
					var iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + address;
					var linkurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&geocode=&q=' + address;
					if( iurl.length > 0 ) {
						var iframe = YAHOO.util.Dom.get( 'googlemapiframe' );
						iframe.src = iurl;
						var link = YAHOO.util.Dom.get( 'googlemaplink' );
						link.href = linkurl;
					}
				});

				]]>
			</script>
		</xsl:for-each>
	</div>
</xsl:template>

