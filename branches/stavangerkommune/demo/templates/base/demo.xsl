<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"/>
			</xsl:when>
			<xsl:when test="edit">
				<xsl:apply-templates select="view"/>
			</xsl:when>
			<xsl:when test="list_wml">
				<xsl:apply-templates select="list_wml"/>
			</xsl:when>
			<xsl:when test="list_html">
				<xsl:apply-templates select="list_html"/>
			</xsl:when>
			<xsl:when test="list2_wml">
				<xsl:apply-templates select="list_wml"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list2_html"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="list_html">
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
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="center">
					<xsl:call-template name="filter_select"/>
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
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:apply-templates select="values"/>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_add"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_name"><xsl:value-of select="sort_name"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_name}"><xsl:value-of select="lang_name"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_view_place_text"><xsl:value-of select="lang_view_place_text"/></xsl:variable>
		<xsl:variable name="lang_edit_place_text"><xsl:value-of select="lang_edit_place_text"/></xsl:variable>
		<xsl:variable name="lang_delete_place_text"><xsl:value-of select="lang_delete_place_text"/></xsl:variable>

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
				<td align="left">
					<xsl:value-of select="name"/>
				</td>
				<td align="center">
					<xsl:variable name="link_view"><xsl:value-of select="link_view"/></xsl:variable>
					<a href="{$link_view}" onMouseover="window.status='{$lang_view_place_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"/></a>
				</td>
				<xsl:choose>
					<xsl:when test="link_edit != ''">
						<td align="center">
							<xsl:variable name="link_edit"><xsl:value-of select="link_edit"/></xsl:variable>
							<a href="{$link_edit}" onMouseover="window.status='{$lang_edit_place_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_edit"/></a>
						</td>
					</xsl:when>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="link_edit != ''">
						<td align="center">
							<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
							<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_place_text}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
						</td>
					</xsl:when>
				</xsl:choose>
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

	<xsl:template match="list_wml">
		<wml>
		<card id = "card1" title = "list demo">
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
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="center">
					<xsl:call-template name="filter_select"/>
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
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:apply-templates select="values"/>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_add"/>
		</table>
		</card>
		</wml>
	</xsl:template>

	<xsl:template match="list2_html">
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
				<td>
					<xsl:call-template name="categories"/>
				</td>
				<td align="center">
					<xsl:call-template name="filter_select"/>
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
			<xsl:call-template name="table_header2"/>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:call-template name="values2"/>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_add != ''">
					<xsl:apply-templates select="table_add"/>
				</xsl:when>
			</xsl:choose>
		</table>
	</xsl:template>


	<xsl:template name="table_header2">
			<tr class="th">
				<xsl:for-each select="table_header" >
					<td class="th_text" width="{with}" align="{align}">
						<xsl:choose>
							<xsl:when test="sort_link!=''">
								<a href="{sort}" onMouseover="window.status='{header}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="header"/></a>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="header"/>					
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</xsl:for-each>
			</tr>
	</xsl:template>

	<xsl:template name="values2">
		<xsl:for-each select="values" >
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
				<xsl:for-each select="row" >
					<xsl:choose>
						<xsl:when test="link">
							<td class="small_text" align="center">
								<a href="{link}" onMouseover="window.status='{statustext}';return true;" onMouseout="window.status='';return true;" target = "{target}"><xsl:value-of select="text"/></a>
							</td>
						</xsl:when>
						<xsl:otherwise>
							<td class="small_text" align="left">
								<xsl:value-of select="value"/>					
							</td>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
	</xsl:template>



<!-- add / edit  -->
	<xsl:template match="edit" xmlns:php="http://php.net/xsl">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
		<table cellpadding="2" cellspacing="2" width="90%" align="center">
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
				<xsl:when test="value_id != ''">
				<tr>
				<td valign="top" width="40%">
						<xsl:value-of select="php:function('lang', 'id')" />
					</td>
					<td align="left">
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="php:function('lang', 'entry_date')" />
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
				</xsl:when>
			</xsl:choose>	
			<tr>
				<td>
					<xsl:value-of select="php:function('lang', 'category')" />
				</td>
				<td>
					<xsl:call-template name="categories"/>
				</td>
			</tr>
			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="php:function('lang', 'name')" />
				</td>
				<td>
					<input type="text" size="60" name="values[name]" value="{value_name}">
						<xsl:attribute name="title">
								<xsl:value-of select="lang_name_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'address')" />
				</td>
				<td>
					<input type="text" size="60" name="values[address]" value="{value_address}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_address_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'zip')" />
				</td>
				<td>
					<input type="text" size="6" name="values[zip]" value="{value_zip}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_zip_status_text"/>
						</xsl:attribute>
					</input>
					<xsl:value-of select="lang_town"/>
					<input type="text" size="40" name="values[town]" value="{value_town}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_town_status_text"/>
						</xsl:attribute>
					</input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'remark')" />
				</td>
				<td>
					<textarea cols="60" rows="10" name="values[remark]" id="remark" wrap="virtual">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_remark_status_text"/>
						</xsl:attribute>
						<xsl:value-of select="value_remark"/>		
					</textarea>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="attributes_values != ''">
					<tr>
						<td colspan="2" align="left">				
							<xsl:call-template name="attributes_form"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
					<xsl:value-of select="php:function('lang', 'private')" />
				</td>
				<td>
					<xsl:choose>
							<xsl:when test="value_access = 'private'">
								<input type="checkbox" name="values[access]" value="True" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'The note is private. If the note should be public, uncheck this box')" />
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[access]" value="True">
									<xsl:attribute name="title">
										<xsl:value-of select="php:function('lang', 'The note is public. If the note should be private, check this box')" />
									</xsl:attribute>
								</input>
							</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'generic list 1')" />
				</td>
				<td>
					<select name="values[generic_list_1]" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Set a value')" />
						</xsl:attribute>
						<option value="0">
							<xsl:value-of select="php:function('lang', 'Set a value')" />
						</option>
						<xsl:apply-templates select="generic_list_1/options"/>
					</select>			
				</td>
			</tr>

			<tr>
				<td valign="top">
					<xsl:value-of select="php:function('lang', 'generic list 2')" />
				</td>
				<td>
					<select name="values[generic_list_2]" >
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Set a value')" />
						</xsl:attribute>
						<option value="0">
							<xsl:value-of select="php:function('lang', 'Set a value')" />
						</option>
						<xsl:apply-templates select="generic_list_2/options"/>
					</select>			
				</td>
			</tr>


			<tr height="50">
				<td colspan = "2" align = "center"><table><tr>
				<td valign="bottom">
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save')" />
						</xsl:attribute>
					</input>
				</td>
				<td valign="bottom">
					<xsl:variable name="lang_apply"><xsl:value-of select="php:function('lang', 'apply')" /></xsl:variable>
					<input type="submit" name="values[apply]" value="{$lang_apply}">
						<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'apply the values')" />
						</xsl:attribute>
					</input>
				</td>
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'Back to the list')" />
						</xsl:attribute>
					</input>
				</td>
				</tr></table></td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>


<!-- view  -->
	<xsl:template match="view">
		<div align="left">
		<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
		<form method="post" action="{$form_action}">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_id"/>
					</td>
					<td>
						<xsl:value-of select="value_id"/>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_entry_date"/>
					</td>
					<td>
						<xsl:value-of select="value_entry_date"/>
					</td>
				</tr>
			<tr>
				<td>
					<xsl:value-of select="lang_category"/>
				</td>
				<td>
					<xsl:value-of select="value_cat"/>
				</td>
			</tr>

			<tr>
				<td valign="top" width="10%">
					<xsl:value-of select="lang_name"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" value="{value_name}"> </input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_address"/>
				</td>
				<td>
					<input type="text" readonly="true" size="60" value="{value_address}"></input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_zip"/>
				</td>
				<td>
					<input type="text" readonly="true" size="6" value="{value_zip}"></input>
					<xsl:value-of select="lang_town"/>
					<input type="text" readonly="true" size="40" value="{value_town}"></input>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_remark"/>
				</td>
				<td>
					<textarea cols="60" readonly="true" rows="10">
						<xsl:value-of select="value_remark"/>		
					</textarea>
				</td>
			</tr>
			<xsl:choose>
				<xsl:when test="attributes_values != ''">
					<tr>
						<td colspan="2" align="left">				
							<xsl:call-template name="attributes_view"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td>
					<xsl:value-of select="lang_access"/>
				</td>
				<td>
					<xsl:value-of select="value_access"/>
				</td>
			</tr>

		</table>
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<tr height="50">
				<td align="left" valign="bottom">
					<xsl:variable name="lang_cancel"><xsl:value-of select="lang_cancel"/></xsl:variable>
					<input type="submit" name="values[cancel]" value="{$lang_cancel}">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_cancel_status_text"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
		</form>
		</div>
	</xsl:template>
	
	
	<xsl:template match="options">
		<option value="{id}">
			<xsl:if test="selected != 0">
				<xsl:attribute name="selected" value="selected" />
			</xsl:if>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</option>
	</xsl:template>

