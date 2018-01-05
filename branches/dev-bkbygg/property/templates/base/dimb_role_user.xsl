<!-- $Id$ -->

<!-- separate tabs and  inline tables-->


<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">
		#box { width: 200px; height: 5px; background: blue; }
		select { width: 200px; }
		.row_on,.th_bright
		{
		background-color: #CCEEFF;
		}

		.row_off
		{
		background-color: #DDF0FF;
		}

	</style>

	<xsl:call-template name="table" />
	<div id="popupBox"></div>	
	<div id="curtain"></div>
</xsl:template>

<xsl:template name="table" xmlns:php="http://php.net/xsl">
	<div class="body">
		<div id="invoice-layout">
			<div class="header">
				<h2>
					<xsl:value-of select="php:function('lang', 'role')"/>
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
							<xsl:call-template name="role_fields" />
							<tr>
								<td colspan = '6'>
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
		<td colspan = '6'>
			<table>
				<tr>
					<td>
						<xsl:value-of select="php:function('lang', 'dim b')" />
					</td>
					<td>
						<xsl:value-of select="php:function('lang', 'role')" />
					</td>
					<td>
						<xsl:value-of select="php:function('lang', 'user')" />
					</td>
					<td colspan = "2" align = "center">
						<xsl:value-of select="php:function('lang', 'search')" />
						<xsl:text> </xsl:text>
						<xsl:value-of select="php:function('lang', 'date')" />
					</td>
				</tr>
				<tr id="filters">
					<td>
						<select id="dimb_id" name="dimb">
							<xsl:apply-templates select="dimb_list/options"/>
						</select>
					</td>
					<td>
						<select id="role_id" name="role_id">
							<xsl:apply-templates select="role_list/options"/>
						</select>
					</td>
					<td>
						<select id="user_id" name="user_id">
							<xsl:apply-templates select="user_list/options"/>
						</select>
					</td>
					<td>
						<input type="text" name="query_start" id="query_start" size = "10"/>
					</td>
					<td>
						<input type="text" name="query_end" id="query_end" size = "10"/>
					</td>
					<td>
						<xsl:variable name="lang_search">
							<xsl:value-of select="php:function('lang', 'Search')" />
						</xsl:variable>
						<input type="button" id = "search" name="search" value="{$lang_search}" title = "{$lang_search}" />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</xsl:template>


<xsl:template name="role_fields" xmlns:php="http://php.net/xsl">
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'date from')" />
		</td>
		<td>
			<input type="text" name="values[active_from]" id="active_from" value=""/>
		</td>
	</tr>
	<tr class ='row_off'>
		<td>
			<xsl:value-of select="php:function('lang', 'date to')" />
		</td>
		<td>
			<input type="text" name="values[active_to]" id="active_to" value=""/>
		</td>
	</tr>
</xsl:template>


<!-- options for use with select-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

