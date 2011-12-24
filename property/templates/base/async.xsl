<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="list">

		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
					<!--	<xsl:with-param name="nextmatchs_params"/>
					</xsl:call-template> -->
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header"></xsl:apply-templates>
			<xsl:apply-templates select="values"></xsl:apply-templates>
			<xsl:apply-templates select="table_add"></xsl:apply-templates>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_id"><xsl:value-of select="sort_id"></xsl:value-of></xsl:variable>
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="right">
				<a href="{$sort_id}"><xsl:value-of select="lang_id"></xsl:value-of></a>
			</td>
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_data"></xsl:value-of>
			</td>
			<td class="th_text" width="20%" align="center">
				<xsl:value-of select="lang_descr"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_run"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_schedule"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_run_statustext"><xsl:value-of select="lang_run_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_schedule_statustext"><xsl:value-of select="lang_schedule_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
		<tr>
			<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="@class">
						<xsl:value-of select="@class"></xsl:value-of>
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
				<xsl:value-of select="id"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="name"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="data"></xsl:value-of>
			</td>
			<td>
				<xsl:value-of select="first"></xsl:value-of>
			</td>
			<td align="center">
				<xsl:variable name="link_run"><xsl:value-of select="link_run"></xsl:value-of></xsl:variable>
				<a href="{$link_run}" onMouseover="window.status='{$lang_run_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_run"></xsl:value-of></a>
			</td>
			<td align="center">
				<xsl:variable name="link_schedule"><xsl:value-of select="link_schedule"></xsl:value-of></xsl:variable>
				<a href="{$link_schedule}" onMouseover="window.status='{$lang_schedule_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_schedule"></xsl:value-of></a>
			</td>
			<td align="center">
				<xsl:variable name="link_edit"><xsl:value-of select="link_edit"></xsl:value-of></xsl:variable>
				<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"></xsl:value-of></a>
			</td>
			<td align="center">
				<xsl:variable name="link_delete"><xsl:value-of select="link_delete"></xsl:value-of></xsl:variable>
				<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"></xsl:value-of></a>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="table_add">
		<tr>
			<td height="50">
				<xsl:variable name="add_action"><xsl:value-of select="add_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_add"><xsl:value-of select="lang_add"></xsl:value-of></xsl:variable>
				<form method="post" action="{$add_action}">
					<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
			<td height="50">
				<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
				<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
				<form method="post" action="{$done_action}">
					<input type="submit" name="add" value="{$lang_done}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"></xsl:value-of>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</form>
			</td>
		</tr>
	</xsl:template>

<!-- add / edit  -->
	<xsl:template match="edit">
		<div align="left">

			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:variable name="form_action"><xsl:value-of select="form_action"></xsl:value-of></xsl:variable>
				<form method="post" action="{$form_action}">
					<tr>
						<td valign="top">
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<xsl:value-of select="lang_id"></xsl:value-of>
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>
						</td>
						<td>
							<xsl:choose>
								<xsl:when test="value_id != ''">
									<xsl:value-of select="value_id"></xsl:value-of>
								</xsl:when>
								<xsl:otherwise>
								</xsl:otherwise>
							</xsl:choose>	
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_name"></xsl:value-of>
						</td>
						<td>
							<input type="text" size="40" name="values[name]" value="{value_name}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_name_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_data"></xsl:value-of>
						</td>
						<td>
							<input type="text" size="60" name="values[data]" value="{value_data}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_data_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_descr"></xsl:value-of>
						</td>
						<td>
							<textarea cols="60" rows="10" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"></xsl:value-of>		
							</textarea>

						</td>
					</tr>
					<tr height="50">
						<td>
							<xsl:variable name="lang_save"><xsl:value-of select="lang_save"></xsl:value-of></xsl:variable>
							<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_save_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
		</div>
	</xsl:template>
