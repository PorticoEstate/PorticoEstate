<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div class="content">
		<ul class="pathway">
			<li>
				<a href="{booking/bookings_link}">
					<xsl:value-of select="php:function('lang', 'Bookings')" />
				</a>
			</li>
			<li>#<xsl:value-of select="booking/id"/> (<xsl:value-of select="booking/activity_name"/>)</li>
		</ul>

		<xsl:call-template name="msgbox"/>

		<dl class="proplist-col">
			<dt>
				<xsl:value-of select="php:function('lang', 'From')" />
			</dt>
			<dd>
				<xsl:value-of select="php:function('pretty_timestamp', booking/from_)"/>
			</dd>
			<dt>
				<xsl:value-of select="php:function('lang', 'To')" />
			</dt>
			<dd>
				<xsl:value-of select="php:function('pretty_timestamp', booking/to_)"/>
			</dd>
			<dt>
				<xsl:value-of select="php:function('lang', 'Cost')" />
			</dt>
			<dd>
				<xsl:value-of select="booking/cost"/>
			</dd>
		</dl>
		<dl class="proplist-col">
			<dt>
				<xsl:value-of select="php:function('lang', 'Season')" />
			</dt>
			<dd>
				<xsl:value-of select="booking/season_name"/>
			</dd>
			<dt>
				<xsl:value-of select="php:function('lang', 'Group')" />
			</dt>
			<dd>
				<xsl:value-of select="booking/group_name"/>
			</dd>
		</dl>
		<dl class="proplist-col">
			<dt>
				<xsl:value-of select="php:function('lang', 'Resources')" />
			</dt>
			<dd>
				<div id="resources_container"/>
			</dd>
		</dl>

		<div class="form-buttons">
			<xsl:if test="booking/permission/write">
				<button>
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="booking/edit_link"/>"</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Edit')" />
				</button>
				<button>
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="booking/delete_link"/>"</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Delete booking')" />
				</button>
			</xsl:if>
		</div>
	</div>
    
	<script type="text/javascript">
		var resourcesIds = '<xsl:value-of select="booking/resource_ids" />';
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type')" />;
        <![CDATA[
            var resourceURL = phpGWLink('bookingfrontend/index.php', {menuaction: 'bookingfrontend.uiresource.index_json', sort:'name'}, true) + '&' + resourcesIds;
        ]]>
		var colDefsResources = [{key: 'name', label: lang['Name'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}];
		createTable('resources_container', resourceURL, colDefsResources, 'results');
	</script>
</xsl:template>
