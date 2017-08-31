
<!-- $Id: party.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<div>
					<xsl:value-of select="php:function('lang', 'name')"/> : <xsl:value-of select="value_name"/>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required')"/>;
		</script>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'identifier')"/>
							</label>
							<input type="text" name="identifier" value="{value_identifier}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
							<input type="hidden" name="id" value="{party_id}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'customer id')"/>
							</label>
							<input type="text" name="customer_id" value="{value_customer_id}">
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'firstname')"/>
							</label>
							<input type="text" id="firstname" name="firstname" value="{value_firstname}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>								
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'lastname')"/>
							</label>
							<input type="text" id="lastname" name="lastname" value="{value_lastname}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'job_title')"/>
							</label>
							<input type="text" name="title" value="{value_job_title}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'company')"/>
							</label>
							<input type="text" id="company_name" name="company_name" value="{value_company}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'department')"/>
							</label>
							<input type="text" id="department" name="department" value="{value_department}">
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
							<input type="text" name="address1" value="{value_address1}"></input>
							<input type="text" name="address2" value="{value_address2}"></input>
						</div>						
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'postal_code_place')"/>
							</label>
							<input type="text" name="postal_code" value="{value_postal_code}"></input>
							<input type="text" name="place" value="{value_place}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'inactive_party')"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive">
								<xsl:if test="is_inactive_party = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_number')"/>
							</label>
							<input type="text" name="account_number" value="{value_account_number}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'phone')"/>
							</label>
							<input type="text" name="phone" value="{value_phone}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'mobile_phone')"/>
							</label>
							<input type="text" name="mobile_phone" value="{value_mobile_phone}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'fax')"/>
							</label>
							<input type="text" name="fax" value="{value_fax}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<input type="text" name="email" id="email" value="{value_email}">
								<xsl:attribute name="data-validation">
									<xsl:text>email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
							</input>
							<xsl:choose>
								<xsl:when test="valid_email = 1">
									<xsl:text> </xsl:text>
									<a href="{link_create_user}">
										<xsl:value-of select="php:function('lang', 'create_user_based_on_email_link')"/>
									</a>
								</xsl:when>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'url')"/>
							</label>
							<input type="text" name="url" value="{value_url}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'unit_leader')"/>
							</label>
							<input type="text" id="unit_leader" name="unit_leader" value="{value_unit_leader}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<textarea cols="47" rows="7" name="comment">
								<xsl:value-of select="value_comment"/>
							</textarea>
						</div>
						<xsl:choose>
							<xsl:when test="use_fellesdata = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'organization')"/>
									</label>
									<select id="org_enhet_id" name="org_enhet_id">
										<xsl:apply-templates select="list_organization/options"/>
									</select>
								</div>
							</xsl:when>
						</xsl:choose>
					</fieldset>
				</div>
				<xsl:choose>
					<xsl:when test="party_id > 0">
						<div id="contracts">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="contract_query" name="contract_query" value=""></input>
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="contract_search_options" name="contract_search_options">
											<xsl:apply-templates select="list_search_contract/options"/>
										</select>
									</div>
									<div class="pure-control-group">							
										<label>
											<xsl:value-of select="php:function('lang', 'status')"/>
										</label>
										<select id="contract_status" name="contract_status">
											<xsl:apply-templates select="list_status/options"/>
										</select>
										<label>
											<xsl:value-of select="php:function('lang', 'date')"/>
										</label>
										<input type="text" id="status_date" name="status_date" value=""></input>					
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
										</label>
										<select id="contract_type" name="contract_type">
											<xsl:apply-templates select="list_field_of_responsibility/options"/>
										</select>
									</div>										
								</div>
							</div>								
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
						<div id="documents">
							<script type="text/javascript">
								var link_upload_document = <xsl:value-of select="link_upload_document"/>;
							</script>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Upload')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label></label>
										<input type="file" id="ctrl_upoad_path" name="file_path"/>
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'title')"/>
										</label>
										<input type="text" id="document_title" name="document_title"/>
										<xsl:text> </xsl:text>
										<select id="document_type" name="document_type">
											<xsl:apply-templates select="list_document_types/options"/>
										</select>
										<xsl:text> </xsl:text>
										<xsl:variable name="upload">
											<xsl:value-of select="php:function('lang', 'upload')"/>
										</xsl:variable>											
										<input type="button" class="pure-button pure-button-primary" name="upload_button" id="upload_button" value="{$upload}" />							
									</div>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input id="document_query" type="text" name="document_query" />
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="document_search_option" name="document_search_option">
											<xsl:apply-templates select="list_document_search/options"/>
										</select>
										<label>
											<xsl:value-of select="php:function('lang', 'document_type')"/>
										</label>
										<select id="document_type_search" name="document_type_search">
											<xsl:apply-templates select="list_document_types/options"/>
										</select>																	
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_1'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl' />
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="data" select ='data' />
											<xsl:with-param name="config" select ='config' />
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_party" value="{lang_save}" onMouseout="window.status='';return true;"/>
				<xsl:choose>
					<xsl:when test="use_fellesdata = 1">
						<input type="button" onclick="onGetSync_data('{sync_info_url}')" class="pure-button pure-button-primary" name="synchronize" value="{lang_sync_data}" onMouseout="window.status='';return true;"/>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
		<form id="form_upload" name="form_upload" method="post" action="" enctype="multipart/form-data"></form>
	</div>
</xsl:template>

<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<xsl:call-template name="top-toolbar" />
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required')"/>;
	</script>
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'identifier')"/>
							</label>
							<xsl:value-of select="value_identifier"/>
							<input type="hidden" name="id" value="{party_id}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'customer id')"/>
							</label>
							<xsl:value-of select="value_customer_id"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'firstname')"/>
							</label>
							<xsl:value-of select="value_firstname"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'lastname')"/>
							</label>
							<xsl:value-of select="value_lastname"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'job_title')"/>
							</label>
							<xsl:value-of select="value_job_title"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'company')"/>
							</label>
							<xsl:value-of select="value_company"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'department')"/>
							</label>
							<xsl:value-of select="value_department"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
							<div class="pure-custom">
								<div>
									<xsl:value-of select="value_address1"/>
								</div>
								<div>
									<xsl:value-of select="value_address2"/>
								</div>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'postal_code_place')"/>
							</label>
							<xsl:value-of select="value_postal_code"/>
							<xsl:value-of select="value_place"/>
						</div>
						<div class="pure-control-group">
							<label></label>
							<xsl:if test="is_inactive_party = 1">
								<xsl:value-of select="php:function('lang', 'inactive_party')"/>
							</xsl:if>
							<xsl:if test="is_inactive_party = 0">
								<xsl:value-of select="php:function('lang', 'active_party')"/>
							</xsl:if>													
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_number')"/>
							</label>
							<xsl:value-of select="value_account_number"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'phone')"/>
							</label>
							<xsl:value-of select="value_phone"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'mobile_phone')"/>
							</label>
							<xsl:value-of select="value_mobile_phone"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'fax')"/>
							</label>
							<xsl:value-of select="value_fax"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<xsl:value-of select="value_email"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'url')"/>
							</label>
							<xsl:value-of select="value_url"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'unit_leader')"/>
							</label>
							<xsl:value-of select="value_unit_leader"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<xsl:value-of select="value_comment"/>
						</div>
						<xsl:choose>
							<xsl:when test="use_fellesdata = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'organization')"/>
									</label>
									<xsl:value-of select="value_organization"/>
								</div>
							</xsl:when>
						</xsl:choose>
					</fieldset>
				</div>
				<div id="contracts">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'search_options')"/>
						</label>
						<div class="pure-custom">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_for')"/>
								</label>
								<input type="text" id="contract_query" name="contract_query" value=""></input>
								<label>
									<xsl:value-of select="php:function('lang', 'search_where')"/>
								</label>
								<select id="contract_search_options" name="contract_search_options">
									<xsl:apply-templates select="list_search_contract/options"/>
								</select>
							</div>
							<div class="pure-control-group">							
								<label>
									<xsl:value-of select="php:function('lang', 'status')"/>
								</label>
								<select id="contract_status" name="contract_status">
									<xsl:apply-templates select="list_status/options"/>
								</select>
								<label>
									<xsl:value-of select="php:function('lang', 'date')"/>
								</label>
								<input type="text" id="status_date" name="status_date" value=""></input>					
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
								</label>
								<select id="contract_type" name="contract_type">
									<xsl:apply-templates select="list_field_of_responsibility/options"/>
								</select>
							</div>										
						</div>
					</div>								
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
				<div id="documents">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'search_options')"/>
						</label>
						<div class="pure-custom">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_for')"/>
								</label>
								<input id="document_query" type="text" name="document_query" />
								<label>
									<xsl:value-of select="php:function('lang', 'search_where')"/>
								</label>
								<select id="document_search_option" name="document_search_option">
									<xsl:apply-templates select="list_document_search/options"/>
								</select>
								<label>
									<xsl:value-of select="php:function('lang', 'document_type')"/>
								</label>
								<select id="document_type_search" name="document_type_search">
									<xsl:apply-templates select="list_document_types/options"/>
								</select>																	
							</div>
						</div>
					</div>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_1'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</div>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>				
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>