
<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="login">
			<xsl:apply-templates select="login"/>
		</xsl:when>
		<xsl:when test="get_all">
			<xsl:apply-templates select="get_all"/>
		</xsl:when>
		<xsl:when test="export_data">
			<xsl:apply-templates select="export_data"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- login -->
<xsl:template xmlns:php="http://php.net/xsl" match="login">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="login">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'username')"/>
						</label>
						<input type="text" id="external_username" name="external_username" value="{value_external_username}" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'username')"/>
							</xsl:attribute>
						</input>
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'password')"/>
						</label>

						<input type="password" id="external_password" name="external_password" value="" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'password')"/>
							</xsl:attribute>
						</input>
					</div>
					<xsl:if test="value_token != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'token')"/>
							</label>
							<textarea class="pure-input-1-2" rows="12" >
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
								<xsl:value-of disable-output-escaping="yes" select="value_token"/>
							</textarea>
						</div>
					</xsl:if>
				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>

	</form>
</xsl:template>

<!-- get_all -->
<xsl:template xmlns:php="http://php.net/xsl" match="get_all">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="get_all">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'action')"/>
						</label>

						<select name="action" id="action" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'action')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="action_list/options"/>
						</select>
					</div>
					<xsl:if test="value_token != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'token')"/>
							</label>
							<textarea class="pure-input-1-2" rows="12" >
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
								<xsl:value-of disable-output-escaping="yes" select="value_token"/>
							</textarea>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'data')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="data_from_api"/>
						<table class="pure-table pure-table-bordered pure-custom">
							<tr>
								<td colspan="{nm_colspan}" width="100%">
									<xsl:call-template name="nextmatchs" />
								</td>
							</tr>
							<tr>
								<xsl:apply-templates select="table_heading/heading"/>
							</tr>
							<xsl:for-each select="table_data">
								<!--								<xsl:variable name="details" select="child::*/details"/>-->
								<tr>
									<xsl:for-each select="values">
										<td>
											<xsl:value-of disable-output-escaping="yes" select="value"/>
										</td>
										
										<!--										<xsl:value-of disable-output-escaping="yes" select="$details/ID"/>-->

									</xsl:for-each>
								</tr>
							</xsl:for-each>
						</table>
					</div>

				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>
	</form>
	<script type="text/javascript">

		$(function() {
			$('#action').change(function() {
				this.form.submit();
			});
		});
	</script>

</xsl:template>

<!-- export_data -->
<xsl:template xmlns:php="http://php.net/xsl" match="export_data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="export_data">
				<fieldset>
					<div class="pure-control-group">
						<label>
							Helseforetak
						</label>
						<select name="helseforetak_id" id="helseforetak_id" class="pure-input-1-2" >
							<xsl:apply-templates select="helseforetak_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'select')"/>
						</label>

						<table class="pure-table pure-table-bordered pure-custom">
							<thead>
								<tr>
									<th>
										<xsl:value-of select="php:function('lang', 'category')"/>

									</th>
									<th>
										<xsl:value-of select="php:function('lang', 'part of town')"/>

									</th>
								</tr>
							</thead>

							<tbody>
								<tr>
									<td>
										<table class="pure-table pure-table-bordered pure-custom">
											<thead>
												<tr>
													<th>#</th>
													<th>
														<xsl:value-of select="php:function('lang', 'name')"/>
													</th>
													<th>
														<xsl:value-of select="php:function('lang', 'select')"/>
													</th>
												</tr>
											</thead>

											<tbody>
												<xsl:for-each select="categories">
													<tr>
														<td>
															<xsl:value-of select="id"/>
														</td>
														<td>
															<xsl:value-of select="name"/>
														</td>
														<td>
															<input id="option-{id}" type="checkbox" name = "selected_categories[]" value="{id}">

																<xsl:if test="selected != 0">
																	<xsl:attribute name="checked" value="checked"/>
																</xsl:if>
															</input>
														</td>
													</tr>
												</xsl:for-each>
											</tbody>
										</table>
									</td>
									<td>
										<table class="pure-table pure-table-bordered pure-custom">
											<thead>
												<tr>
													<th>#</th>
													<th>
														<xsl:value-of select="php:function('lang', 'name')"/>
													</th>
													<th>
														<xsl:value-of select="php:function('lang', 'select')"/>
													</th>
												</tr>
											</thead>

											<tbody>
												<xsl:for-each select="part_of_towns">
													<tr>
														<td>
															<xsl:value-of select="id"/>
														</td>
														<td>
															<xsl:value-of select="name"/>
														</td>
														<td>
															<input id="option-{id}" type="checkbox" name = "selected_part_of_towns[]" value="{id}">

																<xsl:if test="selected != 0">
																	<xsl:attribute name="checked" value="checked"/>
																</xsl:if>
															</input>
														</td>
													</tr>
												</xsl:for-each>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'action')"/>
						</label>

						<select name="action" id="action" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'action')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="action_list/options"/>
						</select>
					</div>
					<xsl:if test="value_token != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'token')"/>
							</label>
							<textarea class="pure-input-1-2" rows="12" >
								<xsl:attribute name="disabled">
									<xsl:text>disabled</xsl:text>
								</xsl:attribute>
								<xsl:value-of disable-output-escaping="yes" select="value_token"/>
							</textarea>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'data')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="data_for_export"/>
					</div>

				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>
	</form>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" name="submit_data">
	<div class="proplist-col">
		<xsl:variable name="lang_send">
			<xsl:value-of select="php:function('lang', 'save')"/>
		</xsl:variable>
		<xsl:variable name="lang_cancel">
			<xsl:value-of select="php:function('lang', 'cancel')"/>
		</xsl:variable>
		<input class="pure-button pure-button-primary" type="submit" name="save" value="{$lang_send}">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Save the entry and return to list')"/>
			</xsl:attribute>
		</input>
<!--		<input class="pure-button pure-button-primary" type="button" name="cancel" value="{$lang_cancel}">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
			</xsl:attribute>
		</input>-->
	</div>


</xsl:template>

<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

<!-- New template-->
<xsl:template match="heading">
	<th>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</th>
</xsl:template>
<!-- New template-->
<xsl:template match="data">
	<th>
		<xsl:value-of disable-output-escaping="yes" select="value"/>
	</th>
</xsl:template>
