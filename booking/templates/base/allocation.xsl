<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form'  class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="allocation/tabs"/>
			<div id="allocations" class="booking-container">
				<h1>
					<xsl:value-of select="allocation/organization_name"/>
				</h1>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'From')" />
					</label>
					<xsl:value-of select="php:function('pretty_timestamp', allocation/from_)"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'To')" />
					</label>
					<xsl:value-of select="php:function('pretty_timestamp', allocation/to_)"/>
				</div>
				<div class="pure-control-group">
					<label for="field_cost">
						<xsl:value-of select="php:function('lang', 'Cost')" />
					</label>
					<xsl:value-of select="allocation/cost"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Season')" />
					</label>
					<xsl:value-of select="allocation/season_name"/>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Organization')" />
					</label>
					<xsl:value-of select="allocation/organization_name"/>
				</div>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'Resources')" />
					</label>
					<div id="resources_container" style="display:inline-block;"></div>
				</div>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'participants')" />
					</label>
						<div id="participant_container" style="display:inline-block;"></div>
				</div>
			</div>
		</div>
	</form>
	<div class="pure-control-group">
		<xsl:choose>
			<xsl:when test="allocation/application_id !=''">
				<button class="pure-button pure-button-primary">
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/application_link"/>"</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'return to application')" />
				</button>
			</xsl:when>
			<xsl:otherwise>
				<button class="pure-button pure-button-primary">
					<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:booking.uiallocation.index')" />"</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'back')" />
				</button>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="allocation/permission/write">
			<button class="pure-button pure-button-primary">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/edit_link"/>"</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Edit')" />
			</button>
			<button class="pure-button pure-button-primary">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="allocation/delete_link"/>"</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Delete')" />
			</button>
		</xsl:if>
	</div>
	<script type="text/javascript">
		var resourceIds = '<xsl:value-of select="allocation/resource_ids"/>';
		var allocation_id = '<xsl:value-of select="allocation/id"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type', 'phone', 'email', 'quantity', 'from', 'to')"/>;
    <![CDATA[
//        var resourcesURL = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
		var resourcesURL = phpGWLink('index.php', {menuaction:'booking.uiresource.index', sort:'name', length:-1}, true) +'&' + resourceIds;
		var participantURL = phpGWLink('index.php', {menuaction:'booking.uiparticipant.index', sort:'phone', filter_reservation_id: allocation_id, filter_reservation_type: 'allocation', length:-1}, true);

    ]]>
		var colDefs = [{key: 'name', label: lang['Name'], formatter: genericLink()}, {key: 'rescategory_name', label: lang['Resource Type']}];
		createTable('resources_container',resourcesURL,colDefs, 'data', 'pure-table pure-table-bordered');

		var colDefsParticipantURL = [
		{key: 'phone', label: lang['phone']},
		{key: 'quantity', label: lang['quantity']},
		{key: 'from_', label: lang['from']},
		{key: 'to_', label: lang['to']}
		];

		var paginatorTableparticipant = new Array();
		paginatorTableparticipant.limit = 10;
		createPaginatorTable('participant_container', paginatorTableparticipant);

		createTable('participant_container', participantURL, colDefsParticipantURL, '', 'pure-table pure-table-bordered', paginatorTableparticipant);


	</script>
</xsl:template>
