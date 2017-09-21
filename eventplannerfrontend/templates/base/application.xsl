
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
	<div id="content" class="content">
		<style type="text/css">
			#floating-box {
			position: relative;
			z-index: 1000;
			}
			#submitbox {
			display: none;
			}
		</style>
		<xsl:variable name="date_format">
			<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
		</xsl:variable>

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<script type="text/javascript">
				var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'next', 'save', 'send')"/>;
			</script>
			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>
					<!--
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
					-->
					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<div id="first_tab">
						<fieldset>
							<xsl:value-of disable-output-escaping="yes" select="application_condition"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'publishing')"/>
								</label>
								<table id="user_agreement_publishing" class="pure-table pure-custom pure-input-1-2" border="0" cellspacing="2" cellpadding="2">
									<tr>
										<td colspan = "2">
											<xsl:value-of disable-output-escaping="yes" select="user_agreement_text_1"/>
										</td>
									</tr>
									<tr>
										<th style="text-align:left">
											<xsl:value-of select="php:function('lang', 'yes')"/>
										</th>
										<th style="text-align:left">
											<xsl:value-of select="php:function('lang', 'no')"/>
										</th>
									</tr>
									<tr>
										<td>
											<input type="radio" name="agreement_1" id="agreement_1_1" value="1">
												<xsl:if test="application/agreement_1 = 1">
													<xsl:attribute name="checked" value="checked"/>
												</xsl:if>
											</input>
										</td>
										<td>
											<input type="radio" name="agreement_1" id="agreement_1_2" value="2">
												<xsl:if test="application/agreement_1 = 2">
													<xsl:attribute name="checked" value="checked"/>
												</xsl:if>
											</input>
										</td>
									</tr>
								</table>
								<input type="text" data-validation="publishing" size="1" style="visibility: hidden;">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'publishing')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'user agreement')"/>
								</label>
								<table id="user_agreement_table" class="pure-table pure-custom pure-input-1-2" border="0" cellspacing="2" cellpadding="2">
									<tr>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="user_agreement_text_2"/>
										</td>
									</tr>
									<tr>
										<th style="text-align:left" class="pure-input-1-2">
											<xsl:value-of select="php:function('lang', 'yes')"/>
										</th>
									</tr>
									<tr>
										<td style="text-align:left">
											<input type="checkbox" name="agreement_2" id="agreement_2" value="1">
												<xsl:if test="application/agreement_2 = 1">
													<xsl:attribute name="checked" value="checked"/>
												</xsl:if>
											</input>
										</td>
									</tr>
								</table>
								<input type="text" data-validation="user_agreement_2" size="1" style="visibility: hidden;">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'user agreement')"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'application')"/>
							</legend>
							<xsl:if test="application/id > 0">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<input type="hidden" id="application_id" name="id" value="{application/id}"/>
									<xsl:value-of select="application/id"/>
								</div>
							</xsl:if>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'category')"/>
								</label>
								<xsl:call-template name="categories"/>
							</div>


							<div class="pure-control-group">
								<xsl:variable name="lang_vendor">
									<xsl:value-of select="php:function('lang', 'vendor name')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_vendor"/>
								</label>


								<select id="vendor_id" name="vendor_id" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_vendor"/>
									</xsl:attribute>
									<xsl:apply-templates select="vendor_list/options"/>
								</select>

								<xsl:text> </xsl:text>
								<a href="{new_vendor_url}" target="_blank">
									<xsl:value-of select="php:function('lang', 'new')"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="php:function('lang', 'vendor')"/>
								</a>
							</div>
<!--
							<div class="pure-control-group">
								<xsl:variable name="lang_vendor">
									<xsl:value-of select="php:function('lang', 'vendor name')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_vendor"/>
								</label>
								<input type="hidden" id="vendor_id" name="vendor_id"  value="{application/vendor_id}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_vendor"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="$lang_vendor"/>
									</xsl:attribute>
								</input>
								<input type="text" id="vendor_name" name="vendor_name" value="{application/vendor_name}" class="pure-input-1-2" >
									<xsl:attribute name="placeholder">
										<xsl:value-of select="$lang_vendor"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</input>


								<xsl:text> </xsl:text>
								<a href="{new_vendor_url}" target="_blank">
									<xsl:value-of select="php:function('lang', 'new')"/>
									<xsl:text> </xsl:text>
									<xsl:value-of select="php:function('lang', 'vendor')"/>
								</a>
								<div id="vendor_container"/>
							</div>	-->
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contact name')"/>
								</label>
								<input type="text" id="contact_name" name="contact_name" value="{application/contact_name}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'contact name')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'contact name')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contact email')"/>
								</label>
								<input type="text" id="contact_email" name="contact_email" value="{application/contact_email}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'contact email')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'contact email')"/>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'contact phone')"/>
								</label>
								<input type="text" id="contact_phone" name="contact_phone" value="{application/contact_phone}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'contact phone')"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'contact phone')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'other participants')"/>
								</label>
								<textarea cols="47" rows="7" name="other_participants" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'other participants')"/>
									</xsl:attribute>
									<xsl:value-of select="application/other_participants"/>
								</textarea>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'program data')"/>
							</legend>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'program title')"/>
								</label>
								<input type="text" id="title" name="title" value="{application/title}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'program title')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'program title')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'program description')"/>
								</label>
								<textarea cols="47" rows="7" name="description" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'might be published')"/>
									</xsl:attribute>
									<xsl:value-of select="application/description"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'program type')"/>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th>
													<xsl:value-of select="php:function('lang', 'select')"/>
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'program type')"/>
												</th>
											</tr>
										</thead>
										<tbody id="application_tbody_types">
											<xsl:for-each select="application_type_list">
												<tr>
													<td>
														<input type="checkbox" name="types[]" value="{id}">
															<xsl:if test="selected = 1">
																<xsl:attribute name="checked" value="checked"/>
															</xsl:if>
														</input>
													</td>
													<td>
														<xsl:value-of disable-output-escaping="yes" select="name"/>
													</td>
												</tr>
											</xsl:for-each>
										</tbody>
									</table>
								</div>
								<input type="text" data-validation="application_types" size="1" style="visibility: hidden;">
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'program type')"/>
									</xsl:attribute>
								</input>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'application public type')"/>
								</label>
								<select id="non_public" name="non_public" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'application public type')"/>
									</xsl:attribute>
									<xsl:apply-templates select="list_public_types/options"/>
								</select>
							</div>

							<div class="pure-control-group">
								<xsl:variable name="lang_date_start">
									<xsl:value-of select="php:function('lang', 'date start')"/>
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
									<xsl:value-of select="php:function('lang', 'date end')"/>
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
									<xsl:value-of select="php:function('lang', 'event timespan')"/>
								</label>
								<input type="text" id="timespan" name="timespan" value="{application/timespan}">
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'event timespan')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'event timespan')"/>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'charge per unit')"/>
								</label>
								<input type="text" id="charge_per_unit" name="charge_per_unit" value="{application/charge_per_unit}">
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'charge per unit')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'integer')"/>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'number of units')"/>
								</label>
								<input type="text" id="number_of_units" name="number_of_units" value="{application/number_of_units}">
									<xsl:attribute name="data-validation">
										<xsl:text>number</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'number of units')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'integer')"/>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'total amount')"/>
								</label>
								<input id="total_amount" type="text" disabled="disabled"/>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'remark')"/>
								</label>
								<textarea cols="47" rows="7" name="remark" class="pure-input-1-2" >
									<xsl:value-of select="application/remark"/>
								</textarea>
							</div>
						</fieldset>

					</div>
					<div id="demands">
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'arena requirement')"/>
							</legend>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'size of stage')"/>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-input-1-2" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th>
													<xsl:value-of select="php:function('lang', 'width')"/>
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'depth')"/>
												</th>
												<th>
													<xsl:value-of select="php:function('lang', 'sum')"/>
												</th>
											</tr>
										</thead>
										<tbody id="application_stage_size">
											<tr>
												<td>
													<input type="text" id="stage_width" name="stage_width" value="{application/stage_width}" size="2">
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'width')"/>
														</xsl:attribute>
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="data-validation-optional">
															<xsl:text>true</xsl:text>
														</xsl:attribute>
													</input>
												</td>
												<td>
													<input type="text" id="stage_depth" name="stage_depth" value="{application/stage_depth}" size="2">
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="title">
															<xsl:value-of select="php:function('lang', 'depth')"/>
														</xsl:attribute>
														<xsl:attribute name="data-validation-optional">
															<xsl:text>true</xsl:text>
														</xsl:attribute>
													</input>
												</td>
												<td>
													<input id="stage_size" type="text" disabled="disabled" size="3"/>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'stage requirement')"/>
								</label>
								<textarea cols="47" rows="7" name="stage_requirement" class="pure-input-1-2">
									<xsl:value-of select="application/stage_requirement"/>
								</textarea>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'wardrobe')"/>
								</label>
								<input type="checkbox" name="wardrobe" id="wardrobe" value="1">
									<xsl:if test="application/wardrobe = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'audience limit')"/>
								</label>
								<input type="text" id="audience_limit" name="audience_limit" value="{application/audience_limit}"  size="5">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'audience limit')"/>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'integer')"/>
									</xsl:attribute>
								</input>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'labour support')"/>
							</legend>

							<div class="pure-control-group">
								<label>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered pure-input-1-2" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th></th>
												<th>
													<xsl:value-of select="php:function('lang', 'minute')"/>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'rig up min before')"/>
												</td>
												<td>
													<input type="text" id="rig_up_min_before" name="rig_up_min_before" value="{application/rig_up_min_before}" size="5">
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="data-validation-error-msg">
															<xsl:value-of select="php:function('lang', 'rig up min before')"/>
														</xsl:attribute>
														<xsl:attribute name="placeholder">
															<xsl:value-of select="php:function('lang', 'integer')"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'rig down min after')"/>
												</td>
												<td>
													<input type="text" id="rig_down_min_after" name="rig_down_min_after" value="{application/rig_down_min_after}" size="5">
														<xsl:attribute name="data-validation">
															<xsl:text>number</xsl:text>
														</xsl:attribute>
														<xsl:attribute name="data-validation-error-msg">
															<xsl:value-of select="php:function('lang', 'rig down min after')"/>
														</xsl:attribute>
														<xsl:attribute name="placeholder">
															<xsl:value-of select="php:function('lang', 'integer')"/>
														</xsl:attribute>
													</input>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'technical support')"/>
							</legend>
							<div class="pure-control-group">
								<label>
								</label>
								<div class="pure-custom">
									<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
										<thead>
											<tr>
												<th>Hva</th>
												<th>Ja</th>
												<th>Fritekst</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Strøm</td>
												<td>
													<input type="checkbox" name="power" id="power" value="1">
														<xsl:if test="application/power = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="power_remark" name="power_remark" value="{application/power_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Lydanlegg</td>
												<td>
													<input type="checkbox" name="sound" id="sound" value="1">
														<xsl:if test="application/sound = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="sound_remark" name="sound_remark" value="{application/sound_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Lyssetting/blending</td>
												<td>
													<input type="checkbox" name="light" id="light" value="1">
														<xsl:if test="application/light = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="light_remark" name="light_remark" value="{application/light_remark}">
													</input>
												</td>
											</tr>
											<tr>
												<td>Piano</td>
												<td>
													<input type="checkbox" name="piano" id="piano" value="1">
														<xsl:if test="application/piano = 1">
															<xsl:attribute name="checked" value="checked"/>
														</xsl:if>
													</input>
												</td>
												<td>
													<input type="text" id="piano_remark" name="piano_remark" value="{application/piano_remark}">
													</input>
												</td>

											</tr>
											<tr>
												<td>Annet utstyr (prosjektor, lerret mm)</td>
												<td>
												</td>
												<td>
													<input type="text" id="equipment_remark" name="equipment_remark" value="{application/equipment_remark}">
													</input>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

						</fieldset>
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'raider')"/>
							</legend>
							<div class="pure-control-group">
								<label>
								</label>
								<textarea cols="47" rows="7" name="raider" class="pure-input-1-2">
									<xsl:value-of select="application/raider"/>
								</textarea>
							</div>
						</fieldset>
					</div>
					<div id='files'>
						<script type="text/javascript">
							var multi_upload_parans = <xsl:value-of select="multi_upload_parans"/>;
						</script>
						<xsl:value-of disable-output-escaping="yes" select="application_condition"/>
						<fieldset>
							<legend>
								<xsl:text>CV</xsl:text>
							</legend>
							<xsl:call-template name="file_upload">
								<xsl:with-param name="section">cv</xsl:with-param>
							</xsl:call-template>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_2'">
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
						<fieldset>
							<legend>
								<xsl:value-of select="php:function('lang', 'program description')"/>
							</legend>
							<xsl:call-template name="file_upload">
								<xsl:with-param name="section">documents</xsl:with-param>
							</xsl:call-template>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'files')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_3'">
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

					<div id='calendar'>
						<fieldset>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'date start')"/>
								</label>
								<xsl:if test="application/date_start != 0 and application/date_start != ''">
									<xsl:value-of select="php:function('date', $date_format, number(application/date_start))"/>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'date end')"/>
								</label>
								<xsl:if test="application/date_end != 0 and application/date_end != ''">
									<xsl:value-of select="php:function('date', $date_format, number(application/date_end))"/>
								</xsl:if>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'event timespan')"/>
								</label>
								<xsl:if test="application/date_end != 0 and application/timespan != ''">
									<xsl:value-of select="application/timespan"/>
								</xsl:if>
							</div>

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'event start')"/>
								</label>
								<input type="text" id="from_" name="from_" size="16" readonly="readonly">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'select event start')"/>
									</xsl:attribute>
								</input>
							</div>
							<!--div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'to')"/>
								</label>
								<input type="text" id="to_" name="to_" size="16" readonly="readonly">
								</input>
							</div-->

							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'event dates')"/>
								</label>
								<div class="pure-custom">
									<xsl:for-each select="datatable_def">
										<xsl:if test="container = 'datatable-container_1'">
											<xsl:call-template name="table_setup">
												<xsl:with-param name="container" select ='container'/>
												<xsl:with-param name="requestUrl" select ='requestUrl'/>
												<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
												<xsl:with-param name="tabletools" select ='tabletools'/>
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
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'next')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="save" id="save_button_bottom" onClick="validate_submit();">
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
