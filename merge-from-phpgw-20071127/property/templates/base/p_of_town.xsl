
	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">
		<xsl:call-template name="menu"/>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td align="left">
					<xsl:call-template name="filter_district"/>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
				<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_time_created" select="sort_time_created"/>
		<xsl:variable name="sort_part_of_town_id" select="sort_part_of_town_id"/>
		<xsl:variable name="sort_name" select="sort_name"/>
		<xsl:variable name="sort_category" select="sort_category"/>
		
			<tr class="th">
				<td width="10%" align="right">
					<a href="{$sort_part_of_town_id}" class="th_text"><xsl:value-of select="lang_part_of_town_id"/></a>
				</td>
				<td width="40%">
					<a href="{$sort_name}" class="th_text"><xsl:value-of select="lang_name"/></a>
				</td>
				<td width="10%" align="center">
					<a href="{$sort_category}" class="th_text"><xsl:value-of select="lang_category"/></a>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_view"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_edit"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"/></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"/></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>

				<td align="right">
					<xsl:value-of select="part_of_town_id"/>
				</td>
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="left">
					<xsl:value-of select="category"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
					<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="table_add">
			<tr>
				<td height="50">
					<xsl:variable name="add_action"><xsl:value-of select="add_action"/></xsl:variable>
					<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
					<form method="post" action="{$add_action}">
						<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
							<xsl:attribute name="onMouseover">
								<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_add_statustext"/>
								<xsl:text>'; return true;</xsl:text>
							</xsl:attribute>
						</input>
					</form>
				</td>
			</tr>
	</xsl:template>

<!-- add / edit -->

	<xsl:template match="edit">
		<xsl:variable name="edit_url"><xsl:value-of select="edit_url"/></xsl:variable>
		<div align="left">
		<form name="form" method="post" action="{$edit_url}">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="value_part_of_town_id!=''">
					<tr >
						<td width="25%" align="left">
							<xsl:value-of select="lang_part_of_town_id"/>
						</td>
						<td width="75%" align="left">
							<xsl:value-of select="value_part_of_town_id"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>

			<tr >
				<td align="left">
					<xsl:value-of select="lang_district"/>
				</td>
				<td align="left">
					<xsl:call-template name="select_district"/>
				</td>
			</tr>
			<tr  align="left">
				<td valign="top" >
					<xsl:value-of select="lang_name"/>
				</td>
				<td align="left">
					<input type="text" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_name_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>

				</td>
			</tr>
			<tr height="50">
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="lang_apply"/></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_apply_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
				<td align="right" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>

<!-- view -->

	<xsl:template match="view">
		<table cellpadding="2" cellspacing="2" width="79%" align="center">
			<tr class="row_off">
				<td width="19%">
					<xsl:value-of select="lang_id"/>
				</td>
				<td width="81%">
					<xsl:value-of select="value_id"/>
				</td>
			</tr>
			<tr class="row_on">
				<td>
					<xsl:value-of select="lang_district"/>
				</td>
				<td>
					<xsl:value-of select="value_district"/>
				</td>
			</tr>
			<tr class="row_off">
				<td valign="top">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<xsl:value-of select="value_name"/>
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="done_action"><xsl:value-of select="done_action"/></xsl:variable>
					<xsl:variable name="lang_done"><xsl:value-of select="lang_done"/></xsl:variable>
					<form method="post" action="{$done_action}">
					<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseover="window.status='Back to the list.';return true;" onMouseout="window.status='';return true;"/>
					</form>
				</td>
			</tr>
		</table>
	</xsl:template>
