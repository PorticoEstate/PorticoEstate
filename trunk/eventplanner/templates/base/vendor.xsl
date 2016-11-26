
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
						<xsl:if test="vendor/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{vendor/id}"/>
								<xsl:value-of select="vendor/id"/>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'active')"/>
							</label>
							<input type="checkbox" name="active" id="active" value="1">
								<xsl:if test="vendor/active = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
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
							<input type="text" name="name" value="{vendor/name}">
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
							<input type="text" name="address_1" value="{vendor/address_1}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_1"/>
								</xsl:attribute>

							</input>
							<input type="text" name="address_2" value="{vendor/address_2}">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_2"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'postal_code_place')"/>
							</label>
							<input type="text" name="zip_code" value="{vendor/zip_code}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'zip_code')"/>
								</xsl:attribute>
							</input>
							<input type="text" name="city" value="{vendor/city}">
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
								<xsl:value-of select="php:function('lang', 'vendor_organization_number')"/>
							</label>
							<input type="text" id="lastname" name="vendor_organization_number" value="{vendor/vendor_organization_number}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'vendor_organization_number')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_name')"/>
							</label>
							<input type="text" name="contact_name" value="{vendor/contact_name}">
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
							<input type="text" name="contact_email" id="contact_email" value="{vendor/contact_email}">
								<xsl:attribute name="data-validation">
									<xsl:text>email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'email')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact_phone')"/>
							</label>
							<input type="text" name="contact_phone" value="{vendor/contact_phone}">
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
							<input type="text" name="account_number" value="{vendor/account_number}">
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
								<xsl:value-of select="vendor/description"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<textarea cols="47" rows="7" name="remark">
								<xsl:value-of select="vendor/remark"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'comment')"/>
							</label>
							<textarea cols="47" rows="7" name="comment">
								<xsl:value-of select="vendor/comment"/>
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
