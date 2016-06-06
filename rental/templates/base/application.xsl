
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
		<xsl:when test="adjustment_price">
			<xsl:apply-templates select="adjustment_price" />
			
		</xsl:when>
	</xsl:choose>
	
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
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
				<div id="application">
					<fieldset>
						<div class="pure-control-group">
							<xsl:variable name="lang_dimb">
								<xsl:value-of select="php:function('lang', 'dimb')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_dimb"/>
							</label>
							<input type="hidden" id="ecodimb" name="values[ecodimb]"  value="{value_ecodimb}"/>
							<input type="text" id="ecodimb_name" name="values[ecodimb_name]" value="{value_ecodimb_descr}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_dimb"/>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_dimb"/>
								</xsl:attribute>
							</input>
							<div id="ecodimb_container"/>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_district">
								<xsl:value-of select="php:function('lang', 'district')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_district"/>
							</label>

							<select name="values[district_id]">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_district"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_district"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="$lang_district"/>
								</option>
								<xsl:apply-templates select="district_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_composite_type">
								<xsl:value-of select="php:function('lang', 'what')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_composite_type"/>
							</label>

							<select name="values[composite_type_id]">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_composite_type"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_composite_type"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="$lang_composite_type"/>
								</option>
								<xsl:apply-templates select="composite_type_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_date_start">
								<xsl:value-of select="php:function('lang', 'date_start')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_date_start"/>
							</label>
							<input type="text" id="date_start" name="date_start" size="10" value="{value_date_start}" readonly="readonly">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_date_start"/>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_date_end">
								<xsl:value-of select="php:function('lang', 'date_end')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_date_end"/>
							</label>
							<input type="text" id="date_end" name="date_end" size="10" value="{value_date_end}" readonly="readonly">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_date_end"/>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'cleaning')"/>
							</label>
							<input type="checkbox" name="values[cleaning]" id="cleaning">
								<xsl:if test="value_cleaning = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_payment_method">
								<xsl:value-of select="php:function('lang', 'payment method')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_payment_method"/>
							</label>
							<select name="values[payment_method]">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_payment_method"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_payment_method"/>
								</xsl:attribute>
								<option value="">
									<xsl:value-of select="$lang_payment_method"/>
								</option>
								<xsl:apply-templates select="payment_method_list/options"/>
							</select>
						</div>
					</fieldset>
				</div>
				<div id="party">
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
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>

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
									<xsl:text>required</xsl:text>
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
				<div id="assignment">
					<fieldset>
					</fieldset>
				</div>

			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save" value="{lang_save}" onMouseout="window.status='';return true;"/>
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


<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="showing">
					<!--fieldset>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'title')"/>
							</label>
							<xsl:value-of select="value_title"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>						
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'agresso_id')"/>
							</label>
							<xsl:value-of select="value_agresso_id"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_area')"/>
							</label>
							<div class="pure-custom">
								<div>
									<input type="radio" name="is_area" value="true" disabled="disabled">
										<xsl:if test="is_area = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="php:function('lang', 'calculate_price_per_area')"/>
								</div>
								<div>
									<input type="radio" name="is_area" value="false" disabled="disabled">
										<xsl:if test="is_area = 0">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input> 
									<xsl:value-of select="php:function('lang', 'calculate_price_apiece')"/>
								</div>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'type')"/>
							</label>
							<xsl:value-of select="lang_current_price_type"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'price')"/>
							</label>
							<xsl:value-of select="value_price_formatted"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_inactive')"/>
							</label>
							<input type="checkbox" name="is_inactive" id="is_inactive" disabled="disabled">
								<xsl:if test="is_inactive = 1">
									<xsl:attribute name="checked" value="checked"/>
								</xsl:if>
							</input>
							<xsl:if test="has_active_contract = 1">
								<xsl:value-of select="lang_price_element_in_use"/>
							</xsl:if>									
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_adjustable')"/>
							</label>
							<xsl:value-of select="lang_adjustable_text"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'is_standard')"/>
							</label>
							<xsl:value-of select="lang_standard_text"/>
						</div>
					</fieldset-->
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
