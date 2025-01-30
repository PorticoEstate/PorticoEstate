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
						<input type="checkbox" value="1" name="active" >
							<xsl:if test="allocation/active=1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</input>
					</label>
					<xsl:value-of select="php:function('lang', 'active')"/>
				</div>
				<div class="pure-control-group">
					<label>
						<input type="checkbox" value="1" name="skip_bas" >
							<xsl:if test="allocation/skip_bas=1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</input>
					</label>
					<xsl:value-of select="php:function('lang', 'skip bas')"/>
				</div>

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
				<xsl:if test="config/activate_application_articles">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Articles')" />
						</label>
						<div id="articles_container" class="pure-custom" style="display:inline-block;"></div>
					</div>
				</xsl:if>
				<!--additional_invoice_information-->
				<xsl:if test="allocation/additional_invoice_information">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Additional Invoice Information')" />
						</label>
						<xsl:value-of select="allocation/additional_invoice_information"/>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label style="vertical-align:top;">
						<xsl:value-of select="php:function('lang', 'participants')" />
					</label>
					<div id="participant_container" style="display:inline-block;"></div>
				</div>
				<div class="pure-control-group">
					<label for="field_sms_content">
						<xsl:value-of select="php:function('lang', 'SMS')" />
					</label>
					<textarea rows="5" id="field_sms_content" name="sms_content" class="pure-input-1-2" >
					</textarea>
				</div>
				<div class="pure-controls">
					<label for="send_sms" class="pure-checkbox">
						<input type="checkbox" id="send_sms" name="send_sms" value="1"/>
						<xsl:value-of select="php:function('lang', 'send SMS')" />
					</label>
					<button type="submit" class="pure-button pure-button-primary">
						<xsl:value-of select="php:function('lang', 'send SMS')" />
					</button>
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
		var template_set = '<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|template_set')" />';
		var resourceIds = '<xsl:value-of select="allocation/resource_ids"/>';
		var allocation_id = '<xsl:value-of select="allocation/id"/>';
		var reservation_type = 'allocation';
		var reservation_id = '<xsl:value-of select="allocation/id"/>';
		var initialSelection = <xsl:value-of select="allocation/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Resource Type', 'phone', 'email', 'quantity', 'from', 'to', 'tax', 'article', 'unit', 'unit cost', 'Selected', 'Sum')"/>;
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
