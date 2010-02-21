<!-- $Id: tts.xsl 4859 2010-02-18 23:09:16Z sigurd $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="demo_section_within_template">
				<xsl:apply-templates select="demo_section_within_template"/>
			</xsl:when>
			<xsl:when test="demo_2">
				<xsl:apply-templates select="demo_2"/>
			</xsl:when>
			<xsl:when test="inline_table">
				<xsl:apply-templates select="inline_table"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list">
	</xsl:template>


<!-- 1 -->
	<xsl:template match="demo_section_within_template" xmlns:php="http://php.net/xsl">
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
				<div class="yui-navset" id="ticket_tabview">
					<xsl:value-of disable-output-escaping="yes" select="tabs" />
					<div class="yui-content">
						<div id="general">
						</div>
					</div>
				</div>
			</form>
	</xsl:template>
<!-- 2 -->
	<xsl:template match="demo_2" xmlns:php="http://php.net/xsl">
		<table cellpadding="2" cellspacing="2" width="95%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
		</table>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form ENCTYPE="multipart/form-data" name="form" method="post" action="{$form_action}">
				<div class="yui-navset" id="ticket_tabview">
					<xsl:value-of disable-output-escaping="yes" select="tabs" />
					<div class="yui-content">
						<div id='date_test'>
								<li>
									<xsl:value-of select="php:function('lang', 'start date')" />
									<xsl:value-of disable-output-escaping="yes" select="date_start"/>
								</li>
								<li>
									<xsl:value-of select="php:function('lang', 'end date')" />
									<xsl:value-of  disable-output-escaping="yes" select="date_end"/>
								</li>
						</div>
					</div>
				</div>
			</form>
	</xsl:template>
	
	
<!--Inline table-->
	<xsl:template match="inline_table" xmlns:php="http://php.net/xsl">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<table cellpadding="2" cellspacing="2" width="95%" align="center">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</table>
			</xsl:when>
		</xsl:choose>

		<div class="yui-navset" id="ticket_tabview">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div class="yui-content">
				<div id="paging_0"></div><div id="datatable-container_0"></div>
			</div>
		</div>

		<!--  DATATABLE DEFINITIONS-->
		<script>
			var property_js = <xsl:value-of select="property_js" />;
			var base_java_url = <xsl:value-of select="base_java_url" />;
			var datatable = new Array();
			var myColumnDefs = new Array();
			var myButtons = new Array();
		    var td_count = <xsl:value-of select="td_count" />;

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
				{
					values			:	<xsl:value-of select="values"/>,
					total_records	: 	<xsl:value-of select="total_records"/>,
					is_paginator	:  	<xsl:value-of select="is_paginator"/>,
			<!--		permission		:	<xsl:value-of select="permission"/>, -->
					footer			:	<xsl:value-of select="footer"/>
				}
				]
			</xsl:for-each>
			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
			<xsl:for-each select="myButtons">
				myButtons[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
	</xsl:template>

