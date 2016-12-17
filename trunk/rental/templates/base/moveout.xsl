
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
						<legend>
							<xsl:value-of select="php:function('lang', 'basis data')"/>
						</legend>
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
								<xsl:value-of select="$lang_contract"/>
							</label>
							<input type="hidden" id="contract_id" name="contract_id"  value="{contract/id}">
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
							<input type="text" id="contract_name" name="contract_name" value="{contract/old_contract_id}">
								<xsl:if test="contract/id > 0">
									<xsl:attribute name="readonly">
										<xsl:text>readonly</xsl:text>
									</xsl:attribute>
								</xsl:if>
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
					</fieldset>

					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'report')"/>
						</legend>

						<xsl:call-template name="attributes_values"/>

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
