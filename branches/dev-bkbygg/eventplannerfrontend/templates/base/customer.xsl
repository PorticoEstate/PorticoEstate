
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

	<div id="content" class="content">
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'please enter a valid organization number', 'please enter a valid account number')"/>;
		</script>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<fieldset>
						<xsl:if test="customer/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{customer/id}"/>
								<xsl:value-of select="customer/id"/>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<xsl:variable name="lang_category">
								<xsl:value-of select="php:function('lang', 'category')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_category"/>
							</label>
							<select name="category_id" class="pure-input-1-2" >
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
							<input type="text" name="name" value="{customer/name}" class="pure-input-1-2" >
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
								<xsl:value-of select="php:function('lang', 'address')"/>
								<xsl:text> 1</xsl:text>
							</xsl:variable>
							<xsl:variable name="lang_address_2">
								<xsl:value-of select="php:function('lang', 'address')"/>
								<xsl:text> 2</xsl:text>
							</xsl:variable>
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
							<input type="text" name="address_1" value="{customer/address_1}" class="pure-input-1-4" >
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_1"/>
								</xsl:attribute>

							</input>
							<input type="text" name="address_2" value="{customer/address_2}" class="pure-input-1-4" >
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_address_2"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'zip code')"/>
							</label>
							<input type="text" name="zip_code" value="{customer/zip_code}" class="pure-input-1-4" >
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'zip code')"/>
								</xsl:attribute>
							</input>
							<input type="text" name="city" value="{customer/city}" class="pure-input-1-4" >
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
								<xsl:value-of select="php:function('lang', 'organization number')"/>
							</label>
							<input type="text" id="organization_number" name="organization_number" value="{customer/organization_number}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>organization_number</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'organization number')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account number')"/>
							</label>
							<input type="text" id="account_number" name="account_number" value="{customer/account_number}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>account_number</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'account number')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'number of users')"/>
							</label>
							<input type="text" name="number_of_users" value="{customer/number_of_users}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>number</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'integer')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact name')"/>
							</label>
							<input type="text" name="contact_name" value="{customer/contact_name}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact name')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<input type="text" name="contact_email" id="contact_email" value="{customer/contact_email}" class="pure-input-1-2" >
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
								<xsl:value-of select="php:function('lang', 'contact phone')"/>
							</label>
							<input type="text" name="contact_phone" value="{customer/contact_phone}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact phone')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact name')"/>
								<xsl:text> 2</xsl:text>
							</label>
							<input type="text" name="contact2_name" id="contact2_name" value="{customer/contact2_name}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact name')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
								<xsl:text> 2</xsl:text>
							</label>
							<input type="text" name="contact2_email" id="contact2_email" value="{customer/contact2_email}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation">
									<xsl:text>email</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'email')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contact phone')"/>
								<xsl:text> 2</xsl:text>
							</label>
							<input type="text" name="contact2_phone" id="contact2_phone" value="{customer/contact2_phone}" class="pure-input-1-2" >
								<xsl:attribute name="data-validation-depends-on">
									<xsl:text>contact2_name</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-optional">
									<xsl:text>true</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'contact phone')"/>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'description')"/>
							</label>
							<textarea cols="47" rows="7" name="description" class="pure-input-1-2" >
								<xsl:value-of select="customer/description"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<textarea cols="47" rows="7" name="remark" class="pure-input-1-2" >
								<xsl:value-of select="customer/remark"/>
							</textarea>
						</div>
					</fieldset>
				</div>
				<div id="booking">
					<fieldset>
						<xsl:if test="booking_interval != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'booking interval')"/>
								</label>
								<xsl:value-of select="booking_interval"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="php:function('lang', 'hours')"/>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'booking')"/>
							</label>
							<div class="pure-custom">
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_1'">
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
