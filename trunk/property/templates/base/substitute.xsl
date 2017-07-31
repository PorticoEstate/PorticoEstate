
<!-- $Id: tts.xsl 16389 2017-02-28 17:35:22Z sigurdne $ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="view">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="table" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<style type="text/css">
		#box { width: 200px; height: 5px; background: blue; }
		select { width: 200px; }
	</style>
	<xsl:call-template name="table_substitute" />
	<div id="popupBox"></div>
	<div id="curtain"></div>
</xsl:template>

<xsl:template name="table_substitute" xmlns:php="http://php.net/xsl">
	<div class="body">
		<div id="invoice-layout">
			<div class="header">
				<h2>
					<xsl:value-of select="php:function('lang', 'substitute')"/>
				</h2>
			</div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
			<div class="body">
				<div id="voucher_details">
					<table align = "center" width="95%">
						<xsl:apply-templates select="filter_form" />
					</table>
					<form action="{update_action}" name="acl_form" id="acl_form" method="post">
						<table align = "center" width="95%">
							<tr>
								<td colspan = '2'>
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

								</td>
							</tr>
						</table>
						<div id="receipt"></div>
						<xsl:variable name="label_submit">
							<xsl:value-of select="php:function('lang', 'save')" />
						</xsl:variable>
						<div class="row_on">
							<input type="submit" name="update_acl" id="frm_update_acl" value="{$label_submit}"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="filter_form" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="filter_list"/>
</xsl:template>

<xsl:template name="filter_list" xmlns:php="http://php.net/xsl">
	<tr>
		<td colspan = '1'>
			<table>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'user')" />
					</td>
					<td>
						<xsl:value-of select="php:function('lang', 'substitute')" />
					</td>
				</tr>
				<tr id="filters">
					<td>
						<select id="user_id" name="user_id">
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</td>
					<td>
						<select id="substitute_user_id" name="substitute_user_id">
							<xsl:apply-templates select="substitute_list/options"/>
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</xsl:template>



<!-- edit -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<fieldset>
					<div class="pure-control-group">
						<xsl:variable name="lang_substitute">
							<xsl:value-of select="php:function('lang', 'substitute')"/>
						</xsl:variable>
						<label>
							<xsl:value-of select="$lang_substitute"/>
						</label>
						<select name="substitute_user_id" id="substitute_user_id">
							<xsl:attribute name="title">
								<xsl:value-of select="$lang_substitute"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</div>
				</fieldset>
			</div>
		</div>
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
	</form>
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
