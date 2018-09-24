
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
	<div class="content">
		<style type="text/css">
			#floating-box {
			position: relative;
			z-index: 1000;
			}
			#submitbox {
			display: none;
			}
		</style>
		<script>
			var lang_descr = "<xsl:value-of select="lang_descr"/>";
			var lang_selected = "<xsl:value-of select="lang_selected"/>";
		</script>
		<xsl:variable name="date_format">
			<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		</xsl:variable>

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" onsubmit="return process_list()" class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<div id="floating-box">
						<div id="submitbox">
							<xsl:variable name="lang_cancel">
								<xsl:value-of select="php:function('lang', 'cancel')"/>
							</xsl:variable>
							<xsl:variable name="lang_save">
								<xsl:value-of select="php:function('lang', 'next')"/>
							</xsl:variable>

							<table width="200px">
								<tbody>
									<tr>
										<td width="200px">
											<input type="button" class="pure-button pure-button-primary" name="save" id="save_button" onClick="validate_submit();">
												<xsl:attribute name="value">
													<xsl:value-of select="$lang_save"/>
												</xsl:attribute>
												<xsl:attribute name="title">
													<xsl:value-of select="$lang_save"/>
												</xsl:attribute>
											</input>
										</td>
										<td>
											<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_cancel}" onClick="window.location = '{cancel_url}';">
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
												</xsl:attribute>
											</input>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<input id="contact_id" name="contact_id" value="{person_data/contact_id}" type="hidden"></input>
					<input id="owner" name="owner" value="{person_data/owner}" type="hidden"></input>

					<div id="person_data">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Prefix')"/>
								</label>
								<input type="text" id="per_prefix" name="per_prefix" value="{person_data/per_prefix}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Prefix')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'First name')"/>
								</label>
								<input type="text" id="per_first_name" name="per_first_name" value="{person_data/per_first_name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'First name')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'First name')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Middle Name')"/>
								</label>
								<input type="text" id="per_middle_name" name="per_middle_name" value="{person_data/per_middle_name}">
<!--								<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Middle Name')"/>
									</xsl:attribute>-->
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Middle Name')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Last name')"/>
								</label>
								<input type="text" id="per_last_name" name="per_last_name" value="{person_data/per_last_name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Last name')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Last name')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Title')"/>
								</label>
								<input type="text" id="per_title" name="per_title" value="{person_data/per_title}">
<!--									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Title')"/>
									</xsl:attribute>-->
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Title')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Department')"/>
								</label>
								<input type="text" id="per_department" name="per_department" value="{person_data/per_department}">
<!--									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Department')"/>
									</xsl:attribute>-->
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Department')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Private')"/>
								</label>
								<input type="checkbox" name="access" id="access" value="1">
									<xsl:if test="person_data/access = 'private'">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Birthday')"/>
								</label>
								<input type="text" id="per_birthday" name="per_birthday" size="10" readonly="readonly">
									<xsl:if test="person_data/per_birthday != 0 and person_data/per_birthday != ''">
										<xsl:attribute name="value">
											<xsl:value-of select="php:function('date', $date_format, number(person_data/per_birthday))"/>
										</xsl:attribute>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Suffix')"/>
								</label>
								<input type="text" id="per_suffix" name="per_suffix" value="{person_data/per_suffix}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Suffix')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Initials')"/>
								</label>
								<input type="text" id="per_initials" name="per_initials" value="{person_data/per_initials}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Initials')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Sound')"/>
								</label>
								<input type="text" id="per_sound" name="per_sound" value="{person_data/per_sound}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Sound')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Public Key')"/>
								</label>
								<input type="text" id="per_pubkey" name="per_pubkey" value="{person_data/per_pubkey}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Public Key')"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
					</div>

					<div id="orgs">
						<fieldset>
							<div class="pure-form pure-form-stacked">
								<div class="pure-g">
									<div class="pure-u-1 pure-u-md-1-3">
										<label>
											<xsl:value-of select="php:function('lang', 'default organizations')"/>
										</label>
										<select id="preferred_org" name="preferred_org" class="pure-input-1">
											<xsl:apply-templates select="current_orgs/options"/>
										</select>
									</div>
								</div>
								<div class="pure-g">
									<div class="pure-u-1 pure-u-md-1-3">
										<label>
											<xsl:value-of select="php:function('lang', 'all organizations')"/>
										</label>
										<select multiple="true" id="all_orgs" name="all_orgs" class="pure-input-1">
											<xsl:apply-templates select="all_orgs/options"/>
										</select>
									</div>
									<div class="pure-u-1 pure-u-md-1-6">
										<label for="last-name"> </label>
										<div class="pure-input-1">
											<button type="button" class="button-xsmall pure-button selector-add"> &gt;&gt; </button>
											<br/>
											<button type="button" class="button-xsmall pure-button selector-remove"> &lt;&lt; </button>
										</div>
									</div>
									<div class="pure-u-1 pure-u-md-1-3">
										<label>
											<xsl:value-of select="php:function('lang', 'current organizations')"/>
										</label>
										<select multiple="true" id="current_orgs" name="current_orgs[]" class="pure-input-1">
											<xsl:apply-templates select="current_orgs/options"/>
										</select>
									</div>
								</div>
							</div>
						</fieldset>
					</div>

					<div id="categories">
						<fieldset>
							<div class="pure-g">
								<div class="pure-u-1 pure-u-md-1-3">
									<label>
										<xsl:value-of select="php:function('lang', 'all categories')"/>
									</label>
									<select multiple="true" id="all_categories" name="all_categories" class="pure-input-1">
										<xsl:apply-templates select="all_cats/options"/>
									</select>
								</div>
								<div class="pure-u-1 pure-u-md-1-6">
									<label for="last-name"> </label>
									<div class="pure-input-1">
										<button type="button" class="button-xsmall pure-button selector-add-categories"> &gt;&gt; </button>
										<br/>
										<button type="button" class="button-xsmall pure-button selector-remove-categories"> &lt;&lt; </button>
									</div>
								</div>
								<div class="pure-u-1 pure-u-md-1-3">
									<label>
										<xsl:value-of select="php:function('lang', 'current categories')"/>
									</label>
									<select multiple="true" id="current_categories" name="current_categories[]" class="pure-input-1">
										<xsl:apply-templates select="current_cats/options"/>
									</select>
								</div>
							</div>
						</fieldset>
					</div>

					<div id="communications">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Communication data')"/>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th>
													<xsl:value-of select="php:function('lang', 'Description')"/>
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'Value')"/>
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'Preferred')"/>
												</th>
											</tr>
										</thead>
										<tbody>
											<xsl:for-each select="comm_data">
												<tr>
													<td>
														<xsl:value-of disable-output-escaping="yes" select="comm_description"/>
													</td>
													<td>
														<input type="text" name="comm_data[{comm_description}]" value="{comm_data}"></input>
													</td>
													<td>
														<input type="radio" name="preferred_comm_data" value="{comm_description}">
															<xsl:if test="preferred = 'Y'">
																<xsl:attribute name="checked" value="checked"/>
															</xsl:if>
														</input>
													</td>
												</tr>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
					</div>

					<div id="address">
						<fieldset>
							<input id="addr_id" name="addr_id" value="{addr_data/key_addr_id}" type="hidden"></input>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Address 1')"/>
								</label>
								<input type="text" id="addr_add1" name="addr_add1" value="{addr_data/addr_add1}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Address 1')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Address 2')"/>
								</label>
								<input type="text" id="addr_add2" name="addr_add2" value="{addr_data/addr_add2}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Address 2')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'City')"/>
								</label>
								<input type="text" id="addr_city" name="addr_city" value="{addr_data/addr_city}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'City')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'State')"/>
								</label>
								<input type="text" id="addr_state" name="addr_state" value="{addr_data/addr_state}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'State')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Postal code')"/>
								</label>
								<input type="text" id="addr_postal_code" name="addr_postal_code" value="{addr_data/addr_postal_code}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Postal code')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Country')"/>
								</label>
								<input type="text" id="addr_country" name="addr_country" value="{addr_data/addr_country}">
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'Country')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Type')"/>
								</label>
								<select id="addr_type" name="addr_type">
									<xsl:apply-templates select="addr_type/options"/>
								</select>
							</div>
						</fieldset>
					</div>
                            
					<xsl:choose>
						<xsl:when test="mode = 'edit'">
							<xsl:if test="person_data/contact_id > 0">
								<div id="others">
									<fieldset>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'Description')"/>
											</label>
											<input type="text" name="description" id="description" size="30" value="" />
										</div>
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'Value')"/>
											</label>
											<input type="text" name="value" id="value" size="10" value="" />
										</div>
										<div class="pure-control-group">
											<label> </label>
											<xsl:variable name="add">
												<xsl:value-of select="php:function('lang', 'add')"/>
											</xsl:variable>
											<input type="button" class="pure-button" name="add" id="add" value="{$add}" onClick="addOthersData()" />
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
									</fieldset>
								</div>
							</xsl:if>
						</xsl:when>
					</xsl:choose>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="save" id="save_button_bottom" onClick="validate_submit();">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save"/>
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
	</div>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div class="content">
		<style type="text/css">
			#floating-box {
			position: relative;
			z-index: 1000;
			}
			#submitbox {
			display: none;
			}
		</style>
		<script>
			var lang_descr = "<xsl:value-of select="lang_descr"/>";
			var lang_selected = "<xsl:value-of select="lang_selected"/>";
		</script>
		<xsl:variable name="date_format">
			<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		</xsl:variable>

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}"  class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
                           
					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<input id="contact_id" name="contact_id" value="{person_data/contact_id}" type="hidden"></input>
					<input id="owner" name="owner" value="{person_data/owner}" type="hidden"></input>

					<div id="person_data">
						<fieldset>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Full name')"/>
								</label>
								<xsl:value-of select="person_data/per_full_name"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'First name')"/>
								</label>
								<xsl:value-of select="person_data/per_first_name"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Middle Name')"/>
								</label>
								<xsl:value-of select="person_data/per_middle_name"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
								<xsl:value-of select="person_data/per_active"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Company Name')"/>
								</label>
								<xsl:value-of select="person_data/org_name"/>
							</div>
							<xsl:for-each select="comm_data">
								<xsl:if test="preferred = 'Y'">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="php:function('lang', 'Preferred')"/>
										</label>
										<xsl:value-of select="comm_description"/>
									</div>
								</xsl:if>
								<xsl:if test="comm_data != ''">
									<div class="pure-control-group">
										<label>
											<xsl:value-of select="comm_description"/>
										</label>
										<xsl:value-of select="comm_data"/>
									</div>
								</xsl:if>
							</xsl:for-each>
							<xsl:for-each select="others_data">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="other_name"/>
									</label>
									<xsl:value-of select="other_value"/>
								</div>
							</xsl:for-each>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Owner')"/>
								</label>
								<xsl:value-of select="person_data/owner_name"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Access')"/>
								</label>
								<xsl:value-of select="person_data/access"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Category')"/>
								</label>
								<xsl:for-each select="current_cats/options">
									<xsl:value-of select="name"/>
									<xsl:value-of select="phpgw:conditional(not(position() = last()), ', ', '')"/>
								</xsl:for-each>
							</div>
						</fieldset>
					</div>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:if test="vcard = 1">
								<xsl:value-of select="php:function('lang', 'done')"/>
							</xsl:if>
							<xsl:if test="vcard = 0">
								<xsl:value-of select="php:function('lang', 'cancel')"/>
							</xsl:if>
						</xsl:attribute>
					</input>
					<xsl:if test="vcard = 1">
						<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{edit_url}';">
							<xsl:attribute name="value">
								<xsl:value-of select="php:function('lang', 'edit')"/>
							</xsl:attribute>
						</input>
					</xsl:if>
				</div>
			</form>
		</div>
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
