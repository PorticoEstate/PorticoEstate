
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		var base_java_url = <xsl:value-of select="base_java_url"/>;
		var lang = <xsl:value-of select="php:function('js_lang',  'Name', 'Address')"/>
	</script>

	<div class="content">
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
				<input type="hidden" name='validatet_category' id="validatet_category" value="{validatet_category}"/>
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>

					<div id="main">

						<xsl:if test="values/id !=''">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<xsl:value-of select="values/id"/>
							</div>

						</xsl:if>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'name')"/>
							</label>
							<input class="pure-input-3-4" type="text" id="name" name="name" value="{values/name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'external project')"/>
							</label>
							<input type="hidden" id="external_project_id" name="external_project_id"  value="{values/external_project_id}"/>
							<input class="pure-input-3-4" type="text" id="external_project_name" name="external_project_name" value="{value_external_project_name}"/>
							<div id="external_project_container"/>
						</div>

						<xsl:call-template name="vendor_form">
							<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
						</xsl:call-template>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'contract')"/>
							</label>
							<select id="vendor_contract_id" name="contract_id" class="pure-input-3-4">
								<xsl:if test="count(contract_list/options) &gt; 0">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
								</xsl:if>
								<option value="">
									<xsl:value-of select="php:function('lang', 'select')"/>
								</option>
								<xsl:apply-templates select="contract_list/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'email')"/>
							</label>
							<div class="pure-u-md-3-4" >
								<xsl:for-each select="datatable_def">
									<xsl:if test="container = 'datatable-container_1'">
										<xsl:call-template name="table_setup">
											<xsl:with-param name="container" select ='container'/>
											<xsl:with-param name="requestUrl" select ='requestUrl'/>
											<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
											<xsl:with-param name="data" select ='data'/>
											<xsl:with-param name="tabletools" select ='tabletools' />
											<xsl:with-param name="config" select ='config'/>
										</xsl:call-template>
									</xsl:if>
								</xsl:for-each>
							</div>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'extra mail address')"/>
							</label>

							<input type="text" name="mail_recipients[]" value="{value_extra_mail_address}" class="pure-input-3-4" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'comma separated list')"/>
								</xsl:attribute>
							</input>
						</div>

						<!--<xsl:if test="enable_order_service_id = 1">-->
						<div class="pure-control-group">
							<xsl:variable name="lang_service">
								<xsl:value-of select="php:function('lang', 'service')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_service"/>
							</label>
							<input type="hidden" id="service_id" name="service_id"  value="{values/service_id}"/>
							<input class="pure-input-3-4" type="text" id="service_name" name="service_name" value="{value_service_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_service"/>
								</xsl:attribute>
							</input>

							<div id="service_container"/>
						</div>
						<!--</xsl:if>-->
						<div class="pure-control-group">
							<xsl:variable name="lang_dimb">
								<xsl:value-of select="php:function('lang', 'dimb')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_dimb"/>
							</label>
							<input type="hidden" id="ecodimb" name="ecodimb"  value="{ecodimb_data/value_ecodimb}"/>
							<input class="pure-input-3-4" type="text" id="ecodimb_name" name="ecodimb_name" value="{ecodimb_data/value_ecodimb} {ecodimb_data/value_ecodimb_descr}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_dimb"/>
								</xsl:attribute>
							</input>
							<div id="ecodimb_container"/>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_budget_account">
								<xsl:value-of select="php:function('lang', 'budget account')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_budget_account"/>
							</label>
							<input type="hidden" id="b_account_id" name="b_account_id"  value="{b_account_data/value_b_account_id}"/>
							<input class="pure-input-3-4" type="text" id="b_account_name" name="b_account_name" value="{b_account_data/value_b_account_id} {b_account_data/value_b_account_name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="$lang_budget_account"/>
								</xsl:attribute>
							</input>
							<div id="b_account_container"/>
						</div>
						<xsl:if test="enable_unspsc = 1">
							<div class="pure-control-group">
								<xsl:variable name="lang_unspsc_code">
									<xsl:value-of select="php:function('lang', 'unspsc code')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_unspsc_code"/>
								</label>
								<input type="hidden" id="unspsc_code" name="unspsc_code"  value="{values/unspsc_code}"/>
								<input class="pure-input-3-4" type="text" id="unspsc_code_name" name="unspsc_code_name" value="{values/unspsc_code} {value_unspsc_code_name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_unspsc_code"/>
									</xsl:attribute>
								</input>
								<div id="unspsc_code_container"/>
							</div>
						</xsl:if>

						<xsl:choose>
							<xsl:when test="collect_building_part=1">
								<div class="pure-control-group">
									<xsl:variable name="lang_building_part">
										<xsl:value-of select="php:function('lang', 'building part')"/>
									</xsl:variable>
									<label>
										<xsl:value-of select="$lang_building_part"/>
									</label>

									<select name="building_part" class="pure-input-3-4" >
										<xsl:attribute name="title">
											<xsl:value-of select="$lang_building_part"/>
										</xsl:attribute>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_building_part"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="$lang_building_part"/>
										</option>
										<xsl:apply-templates select="building_part_list/options"/>
									</select>
								</div>
								<div class="pure-control-group">
									<xsl:variable name="lang_order_dim1">
										<xsl:value-of select="php:function('lang', 'order_dim1')"/>
									</xsl:variable>
									<label>
										<xsl:value-of select="$lang_order_dim1"/>
									</label>
									<select name="order_dim1" class="pure-input-3-4" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'order_dim1')"/>
										</xsl:attribute>
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="$lang_order_dim1"/>
										</xsl:attribute>
										<option value="">
											<xsl:value-of select="php:function('lang', 'order_dim1')"/>
										</option>
										<xsl:apply-templates select="order_dim1_list/options"/>
									</select>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<div class="pure-control-group">
									<label for="name">
										<xsl:value-of select="php:function('lang', 'category')"/>
									</label>
									<xsl:call-template name="cat_sub_select">
										<xsl:with-param name="class">pure-input-3-4</xsl:with-param>
									</xsl:call-template>
								</div>
							</xsl:otherwise>
						</xsl:choose>

						<!--						<xsl:choose>
							<xsl:when test="branch_list!=''">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'branch')"/>
									</label>
									<select name="branch_id" class="pure-input-3-4" >
										<xsl:attribute name="title">
											<xsl:value-of select="php:function('lang', 'select branch')"/>
										</xsl:attribute>
										<option value="0">
											<xsl:value-of select="php:function('lang', 'select branch')"/>
										</option>
										<xsl:apply-templates select="branch_list/options"/>
									</select>
								</div>
							</xsl:when>
						</xsl:choose>-->
						<xsl:if test="enable_unspsc = 1">
							<div class="pure-control-group">
								<xsl:variable name="lang_tax_code">
									<xsl:value-of select="php:function('lang', 'tax code')"/>
								</xsl:variable>
								<label>
									<xsl:value-of select="$lang_tax_code"/>
								</label>
								<select name="tax_code" class="pure-input-3-4" >
									<xsl:attribute name="title">
										<xsl:value-of select="$lang_tax_code"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="$lang_tax_code"/>
									</xsl:attribute>
									<option value="">
										<xsl:value-of select="$lang_tax_code"/>
									</option>
									<xsl:apply-templates select="tax_code_list/options"/>
								</select>
							</div>
						</xsl:if>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'order text')"/>
							</label>

							<textarea class="pure-input-3-4" rows="10" id="order_descr" name="order_descr" wrap="virtual">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'order text')"/>
								</xsl:attribute>
								<xsl:value-of select="values/order_descr"/>
							</textarea>
						</div>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'remark')"/>
							</label>
							<textarea class="pure-input-3-4" rows="10" id="remark" name="remark" wrap="virtual">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'remark')"/>
								</xsl:attribute>
								<xsl:value-of select="values/remark"/>
							</textarea>
						</div>
					</div>
				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:variable>
					<input type="submit" class="pure-button pure-button-primary" name="save">
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
