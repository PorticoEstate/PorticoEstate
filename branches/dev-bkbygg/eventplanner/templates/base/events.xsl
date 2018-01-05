
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
		<xsl:text> H:i</xsl:text>
	</xsl:variable>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<xsl:variable name="mode">
		<xsl:value-of select="mode"/>
	</xsl:variable>

	<div class="content">
		<script type="text/javascript">
			var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required')"/>;
		</script>
		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
				<div id="first_tab">
					<xsl:if test="booking/id > 0">
						<div class="pure-control-group">
							<label>
								<a href="{booking_url}">
									<xsl:value-of select="php:function('lang', 'booking')"/>
								</a>
							</label>
							<input type="hidden" name="booking_id" value="{booking/id}"/>
							<xsl:value-of select="booking/id"/>
						</div>
					</xsl:if>
					<xsl:if test="vendor_report/id > 0">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'report')"/>
							</label>
							<input type="hidden" name="id" value="{vendor_report/id}"/>
							<xsl:value-of select="vendor_report/id"/>
						</div>
					</xsl:if>
					<xsl:call-template name="application_info">
						<xsl:with-param name="application" select ='application'/>
						<xsl:with-param name="application_type_list" select ='application_type_list'/>
					</xsl:call-template>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'category')"/>
						</label>
						<xsl:value-of select="category_name"/>
					</div>

					<xsl:if test="booking_interval != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'booking interval')"/>
							</label>
							<xsl:value-of select="booking_interval"/>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'days')"/>
						</div>
					</xsl:if>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'event dates')"/>
						</label>
						<div class="pure-custom">
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
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

				</div>
			</div>
			<div class="proplist-col">
				<!--input type="submit" class="pure-button pure-button-primary" name="save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:attribute>
				</input-->
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
