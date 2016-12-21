
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
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned" enctype="multipart/form-data">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'basis data')"/>
						</legend>
						<xsl:if test="movein/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{movein/id}"/>
								<xsl:value-of select="movein/id"/>
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

						<xsl:call-template name="contract_info">
							<xsl:with-param name="contract" select ='contract'/>
						</xsl:call-template>

					</fieldset>

					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'report')"/>
						</legend>

						<xsl:call-template name="attributes_values"/>

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'picture')"/>
							</label>
							<input type="file" id="file" name="file" accept="image/*" capture="camera"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'files')"/>
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

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'note')"/>
							</label>
							<textarea cols="47" rows="7" name="comment">
								<xsl:value-of select="movein/comment"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'details')"/>
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
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="first_tab">
					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'basis data')"/>
						</legend>
						<xsl:if test="movein/id > 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'id')"/>
								</label>
								<input type="hidden" name="id" value="{movein/id}"/>
								<xsl:value-of select="movein/id"/>
							</div>
						</xsl:if>


						<div class="pure-control-group">
							<xsl:variable name="lang_contract">
								<xsl:value-of select="php:function('lang', 'contract')"/>
							</xsl:variable>
							<label>
								<xsl:value-of select="$lang_contract"/>
							</label>
							<div id="composite" class="pure-custom">
								<xsl:value-of select="contract/old_contract_id"/>
							</div>
						</div>
	
						<xsl:call-template name="contract_info">
							<xsl:with-param name="contract" select ='contract'/>
						</xsl:call-template>

					</fieldset>

					<fieldset>
						<legend>
							<xsl:value-of select="php:function('lang', 'report')"/>
						</legend>

						<xsl:call-template name="attributes_values"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'picture')"/>
							</label>
							<input type="file" id="file" name="file" accept="image/*" capture="camera"></input>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'files')"/>
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

						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'details')"/>
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
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<xsl:variable name="lang_cancel">
					<xsl:value-of select="php:function('lang', 'cancel')"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{$lang_cancel}" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>
