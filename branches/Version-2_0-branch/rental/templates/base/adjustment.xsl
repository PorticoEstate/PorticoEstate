  
<!-- $Id: adjustment.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
		<xsl:when test="show_affected_contracts">
			<xsl:apply-templates select="show_affected_contracts" />
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="show_affected_contracts">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="details">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
						</label>
						<xsl:value-of select="value_field_of_responsibility"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'adjustment_type')"/>
						</label>
						<xsl:value-of select="value_adjustment_type"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'percent')"/>
						</label>
						<xsl:value-of select="value_percent"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'interval')"/>
						</label>
						<xsl:value-of select="value_interval"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'year')"/>
						</label>
						<xsl:value-of select="value_year"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'adjustment_date')"/>
						</label>
						<xsl:value-of select="value_adjustment_date"/>
					</div>
					<xsl:choose>
						<xsl:when test="is_extra_adjustment">				
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'extra_adjustment')"/>
								</label>
								<input type="checkbox" name="extra_adjustment" id="extra_adjustment" disabled="disabled">
									<xsl:if test="is_extra_adjustment = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
						</xsl:when>
					</xsl:choose>
					<div class="pure-control-group">
						<label></label>
						<xsl:value-of select="msg_executed"/>
					</div>
					<div>
						<xsl:if test="show_affected_list = 1">
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
						</xsl:if>
						<xsl:if test="show_affected_list = 0">
							<h2>
								<xsl:value-of select="php:function('lang', 'adjustment_list_out_of_date')"/>
							</h2>
						</xsl:if>
					</div>
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


<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="regulation">
					<fieldset>
						<input type="hidden" name="id" value="{adjustment_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'field_of_responsibility')"/>
							</label>
							<xsl:choose>
								<xsl:when test="adjustment_id = 0 or adjustment_id = ''">
									<input type="hidden" name="responsibility_id" id="responsibility_id" value="{responsibility_id}"/>
								</xsl:when>
							</xsl:choose>
							<xsl:value-of select="value_field_of_responsibility"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'adjustment_type')"/>
							</label>
							<select id="adjustment_type" name="adjustment_type">
								<xsl:apply-templates select="list_adjustment_type/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'percent')"/>
							</label>
							<input type="text" id="percent" name="percent" size="10" value="{value_percent}"/> %
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'interval')"/>
							</label>
							<select id="interval" name="interval">
								<xsl:apply-templates select="list_interval/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'year')"/>
							</label>
							<select id="adjustment_year" name="adjustment_year">
								<xsl:apply-templates select="list_years/options"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'adjustment_date')"/>
							</label>
							<input type="text" id="adjustment_date" name="adjustment_date" size="10" value="{value_adjustment_date}" readonly="readonly"/>
						</div>
						<xsl:choose>
							<xsl:when test="is_extra_adjustment">				
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'extra_adjustment')"/>
									</label>
									<input type="checkbox" name="extra_adjustment" id="extra_adjustment">
										<xsl:if test="is_extra_adjustment = 1">
											<xsl:attribute name="checked" value="checked"/>
										</xsl:if>
									</input>
								</div>
							</xsl:when>
						</xsl:choose>
						<div class="pure-control-group">
							<label></label>
							<xsl:value-of select="msg_executed"/>
						</div>
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