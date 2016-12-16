
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<xsl:variable name="mode">
		<xsl:value-of select="mode"/>
	</xsl:variable>

	<div>
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required')"/>;
		</script>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<fieldset>
						<xsl:if test="moveout/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{moveout/id}"/>
								<xsl:value-of select="moveout/id"/>
							</div>
						</xsl:if>


						<div class="pure-control-group">
							<xsl:variable name="lang_contract">
								<xsl:value-of select="php:function('lang', 'contract')"/>
							</xsl:variable>
							<label>
								<xsl:choose>
									<xsl:when test="booking/contract_id > 0">
										<div id="contract_url">
											<a href="{contract_url}" target="_blank">
												<xsl:value-of select="$lang_contract"/>
											</a>
										</div>
									</xsl:when>
									<xsl:otherwise>
										<div id="contract_url">
											<xsl:value-of select="$lang_contract"/>
										</div>
									</xsl:otherwise>
								</xsl:choose>
							</label>
							<input type="hidden" id="contract_id" name="contract_id"  value="{booking/contract_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_contract"/>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_contract"/>
								</xsl:attribute>
							</input>
							<input type="text" id="contract_name" name="contract_name" value="{booking/contract_name}">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_contract"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
							<div id="contract_container"/>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'executive_officer')"/>
							</label>
							<div id="executive_officer" class="pure-custom">
								<xsl:value-of select="contract/executive_officer"/>
							</div>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'composite')"/>
							</label>
							<div id="composite" class="pure-custom">
								<xsl:value-of select="contract/composite"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'rented_area')"/>
							</label>
							<div id="rented_area" class="pure-custom">
								<xsl:value-of select="contract/rented_area"/>
							</div>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'security_amount')"/>
							</label>
							<div id="security_amount" class="pure-custom">
								<xsl:value-of select="contract/security_amount"/>
							</div>
						</div>



						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_start')"/>
							</label>
							<div id="date_start" class="pure-custom">
								<xsl:value-of select="contract/date_start"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'date_end')"/>
							</label>
							<div id="date_end" class="pure-custom">
								<xsl:value-of select="contract/date_end"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'type')"/>
							</label>
							<div id="type" class="pure-custom">
								<xsl:value-of select="contract/type"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'party')"/>
							</label>
							<div id="party" class="pure-custom">
								<xsl:value-of select="contract/party"/>
							</div>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'identifier')"/>
							</label>
							<div id="identifier" class="pure-custom">
								<xsl:value-of select="contract/identifier"/>
							</div>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'mobile_phone')"/>
							</label>
							<div id="mobile_phone" class="pure-custom">
								<xsl:value-of select="contract/mobile_phone"/>
							</div>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'department')"/>
							</label>
							<div id="department" class="pure-custom">
								<xsl:value-of select="contract/department"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contract_status')"/>
							</label>
							<div id="contract_status" class="pure-custom">
								<xsl:value-of select="contract/contract_status"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'rented_area')"/>
							</label>
							<div id="rented_area" class="pure-custom">
								<xsl:value-of select="contract/rented_area"/>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'billing_terms')"/>
							</label>
							<div id="term_label" class="pure-custom">
								<xsl:value-of select="contract/term_label"/>
							</div>
						</div>



						<div class="pure-control-group">
							<xsl:variable name="lang_category">
								<xsl:value-of select="php:function('lang', 'category')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_category"/>
							</label>
							<select name="category_id">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_category"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_category"/>
								</xsl:attribute>
								<xsl:apply-templates select="category_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'name')"/>
							</label>
							<input type="text" name="name" value="{moveout/name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'name')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_address_1">
								<xsl:value-of select="php:function('lang', 'address_1')"/>
							</xsl:variable>
							<xsl:variable name="lang_address_2">
								<xsl:value-of select="php:function('lang', 'address_2')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
							<input type="text" name="address_1" value="{moveout/address_1}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_1"/>
								</xsl:attribute>

							</input>
							<input type="text" name="address_2" value="{moveout/address_2}">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_2"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'postal_code_place')"/>
							</label>
							<input type="text" name="zip_code" value="{moveout/zip_code}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'zip_code')"/>
								</xsl:attribute>
							</input>
							<input type="text" name="city" value="{moveout/city}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'city')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'customer_organization_number')"/>
							</label>
							<input type="text" id="lastname" name="customer_organization_number" value="{moveout/customer_organization_number}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'customer_organization_number')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact name')"/>
							</label>
							<input type="text" name="contact_name" value="{moveout/contact_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact_name')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<input type="text" name="contact_email" id="contact_email" value="{moveout/contact_email}">
								<xsl:attribute name="data-validation">
									<xsl:text>email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'email')"/>
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
								<xsl:value-of select="php:function('lang', 'contact phone')"/>
							</label>
							<input type="text" name="contact_phone" value="{moveout/contact_phone}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact_phone')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_number')"/>
							</label>
							<input type="text" name="account_number" value="{moveout/account_number}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'account_number')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'description')"/>
							</label>
							<textarea cols="47" rows="7" name="description">
								<xsl:value-of select="moveout/description"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<textarea cols="47" rows="7" name="remark">
								<xsl:value-of select="moveout/remark"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<textarea cols="47" rows="7" name="comment">
								<xsl:value-of select="moveout/comment"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'details')"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_0'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl'/>
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
											<xsl:with-param name="data" select ='data'/>
											<xsl:with-param name="config" select ='config'/>
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')"/>
					</xsl:attribute>
				</input>
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


<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">

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
