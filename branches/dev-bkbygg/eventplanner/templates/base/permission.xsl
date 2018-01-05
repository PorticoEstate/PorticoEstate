
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

	<div class="content">
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'please enter a valid organization number', 'please enter a valid account number')"/>;
		</script>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<fieldset>
						<xsl:if test="permission/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{permission/id}"/>
								<xsl:value-of select="permission/id"/>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<xsl:variable name="lang_category">
								<xsl:value-of select="php:function('lang', 'category')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_category"/>
							</label>
							<select id="object_type" name="object_type">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_category"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_category"/>
								</xsl:attribute>
								<xsl:apply-templates select="object_type_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_user">
								<xsl:value-of select="php:function('lang', 'user')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_user"/>
							</label>
							<select id="subject_id" name="subject_id">
								<xsl:attribute name="title">
									<xsl:value-of select="$lang_user"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_user"/>
								</xsl:attribute>
								<xsl:apply-templates select="subject_list/options"/>
							</select>

						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_object">
								<xsl:value-of select="php:function('lang', 'object')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_object"/>
							</label>
							<input type="hidden" id="object_id" name="object_id"  value="{permission/object_id}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_object"/>
								</xsl:attribute>
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_object"/>
								</xsl:attribute>
							</input>
							<input type="text" id="object_name" name="object_name" value="{permission/object_name}">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="$lang_object"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
							<div id="object_container"/>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_permission">
								<xsl:value-of select="php:function('lang', 'permission')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_permission"/>
							</label>
							<div class="pure-custom">
								<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
									<thead>
										<tr>
											<th>
												<xsl:value-of select="php:function('lang', 'read')"/>
											</th>
											<th>
												<xsl:value-of select="php:function('lang', 'add')"/>
											</th>
											<th>
												<xsl:value-of select="php:function('lang', 'edit')"/>
											</th>
											<th>
												<xsl:value-of select="php:function('lang', 'delete')"/>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<input type="checkbox" id="permission_1" name="permission[read]" value="1">
													<xsl:if test="floor(permission/permission div 1) mod 2 = 1">
														<xsl:attribute name="checked" value="checked"/>
													</xsl:if>
													<xsl:attribute name="data-validation">
														<xsl:text>permission</xsl:text>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="checkbox" id="permission_2" name="permission[add]" value="2">
													<xsl:if test="floor(permission/permission div 2) mod 2 = 1">
														<xsl:attribute name="checked" value="checked"/>
													</xsl:if>
													<xsl:attribute name="data-validation">
														<xsl:text>permission</xsl:text>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="checkbox" id="permission_4" name="permission[edit]" value="4">
													<xsl:if test="floor(permission/permission div 4) mod 2 = 1">
														<xsl:attribute name="checked" value="checked"/>
													</xsl:if>
													<xsl:attribute name="data-validation">
														<xsl:text>permission</xsl:text>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="checkbox" id="permission_8" name="permission[delete]" value="8">
													<xsl:if test="floor(permission/permission div 8) mod 2 = 1">
														<xsl:attribute name="checked" value="checked"/>
													</xsl:if>
													<xsl:attribute name="data-validation">
														<xsl:text>permission</xsl:text>
													</xsl:attribute>
												</input>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!--object_id, permission-->

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
