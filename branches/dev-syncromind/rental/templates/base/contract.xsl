
<!-- $Id: contract.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
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
		<div class="toolbar">
			<div class="pure-g">
				<div class="pure-u-1-3">
					<div>
						<xsl:value-of select="php:function('lang', 'contract_number')"/>:<xsl:value-of select="value_contract_number"/>
					</div>
					<div>
						<xsl:value-of select="php:function('lang', 'parties')"/>:<xsl:value-of select="value_parties"/>
					</div>
					<div>
						<xsl:value-of select="php:function('lang', 'last_updated')"/>:<xsl:value-of select="value_last_updated"/>
					</div>
					<div>
						<xsl:value-of select="php:function('lang', 'name')"/>:<xsl:value-of select="value_name"/>
					</div>
					<div>
						<xsl:value-of select="php:function('lang', 'composite')"/>:<xsl:value-of select="value_composite"/>
					</div>
				</div>
				<div class="pure-u-2-3">
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
		</div>
	</div>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">

	<xsl:choose>
		<xsl:when test="//list_consistency_warnings!=''">
			<xsl:for-each select="list_consistency_warnings">
				<dl>
					<dt>
						<xsl:value-of select="php:function('lang', 'contract_warning')"/>
					</dt>
					<dd>
						<xsl:value-of select="warning"/>
					</dd>
				</dl>
			</xsl:for-each>			
		</xsl:when>
	</xsl:choose>
	
	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details" class="pure-g">
					<div class="pure-u-1 pure-u-lg-1-2">
						<input type="hidden" name="id" id="contract_id" value="{contract_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>
							<xsl:choose>
								<xsl:when test="contract_id = 0 or contract_id = ''">
									<input type="hidden" name="location_id" id="location_id" value="{location_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contract_type')"/>
							</label>
							<select id="contract_type" name="contract_type">
								<xsl:apply-templates select="list_contract_type/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'executive_officer')"/>
							</label>
							<select id="executive_officer" name="executive_officer">
								<xsl:apply-templates select="list_executive_officer/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_start')"/>
							</label>
							<input type="text" id="date_start" name="date_start" size="10" value="{value_date_start}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_end')"/>
							</label>
							<input type="text" id="date_end" name="date_end" size="10" value="{value_date_end}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'due_date')"/>
							</label>
							<input type="text" id="due_date" name="due_date" size="10" value="{value_due_date}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'invoice_header')"/>
							</label>
							<input type="text" name="invoice_header" value="{value_invoice_header}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_term')"/>
							</label>
							<select id="billing_term" name="billing_term">
								<xsl:apply-templates select="list_billing_term/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_start')"/>
							</label>
							<input type="text" id="billing_start_date" name="billing_start_date" size="10" value="{value_billing_start}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_end')"/>
							</label>
							<input type="text" id="billing_end_date" name="billing_end_date" size="10" value="{value_billing_end}" readonly="readonly"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'reference')"/>
							</label>
							<input type="text" name="reference" value="{value_reference}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'customer order id')"/>
							</label>
							<input type="number" step="1" name="customer_order_id" value="{value_customer_order_id}"></input>
						</div>
					</div>
					<div class="pure-u-1 pure-u-lg-1-2">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsibility')"/>
							</label>
							<xsl:choose>
								<xsl:when test="list_responsibility">
									<xsl:if test="list_responsibility != ''">
										<select id="responsibility_id" name="responsibility_id">
											<xsl:apply-templates select="list_responsibility/options"/>
										</select>
									</xsl:if>
									<xsl:if test="list_responsibility = ''">
										<input type="text" name="responsibility_id" id="responsibility_id" value="{value_responsibility_id}"/>
									</xsl:if>
								</xsl:when>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'service')"/>
							</label>
							<input type="text" name="service_id" value="{value_service}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_in')"/>
							</label>
							<input type="text" name="account_in" value="{value_account_in}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_out')"/>
							</label>
							<input type="text" name="account_out" value="{value_account_out}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'project_id')"/>
							</label>
							<input type="text" name="project_id" value="{value_project_id}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'security')"/>
							</label>
							<select id="security_type" name="security_type">
								<xsl:apply-templates select="list_security/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'security_amount')"/>
							</label>
							<xsl:value-of select="security_amount_simbol"/>
							<input type="text" name="security_amount" value="{value_security_amount}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'rented_area')"/>
							</label>
							<input type="text" name="rented_area" value="{value_rented_area}"></input>
							<xsl:value-of select="rented_area_simbol"/>
						</div>
						<xsl:choose>
							<xsl:when test="is_adjustable">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'adjustable')"/>
									</label>
									<input type="checkbox" name="adjustable" id="adjustable">
										<xsl:if test="is_adjustable = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'adjustment_interval')"/>
							</label>
							<select id="adjustment_interval" name="adjustment_interval">
								<xsl:apply-templates select="list_adjustment_interval/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'adjustment_share')"/>
							</label>
							<select id="adjustment_share" name="adjustment_share">
								<xsl:apply-templates select="list_adjustment_share/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'adjustment_year')"/>
							</label>
							<xsl:value-of select="value_adjustment_year"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'override adjustment start')"/>
							</label>
							<input type="text" id="override_adjustment_start" name="override_adjustment_start" size="10" value="{value_override_adjustment_start}" data-validation-optional="true" data-validation="date" data-validation-format="yyyy">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'year')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'movein')"/>
							</label>
							<xsl:choose>
								<xsl:when test="movein/url">
									<a href="{movein/url}">
										<xsl:value-of select="movein/created"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{movein/new_report}">
										<xsl:value-of select="php:function('lang', 'new')"/>
									</a>
								</xsl:otherwise>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'moveout')"/>
							</label>
							<xsl:choose>
								<xsl:when test="moveout/url">
									<a href="{moveout/url}">
										<xsl:value-of select="moveout/created"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{moveout/new_report}">
										<xsl:value-of select="php:function('lang', 'new')"/>
									</a>
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</div>
					<div class="pure-u-1 pure-u-lg-1-2">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<textarea cols="40" rows="10" name="comment" id="comment">
								<xsl:value-of select="value_comment"/>
							</textarea>
						</div>
						<xsl:choose>
							<xsl:when test="value_publish_comment">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'publish_comment')"/>
									</label>
									<input type="checkbox" name="publish_comment" id="publish_comment">
										<xsl:if test="value_publish_comment = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
					</div>
				</div>
				<xsl:choose>
					<xsl:when test="contract_id > 0">
						<div id="composite">
							<script type="text/javascript">
								var link_included_composites = <xsl:value-of select="link_included_composites"/>;
								var link_not_included_composites = <xsl:value-of select="link_not_included_composites"/>;
							</script>							
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
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="composite_query" name="composite_query" value=""></input>
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="composite_search_options" name="composite_search_options">
											<xsl:apply-templates select="list_composite_search/options"/>
										</select>										
									</div>
									<div class="pure-control-group">
										<xsl:choose>
											<xsl:when test="//list_furnish_types/options!=''">
												<label>
													<xsl:value-of select="php:function('lang', 'furnish_type')"/>
												</label>
												<select id="furnished_status" name="furnished_status">
													<xsl:apply-templates select="list_furnish_types/options"/>
												</select>
											</xsl:when>
										</xsl:choose>									
										<label>
											<xsl:value-of select="php:function('lang', 'availability')"/>
										</label>
										<select id="is_active" name="is_active">
											<xsl:apply-templates select="list_active/options"/>
										</select>
										<xsl:text> </xsl:text>
										<select id="has_contract" name="has_contract">
											<xsl:apply-templates select="list_has_contract/options"/>
										</select>																				
									</div>
								</div>
							</div>							
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_2'">
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
						<div id="parties">
							<script type="text/javascript">
								var link_included_parties = <xsl:value-of select="link_included_parties"/>;
								var link_not_included_parties = <xsl:value-of select="link_not_included_parties"/>;
							</script>	
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_3'">
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
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'search_options')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'search_for')"/>
										</label>
										<input type="text" id="party_query" name="party_query" value=""></input>
										<label>
											<xsl:value-of select="php:function('lang', 'search_where')"/>
										</label>
										<select id="party_search_options" name="party_search_options">
											<xsl:apply-templates select="list_party_search/options"/>
										</select>										
									</div>
									<div class="pure-control-group">									
										<label>
											<xsl:value-of select="php:function('lang', 'part_of_contract')"/>
										</label>
										<select id="party_type" name="party_type">
											<xsl:apply-templates select="list_party_types/options"/>
										</select>
										<label>
											<xsl:value-of select="php:function('lang', 'marked_as')"/>
										</label>
										<select id="active" name="active">
											<xsl:apply-templates select="list_status/options"/>
										</select>																				
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_4'">
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
						<div id="price">
							<script type="text/javascript">
								var link_included_price_items = <xsl:value-of select="link_included_price_items"/>;
								var link_not_included_price_items = <xsl:value-of select="link_not_included_price_items"/>;
								var	img_cal = <xsl:value-of select="img_cal"/>;
								var	dateformat = "<xsl:value-of select="dateformat"/>";
								var	lang_select_date = "<xsl:value-of select="php:function('lang', 'select date')"/>";
							</script>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_5'">
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
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_6'">
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
						<div id="invoice">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'filters')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">										
										<label>
											<xsl:value-of select="php:function('lang', 'invoice')"/>
										</label>
										<select id="invoice_id" name="invoice_id">
											<xsl:apply-templates select="list_invoices/options"/>
										</select>										
									</div>
								</div>
							</div>
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_7'">
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
									<xsl:if test="container = 'datatable-container_8'">
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
						<div id="notifications">
							<div>
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_9'">
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
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'new_notification')"/>
								</label>
								<div class="pure-custom">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'date')"/>
										</label>
										<input type="text" id="date_notification" name="date_notification" size="10" value="" readonly="readonly"/>												
										<label>
											<xsl:value-of select="php:function('lang', 'recurrence')"/>
										</label>
										<select id="notification_recurrence" name="notification_recurrence">
											<xsl:apply-templates select="list_notification_recurrence/options"/>
										</select>										
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'message')"/>
										</label>
										<input type="text" name="notification_message" id="notification_message" size="50" value="" />									
									</div>
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'user_or_group')"/>
										</label>
										<select id="notification_target" name="notification_target">
											<option value=''>
												<xsl:value-of select="php:function('lang', 'target_none')"/>
											</option>
											<xsl:apply-templates select="list_notification_user_group/option_group"/>
										</select>
										<xsl:text> </xsl:text>
										<label>
											<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
										</label>
										<select id="notification_location" name="notification_location">
											<option value=''>
												<xsl:value-of select="php:function('lang', 'target_none')"/>
											</option>
											<xsl:apply-templates select="list_field_of_responsibility/options"/>
										</select>
										<xsl:text> </xsl:text>
										<xsl:variable name="add_notification">
											<xsl:value-of select="php:function('lang', 'add')"/>
										</xsl:variable>											
										<input type="button" class="pure-button pure-button-primary" name="add_notification" id="add_notification" value="{$add_notification}" onClick="addNotification()" />									
									</div>									
								</div>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save_contract" value="{lang_save}" onMouseout="window.status='';return true;"/>
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
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details" class="pure-g">
					<div class="pure-u-1 pure-u-lg-1-2">
						<input type="hidden" name="id" id="contract_id" value="{contract_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>
							<xsl:choose>
								<xsl:when test="contract_id = 0 or contract_id = ''">
									<input type="hidden" name="location_id" id="location_id" value="{location_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contract_type')"/>
							</label>
							<xsl:value-of select="value_contract_type"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'executive_officer')"/>
							</label>
							<xsl:value-of select="value_executive_officer"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_start')"/>
							</label>
							<xsl:value-of select="value_date_start"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_end')"/>
							</label>
							<xsl:value-of select="value_date_end"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'due_date')"/>
							</label>
							<xsl:value-of select="value_due_date"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'invoice_header')"/>
							</label>
							<xsl:value-of select="value_invoice_header"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_term')"/>
							</label>
							<xsl:value-of select="value_billing_term"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_start')"/>
							</label>
							<xsl:value-of select="value_billing_start"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_end')"/>
							</label>
							<xsl:value-of select="value_billing_end"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'reference')"/>
							</label>
							<xsl:value-of select="value_reference"/>
						</div>
					</div>
					<div class="pure-u-1 pure-u-lg-1-2">

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'responsibility')"/>
							</label>
							<xsl:value-of select="value_responsibility_id"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'service')"/>
							</label>
							<xsl:value-of select="value_service"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_in')"/>
							</label>
							<xsl:value-of select="value_account_in"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_out')"/>
							</label>
							<xsl:value-of select="value_account_out"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'project_id')"/>
							</label>
							<xsl:value-of select="value_project_id"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'security')"/>
							</label>
							<xsl:value-of select="value_security_type"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'security_amount')"/>
							</label>
							<xsl:value-of select="security_amount_simbol"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="value_security_amount_view"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'rented_area')"/>
							</label>
							<xsl:value-of select="value_rented_area"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="rented_area_simbol"/>
						</div>
						<xsl:choose>
							<xsl:when test="is_adjustable = 1">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'adjustable')"/>
									</label>
									<input type="checkbox" name="adjustable" id="adjustable" disabled="disabled">
										<xsl:attribute name="checked" value="checked"/>
									</input>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'adjustment_interval')"/>
									</label>
									<xsl:value-of select="value_current_interval"/>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'adjustment_share')"/>
									</label>
									<xsl:value-of select="value_current_share"/>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'adjustment_year')"/>
									</label>
									<xsl:value-of select="value_adjustment_year"/>
								</div>
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'override adjustment start')"/>
									</label>
									<xsl:value-of select="value_override_adjustment_start"/>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<div class="pure-control-group">
									<label></label>
									<xsl:value-of select="php:function('lang', 'contract_not_adjustable')"/>
								</div>
							</xsl:otherwise>
						</xsl:choose>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'movein')"/>
							</label>
							<xsl:choose>
								<xsl:when test="movein/url">
									<a href="{movein/url}">
										<xsl:value-of select="movein/created"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{movein/new_report}">
										<xsl:value-of select="php:function('lang', 'new')"/>
									</a>
								</xsl:otherwise>
							</xsl:choose>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'moveout')"/>
							</label>
							<xsl:choose>
								<xsl:when test="moveout/url">
									<a href="{moveout/url}">
										<xsl:value-of select="moveout/created"/>
									</a>
								</xsl:when>
								<xsl:otherwise>
									<a href="{moveout/new_report}">
										<xsl:value-of select="php:function('lang', 'new')"/>
									</a>
								</xsl:otherwise>
							</xsl:choose>
						</div>

					</div>
					<div class="pure-u-1 pure-u-lg-1-2">

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<xsl:value-of select="value_comment"/>
						</div>
						<xsl:choose>
							<xsl:when test="value_publish_comment">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'publish_comment')"/>
									</label>
									<input type="checkbox" name="publish_comment" id="publish_comment" disabled="disabled">
										<xsl:if test="value_publish_comment = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
					</div>
				</div>
				<div id="composite">
					<script type="text/javascript">
						var link_included_composites = <xsl:value-of select="link_included_composites"/>;
					</script>							
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
				<div id="parties">
					<script type="text/javascript">
						var link_included_parties = <xsl:value-of select="link_included_parties"/>;
					</script>	
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_2'">
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
				<div id="price">
					<script type="text/javascript">
						var link_included_price_items = <xsl:value-of select="link_included_price_items"/>;
					</script>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_3'">
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
				<div id="invoice">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'filters')"/>
						</label>
						<div class="pure-custom">
							<div class="pure-control-group">										
								<label>
									<xsl:value-of select="php:function('lang', 'invoice')"/>
								</label>
								<select id="invoice_id" name="invoice_id">
									<xsl:apply-templates select="list_invoices/options"/>
								</select>										
							</div>
						</div>
					</div>
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_4'">
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
							<xsl:if test="container = 'datatable-container_5'">
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
				<div id="notifications">
					<div>
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_6'">
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

<xsl:template match="option_group">
	<optgroup label="{label}">
		<xsl:apply-templates select="options"/>
	</optgroup>
</xsl:template>