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
					<xsl:value-of select="php:function('lang', 'budget account user')"/>
				</h2>
			</div>
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<xsl:call-template name="msgbox"/>
				</xsl:when>
			</xsl:choose>
			<div class="body">
				<div id="voucher_details">
					<xsl:apply-templates select="filter_form" />
					<form action="{update_action}" name="acl_form" id="acl_form" method="post">
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
	<table class="pure-table">
		<thead>
			<tr>
				<th>
					<xsl:value-of select="php:function('lang', 'user')" />
				</th>
				<th>
					<xsl:value-of select="php:function('lang', 'budget account')" />
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<select id="user_id" name="user_id">
						<xsl:apply-templates select="user_list/options"/>
					</select>
				</td>
				<td>
					<select id="b_account_id" name="b_account_id">
						<xsl:apply-templates select="b_account_list/options"/>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
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