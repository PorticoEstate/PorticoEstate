<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div class="content">
		<xsl:for-each select="pathway">
			<ul class="pathway">
				<li>
					<a>
						<xsl:attribute name="href">
							<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'Home')" />
					</a>
				</li>
				<li>
					<a href="{building_link}">
						<xsl:value-of select="building_name"/>
					</a>
				</li>
				<li>
					<xsl:value-of select="resource_name"/>
				</li>
			</ul>
		</xsl:for-each>

		<div>
			<button onclick="window.location.href='{resource/schedule_link}'">
				<xsl:value-of select="php:function('lang', 'Resource schedule')" />
			</button>
			- Søk ledig tid/informasjon om hva som skjer
		</div>
		<div class="pure-g">
			<div class="pure-u-1 pure-u-md-1-2">
				<dl class="proplist-col main">
					<xsl:if test="resource/description and normalize-space(resource/description)">
						<dt>
							<xsl:value-of select="php:function('lang', 'Description')" />
						</dt>
						<dd>
							<xsl:value-of disable-output-escaping="yes" select="resource/description"/>
						</dd>
					</xsl:if>
					<xsl:if test="resource/activity_name and normalize-space(resource/activity_name)">
						<dt>
							<xsl:value-of select="php:function('lang', 'Activity')" />
						</dt>
						<dd>
							<xsl:value-of select="resource/activity_name"/>
						</dd>
					</xsl:if>
					<dt>
						<xsl:value-of select="php:function('lang', 'Resource Type')" />
					</dt>
					<dd>
						<xsl:value-of select="php:function('lang', string(resource/type))"/>
					</dd>
					<h3>
						<xsl:value-of select="php:function('lang', 'Documents')" />
					</h3>
					<div id="documents_container"/>
				</dl>
				<div  id="custom_fields"></div>
			</div>
			<input type= "hidden" id="field_activity_id" value="{resource/activity_id}"/>

			<div class="pure-u-1 pure-u-lg-1-2">
				<dl class="proplist-col images">
					<div id="images_container">
					</div>
				</dl>
				<dl class="proplist-col images map">
					<!--div id="images_container"></div-->
					<xsl:if test="resource/building/street and normalize-space(resource/building/street)">
						<div class="gmap-container">
							<iframe width="500" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" id="googlemapiframe" src=""></iframe>
						</div>
						<small>
							<a href="" id="googlemaplink" style="color:#0000FF;text-align:left" target="_new">Vis større kart</a>
						</small>
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
        var documentsResourceURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_resource.index', sort:'name', no_images:1, filter_owner_id:resource_id}, true);
        var documentsResourceImagesURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_resource.index_images', sort:'name', filter_owner_id:resource_id}, true);
        var iurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&output=embed&geocode=&q=' + address;
        var linkurl = 'https://maps.google.com/maps?f=q&source=s_q&hl=no&geocode=&q=' + address;
         ]]>

		var colDefsDocumentsResource = [{key: 'name', label: lang['Name'], formatter: genericLink}];

		createTable('documents_container', documentsResourceURL, colDefsDocumentsResource, '', 'pure-table pure-table-bordered');
		$(window).on('load', function(){
		JqueryPortico.booking.inlineImages('images_container', documentsResourceImagesURL);

		// Load Google map
		if( iurl.length > 0 ) {
		$("#googlemapiframe").attr("src", iurl);
		$("#googlemaplink").attr("href", linkurl);
		}

		});
        <![CDATA[

		$(document).ready(function () {

			get_custom_fields();
		});

		get_custom_fields = function () {
			var oArgs = {menuaction: 'bookingfrontend.uiresource.get_custom', resource_id: resource_id};
			var requestUrl = phpGWLink('bookingfrontend/', oArgs);
			requestUrl += "&phpgw_return_as=stripped_html";
			var activity_id = $("#field_activity_id").val();
			$.ajax({
				type: 'POST',
				data: {activity_id: activity_id},
				dataType: 'html',
				url: requestUrl,
				success: function (data) {
					if (data != null)
					{
						var custom_fields = data;
						$("#custom_fields").html(custom_fields);
					}
				}
			});
		};
         ]]>


	</script>
</xsl:template>
