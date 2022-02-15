<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="edit_action">
		<xsl:value-of select="organization/edit_link"/>
	</xsl:variable>
	<form class= "pure-form pure-form-aligned" action="{$edit_action}" method="post" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="organization/tabs"/>
			<div id="organization" class="booking-container">
				<fieldset>
					<h1>
						<xsl:value-of select="organization/name"/>
					</h1>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organization shortname')" />
						</label>
						<span>
							<xsl:value-of select="organization/shortname" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organization number')" />
						</label>
						<span>
							<xsl:value-of select="organization/organization_number" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'in tax register')"/>
						</label>
						<span>
							<xsl:value-of select="organization/in_tax_register" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Customer number')" />
						</label>
						<span>
							<xsl:value-of select="organization/customer_number" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Activity')" />
						</label>
						<span>
							<xsl:value-of select="organization/activity_name" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Homepage')" />
						</label>
						<xsl:if test="organization/homepage and normalize-space(organization/homepage)">
							<a target="blank" href="{organization/homepage}">
								<xsl:value-of select="organization/homepage" />
							</a>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Email')" />
						</label>
						<a href="mailto:{organization/email}">
							<xsl:value-of select="organization/email"/>
						</a>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Phone')" />
						</label>
						<span>
							<xsl:value-of select="organization/phone"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Description')" />
						</label>
						<div style="display:inline-block;">
							<xsl:value-of select="organization/description" disable-output-escaping="yes"/>
						</div>
					</div>
					<xsl:if test="count(organization/contacts/*) &gt; 0">
						<div class="pure-control-group">
							<label style="vertical-align:top;">
								<xsl:value-of select="php:function('lang', 'Admins')" />
							</label>
							<ul style="list-style:none;display:inline-block;padding:0;margin:0;">
								<xsl:if test="organization/contacts[1]">
									<li>
										<xsl:value-of select="organization/contacts[1]/name"/>
									</li>
								</xsl:if>
								<xsl:if test="organization/contacts[2]">
									<li>
										<xsl:value-of select="organization/contacts[2]/name"/>
									</li>
								</xsl:if>
							</ul>
						</div>
					</xsl:if>
						<xsl:copy-of select="phpgw:booking_customer_identifier_show(organization)"/>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Street')" />
						</label>
						<span>
							<xsl:value-of select="organization/street"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Zip code')" />
						</label>
						<span>
							<xsl:value-of select="organization/zip_code"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Postal City')" />
						</label>
						<span>
							<xsl:value-of select="organization/city"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'District')" />
						</label>
						<span>
							<xsl:value-of select="organization/district"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Documents')" />
						</label>
						<div class="pure-custom">
							<div id="documents_container" class="custom-container"></div>
							<div>
								<a class='button'>
									<xsl:attribute name="href">
										<xsl:value-of select="organization/add_document_link"/>
									</xsl:attribute>
									<xsl:if test="organization/permission/write">
										<xsl:value-of select="php:function('lang', 'Add Document')" />
									</xsl:if>
								</a>
							</div>
						</div>
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'groups')" />
						</label>
						<div class="pure-custom">
							<div id="groups_container"/>
							<xsl:if test="organization/permission/write">
								<a href="{organization/new_group_link}">
									<xsl:value-of select="php:function('lang', 'new group')" />
								</a>
							</xsl:if>
						</div>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'delegates')" />
						</label>
						<div class="pure-custom">
							<div id="delegates_container"/>
							<xsl:if test="organization/permission/write">
								<a href="{organization/new_delegate_link}">
									<xsl:value-of select="php:function('lang', 'new delegate')" />
								</a>
							</xsl:if>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
	</form>
	<div class="form-buttons">
		<button class="pure-button pure-button-primary" onclick="window.location.href='{organization/edit_link}'">
			<xsl:value-of select="php:function('lang', 'Edit')" />
		</button>
		<input type="button" class="pure-button pure-button-primary" name="cencel">
			<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="organization/cancel_link"/>"</xsl:attribute>
			<xsl:attribute name="value">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</xsl:attribute>
		</input>
	</div>
	<script type="text/javascript">
		var organization_id = <xsl:value-of select="organization/id"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Category', 'Actions', 'Account', 'Role', 'Edit', 'Delete', 'Resource Type', 'Sort order', 'email')"/>;

    <![CDATA[
		var groupURL = phpGWLink('index.php', {menuaction:'booking.uigroup.index', sort:'name', filter_organization_id: organization_id, length:-1}, true);
		var delegateURL =  phpGWLink('index.php', {menuaction:'booking.uidelegate.index', sort: 'name', filter_organization_id: organization_id, filter_active:'-1', length:-1},true);
		var documentsURL = phpGWLink('index.php', {menuaction:'booking.uidocument_organization.index', sort:'name',filter_owner_id: organization_id,  length:-1}, true);

        ]]>
		var colDefsGroups = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'primary_contact_email', label: lang['email']}
		];
		var colDefsDelegate = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'email', label: lang['email']}
		];
		var colDefsDocuments = [
		{key: 'name', label: lang['Name'], formatter: genericLink},
		{key: 'category', label: lang['Category']},
		{key: 'actions', label: lang['Actions'], formatter: genericLink({name: 'edit', label:lang['Edit']}, {name: 'delete', label:lang['Delete']})}
		];

		createTable('groups_container', groupURL, colDefsGroups, '', 'pure-table pure-table-bordered');
		createTable('delegates_container', delegateURL, colDefsDelegate, '', 'pure-table pure-table-bordered');
		createTable('documents_container',documentsURL,colDefsDocuments, '', 'pure-table pure-table-bordered');
	</script>

</xsl:template>
