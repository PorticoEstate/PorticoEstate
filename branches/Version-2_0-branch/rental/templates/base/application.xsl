
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
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

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
				<input type="hidden" id="active_tab" name="active_tab"/>
				<div id="application">
					<fieldset>
						<xsl:if test="application/id != ''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{application/id}"/>
								<xsl:value-of select="application/id"/>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<xsl:variable name="lang_dimb">
								<xsl:value-of select="php:function('lang', 'dimb')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_dimb"/>
							</label>
							<input type="hidden" id="ecodimb_id" name="ecodimb_id"  value="{application/ecodimb_id}"/>
							<input type="text" id="ecodimb_name" name="ecodimb_name" value="{value_ecodimb_descr}">
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

							<select name="district_id">
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

							<select name="composite_type">
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
							<input type="text" id="date_start" name="date_start" size="10" readonly="readonly">
								<xsl:if test="application/date_start != 0 and application/date_start != ''">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('date', $date_format, number(application/date_start))"/>
									</xsl:attribute>
								</xsl:if>
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
							<input type="text" id="date_end" name="date_end" size="10" readonly="readonly">
								<xsl:if test="application/date_end != 0 and application/date_end != ''">
									<xsl:attribute name="value">
										<xsl:value-of select="php:function('date', $date_format, number(application/date_end))"/>
									</xsl:attribute>
								</xsl:if>
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
							<input type="checkbox" name="cleaning" id="cleaning" value="1">
								<xsl:if test="application/cleaning = 1">
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
							<select name="payment_method">
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
							<input type="text" name="identifier" value="{application/identifier}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:text>Ansattnummer</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'firstname')"/>
							</label>
							<input type="text" id="firstname" name="firstname" value="{application/firstname}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'lastname')"/>
							</label>
							<input type="text" id="lastname" name="lastname" value="{application/lastname}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'job_title')"/>
							</label>
							<input type="text" name="job_title" value="{application/job_title}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'company')"/>
							</label>
							<input type="text" id="company_name" name="company_name" value="{application/company_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'department')"/>
							</label>
							<input type="text" id="department" name="department" value="{application/department}">
								<xsl:attribute name="data-validation">
									<xsl:text>naming</xsl:text>
								</xsl:attribute>

							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'address')"/>
							</label>
							<input type="text" name="address1" value="{application/address1}"></input>
							<input type="text" name="address2" value="{application/address2}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'postal_code_place')"/>
							</label>
							<input type="text" name="postal_code" value="{application/postal_code}"></input>
							<input type="text" name="place" value="{application/place}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'account_number')"/>
							</label>
							<input type="text" name="account_number" value="{application/account_number}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'phone')"/>
							</label>
							<input type="text" name="phone" value="{application/phone}"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<input type="text" name="email" id="email" value="{application/email}">
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
								<xsl:value-of select="php:function('lang', 'unit_leader2')"/>
							</label>
							<input type="text" id="unit_leader" name="unit_leader" value="{application/unit_leader}"></input>
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
				<xsl:if test="step > 1">
					<div id="assignment">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'executive_officer')"/>
								</label>
								<select id="executive_officer" name="executive_officer">
									<xsl:apply-templates select="list_executive_officer/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_date_start">
									<xsl:value-of select="php:function('lang', 'assign_start')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_date_start"/>
								</label>
								<input type="text" id="assign_date_start" name="assign_date_start" size="10" readonly="readonly">
									<xsl:if test="application/assign_date_start != 0 and application/assign_date_start != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('date', $date_format, number(application/assign_date_start))"/>
										</xsl:attribute>
									</xsl:if>
									<!--xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_date_start"/>
									</xsl:attribute-->

								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_date_end">
									<xsl:value-of select="php:function('lang', 'assign_end')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_date_end"/>
								</label>
								<input type="text" id="assign_date_end" name="assign_date_end" size="10" readonly="readonly">
									<xsl:if test="application/assign_date_end != 0 and application/assign_date_end != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('date', $date_format, number(application/assign_date_end))"/>
										</xsl:attribute>
									</xsl:if>
									<!--xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_date_end"/>
									</xsl:attribute-->

								</input>
							</div>
							<div class="pure-control-group">
								<xsl:variable name="lang_status">
									<xsl:value-of select="php:function('lang', 'status')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_status"/>
								</label>
								<select name="status">
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_status"/>
									</xsl:attribute>
									<!--xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_status"/>
									</xsl:attribute-->
									<option value="">
										<xsl:value-of select="$lang_status"/>
									</option>
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'comment')"/>
								</label>
								<textarea cols="47" rows="7" name="comment">
									<xsl:value-of select="application/comment"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'details')"/>
								</label>
								<xsl:choose>
									<xsl:when test="additional_notes=''">
										<xsl:value-of select="php:function('lang', 'no additional notes')"/>
									</xsl:when>
									<xsl:otherwise>
										<div class = 'pure-u-md-1-2'>
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
									</xsl:otherwise>
								</xsl:choose>
							</div>
						
						</fieldset>
					</div>
				</xsl:if>

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
