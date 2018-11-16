<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<div id="application-show-content" class="margin-top-content">
        <div class="container wrapper">
			<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span>
						#<xsl:value-of select="application/id"/>
					</span>															
			</div>

			<div class="row">
				<div class="col">
					<!--
					<div class="mb-4 float-right">
						<xsl:if test="frontend and application/status='ACCEPTED'">
							<form method="POST">
								<input type="hidden" name="print" value="ACCEPTED"/>
								<button class="btn btn-light" type="submit"><xsl:value-of select="php:function('lang', 'Print as PDF')"/></button>
							</form>
						</xsl:if>
					</div>
					-->
					<xsl:call-template name="msgbox"/>

					<xsl:if test="frontend">
						<dl>
							<span style="font-size: 110%; font-weight: bold;">Din søknad har status <xsl:value-of select="php:function('lang', string(application/status))"/></span>
							<span class="text">, opprettet <xsl:value-of select="php:function('pretty_timestamp', application/created)"/>, sist endret <xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></span>
							<span class="text">
								<br />Melding fra saksbehandler ligger under historikk, deretter vises kopi av din søknad.<br /> Skal du gi en melding til saksbehandler skriver du denne inn i feltet under "Legg til en kommentar"</span>
						</dl>
					</xsl:if>
				</div>

				<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Add a comment')" /></label>
								<form method="POST">
									<textarea name="comment" class="form-control" style="width: 60%; height: 7em"></textarea>
									<br/>
									<input type="submit" class="btn btn-light" value="{php:function('lang', 'Add comment')}" />
								</form>
							</div>
				</div>

				<div class="col-12 mt-4">
					<dl>
						<dt><xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" /></dt>
						<xsl:for-each select="application/comments[author]">
							<dt>
								<xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
							</dt>
							<xsl:choose>
								<xsl:when test='contains(comment,"bookingfrontend.uidocument_building.download")'>
									<dd>
										<xsl:value-of select="comment" disable-output-escaping="yes"/>
									</dd>
								</xsl:when>
								<xsl:otherwise>
									<dd>
										<div style="width: 80%;">
											<xsl:value-of select="comment" disable-output-escaping="yes"/>
										</div>
									</dd>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</dl>
				</div>

				<div class="col-12 mt-4">
					<dl>
						<dt>
							<xsl:value-of select="php:function('lang', 'attachments')" />
						</dt>
						<dd>
							<div id="attachments_container"/>
						</dd>
						<dd>
							<form method="POST" enctype='multipart/form-data' id='file_form'>
								<input name="name" id='field_name' type='file' >
									<xsl:attribute name='title'>
										<xsl:value-of select="document/name"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>mime size</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-allowing">
										<xsl:text>jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-max-size">
										<xsl:text>2M</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:text>Max 2M:: jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
									</xsl:attribute>
								</input>
								<br/>
								<input type="submit" class="btn btn-light mt-4" value="{php:function('lang', 'Add attachment')}" />
							</form>
						</dd>
					</dl>
				</div>

				<div class="col-12 mt-4">
					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></h5>
					<dl>
						<dt>
							<xsl:value-of select="php:function('lang', 'Building (2018)')" />
						</dt>
						<dd>
							<xsl:value-of select="application/building_name"/>
						</dd>
						<dd>
							<div id="resources_container"/>
						</dd>
						<xsl:for-each select="application/dates">
							<dd>
								<span style="font-weight:bold;">
									<xsl:value-of select="php:function('lang', 'From')" />: &nbsp;</span>
								<span>
									<xsl:value-of select="php:function('pretty_timestamp', from_)"/>
								</span>
							</dd>
							<dd>
								<span style="font-weight:bold;">
									<xsl:value-of select="php:function('lang', 'To')" />: &nbsp;</span>
								<span>
									<xsl:value-of select="php:function('pretty_timestamp', to_)"/>
								</span>
							</dd>
						</xsl:for-each>
					</dl>
				</div>

				<div class="col-12 mt-4">
					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Information about the event')" /></h5>
					<dl>
						<dt>
							<xsl:value-of select="php:function('lang', 'Target audience')" />
						</dt>
						<dd>
								<xsl:for-each select="audience">
									<xsl:if test="../application/audience=id">
											<xsl:value-of select="name"/>
									</xsl:if>
								</xsl:for-each>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'Event name')" />
						</dt>
						<dd>
							<xsl:value-of select="application/name"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'Organizer')" />
						</dt>
						<dd>
							<xsl:value-of select="application/organizer"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'Homepage for the event')" />
						</dt>
						<dd>
							<xsl:value-of select="application/homepage"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'description')" />
						</dt>
						<dd>
							<xsl:value-of disable-output-escaping="yes" select="application/description"/>
						</dd>
						<dt>
							<xsl:value-of select="config/application_equipment"/>
						</dt>
						<dd>
							<xsl:value-of disable-output-escaping="yes" select="application/equipment"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'Number of participants')" />
						</dt>
						<dd>
							<table id="agegroup">
								<thead>
									<tr>
										<th/>
										<th>
											<xsl:value-of select="php:function('lang', 'Male')" />
										</th>
										<th>
											<xsl:value-of select="php:function('lang', 'Female')" />
										</th>
									</tr>
								</thead>
								<tbody>
									<xsl:for-each select="agegroups">
										<xsl:variable name="id">
											<xsl:value-of select="id"/>
										</xsl:variable>
										<tr>
											<th>
												<xsl:value-of select="name"/>
											</th>
											<td>
												<xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
											</td>
											<td>
												<xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/>
											</td>
										</tr>
									</xsl:for-each>
								</tbody>
							</table>
						</dd>
					</dl>
				</div>

				<div class="col-12 mt-4">
					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Contact and invoice information')" /></h5>
					<dl>
						<dt>
							<xsl:value-of select="php:function('lang', 'Name')" />
						</dt>
						<dd>
							<xsl:value-of select="application/contact_name"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'street')" />
						</dt>
						<dd>
							<xsl:value-of select="application/responsible_street"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'zip code')" />
						</dt>
						<dd>
							<xsl:value-of select="application/responsible_zip_code"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'postal city')" />
						</dt>
						<dd>
							<xsl:value-of select="application/responsible_city"/>
						</dd>
						<dt>
							<xsl:value-of select="php:function('lang', 'Email')" />
						</dt>
						<dd>
							<xsl:value-of select="application/contact_email"/>
						</dd>
						<dt>
								<xsl:value-of select="php:function('lang', 'Phone')" />
						</dt>
						<dd>
							<xsl:value-of select="application/contact_phone"/>
						</dd>
						<xsl:if test="application/customer_identifier_type = 'organization_number'">
							<dt>
									<xsl:value-of select="php:function('lang', 'organization number')" />
							</dt>
							<dd>
								<xsl:value-of select="application/customer_organization_number"/>
							</dd>
						</xsl:if>
						<xsl:if test="application/customer_identifier_type = 'ssn'">
							<dt>
									<xsl:value-of select="php:function('lang', 'SSN')" />
							</dt>
							<dd>
								<xsl:value-of select="application/customer_ssn"/>
							</dd>
						</xsl:if>
					</dl>
				</div>

				<div class="col-12 mt-4">
					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Terms and conditions')" /></h5>
					<dl>
						<div id='regulation_documents'>&nbsp;</div>
						<br />
						<p>
							<xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" />
						</p>
					</dl>
				</div>

				
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var resourceIds = '<xsl:value-of select="application/resource_ids" />';
		if (!resourceIds || resourceIds == "") {
		resourceIds = false;
		}
		var lang = <xsl:value-of select="php:function('js_lang', 'Resources (2018)', 'Document', 'Name')" />;
		var app_id = <xsl:value-of select="application/id" />;
		var building_id = <xsl:value-of select="application/building_id" />;
		var resources = <xsl:value-of select="application/resources" />;

        <![CDATA[
            var resourcesURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uiresource.index_json', sort:'name'}, true) +'&' + resourceIds;
            var documentURL = phpGWLink('bookingfrontend/index.php', {menuaction:'booking.uidocument_view.regulations', sort:'name'}, true) + '&owner[]=building::' + building_id;
                documentURL += '&owner[]=resource::'+ resources;
			var attachmentsResourceURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_application.index', sort:'name', no_images:1, filter_owner_id:app_id}, true);
        ]]>

		if (resourceIds) {
		var colDefsResource = [{key: 'name', label: lang['Resources (2018)'], formatter: genericLink}];
		createTable('resources_container', resourcesURL, colDefsResource, 'results');
		}

		var colDefsDocument = [{key: 'name', label: lang['Document'], formatter: genericLink}];
		createTable('regulation_documents', documentURL, colDefsDocument);

		var colDefsAttachmentsResource = [{key: 'name', label: lang['Name'], formatter: genericLink}];
		createTable('attachments_container', attachmentsResourceURL, colDefsAttachmentsResource);

	</script>
</xsl:template>
