<!-- $Id$ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="edit">
				<xsl:apply-templates select="edit"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="view">
				<xsl:apply-templates select="view"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list_document">
				<xsl:apply-templates select="list_document"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template match="list">
		<xsl:apply-templates select="menu"></xsl:apply-templates> 
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<tr>
				<td align="left">
					<xsl:call-template name="doc_type_filter"></xsl:call-template> 
				</td>
				<xsl:choose>
					<xsl:when test="cat_list!=''">
						<td align="left">
							<xsl:call-template name="cat_filter2"></xsl:call-template> 
						</td>
					</xsl:when>
				</xsl:choose>
				<td align="left">
					<xsl:call-template name="user_id_filter"></xsl:call-template>
				</td>
				<td align="right">
					<xsl:call-template name="search_field"></xsl:call-template>
				</td>
			</tr>
			<tr>
				<td colspan="4" width="100%">
					<xsl:call-template name="nextmatchs"></xsl:call-template>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:call-template name="table_header"></xsl:call-template>
			<xsl:choose>
				<xsl:when test="values">
					<xsl:call-template name="values"></xsl:call-template>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="table_add !=''">
					<xsl:apply-templates select="table_add"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>	
		</table>
	</xsl:template>


	<xsl:template name="doc_type_filter">
		<xsl:variable name="select_name"><xsl:value-of select="select_name"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
		<form method="post" action="{select_action}">
			<select name="doc_type" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_doc_type_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_doc_type"></xsl:value-of></option>
				<xsl:apply-templates select="doc_type"></xsl:apply-templates>
			</select> 
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"></input>
			</noscript>
		</form>
	</xsl:template>


	<xsl:template match="doc_type">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose> 
	</xsl:template>

	<xsl:template name="cat_filter2">
		<xsl:variable name="select_name"><xsl:value-of select="select_name"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_submit"><xsl:value-of select="lang_submit"></xsl:value-of></xsl:variable>
		<form method="post" action="{select_action}">
			<select name="{$select_name}" onChange="this.form.submit();" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_cat_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
				<option value=""><xsl:value-of select="lang_no_cat"></xsl:value-of></option>
				<xsl:apply-templates select="cat_list"></xsl:apply-templates>
			</select> 
			<noscript>
				<xsl:text> </xsl:text>
				<input type="submit" name="submit" value="{$lang_submit}"></input>
			</noscript>
		</form>
	</xsl:template>


	<xsl:template match="cat_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose> 
	</xsl:template>


	<xsl:template match="list_document">
		<xsl:apply-templates select="menu"></xsl:apply-templates>
		<div align="left">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:choose>
					<xsl:when test="msgbox_data != ''">
						<tr>
							<td align="left" colspan="3">
								<xsl:call-template name="msgbox"></xsl:call-template>
							</td>
						</tr>
					</xsl:when>
				</xsl:choose>
				<xsl:call-template name="location_view"></xsl:call-template>
				<tr>
					<td>
						<xsl:call-template name="categories"></xsl:call-template>
					</td>
					<td align="center">
						<xsl:call-template name="user_id_filter"></xsl:call-template>
					</td>
					<td align="right">
						<xsl:call-template name="search_field"></xsl:call-template>
					</td>
				</tr>
				<tr>
					<td colspan="4" width="100%">
						<xsl:call-template name="nextmatchs"></xsl:call-template>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header_document"></xsl:apply-templates>
				<xsl:apply-templates select="values_document"></xsl:apply-templates>
				<xsl:apply-templates select="table_add"></xsl:apply-templates>
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

	<xsl:template match="table_header_document">
		<xsl:variable name="sort_document_name"><xsl:value-of select="sort_document_name"></xsl:value-of></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_document_name}"><xsl:value-of select="lang_document_name"></xsl:value-of></a>
			</td>
			<td class="th_text" width="60%" align="left">
				<xsl:value-of select="lang_title"></xsl:value-of>
			</td>
			<td class="th_text" width="2%" align="left">
				<xsl:value-of select="lang_doc_type"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="left">
				<xsl:value-of select="lang_user"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_view"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_edit"></xsl:value-of>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_delete"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values_document">
		<xsl:variable name="lang_view_file_statustext"><xsl:value-of select="lang_view_file_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_view_statustext"><xsl:value-of select="lang_view_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_edit_statustext"><xsl:value-of select="lang_edit_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="lang_history_statustext"><xsl:value-of select="//lang_history_statustext"></xsl:value-of></xsl:variable>
		<xsl:variable name="link_history"><xsl:value-of select="//link_history"></xsl:value-of></xsl:variable>
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

			<td align="left">

				<xsl:choose>
					<xsl:when test="link_to_files!=''">
						<xsl:variable name="link_to_file"><xsl:value-of select="link_to_files"></xsl:value-of>/<xsl:value-of select="directory"></xsl:value-of>/<xsl:value-of select="document_name"></xsl:value-of></xsl:variable>
						<a href="{$link_to_file}" target="_blank" onMouseover="window.status='{lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="document_name"></xsl:value-of></a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:variable name="link_view_file"><xsl:value-of select="link_view_file"></xsl:value-of></xsl:variable>
						<a href="{$link_view_file}" target="_blank" onMouseover="window.status='{$lang_view_file_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="document_name"></xsl:value-of></a>
					</xsl:otherwise>
				</xsl:choose>
			</td>
			<td align="left">
				<xsl:value-of select="title"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="doc_type"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="user"></xsl:value-of>
			</td>
			<td align="center">
				<xsl:variable name="link_view"><xsl:value-of select="link_view"></xsl:value-of></xsl:variable>
				<a href="{$link_view}" onMouseover="window.status='{$lang_view_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_view"></xsl:value-of></a>
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
		</tr>
	</xsl:template>

<!-- add / edit -->

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
				<form ENCTYPE="multipart/form-data" method="post" name="form" action="{$form_action}">
					<xsl:choose>
						<xsl:when test="value_document_name!=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_document_name"></xsl:value-of>
									<input type="hidden" name="values[document_name_orig]" value="{value_document_name}"></input>
									<input type="hidden" name="values[location_code]" value="{value_location_code}"></input>
								</td>
								<td>
									<xsl:value-of select="value_document_name"></xsl:value-of>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
					<tr>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_update_file"></xsl:value-of>
						</td>
						<td>
							<input type="file" size="50" name="document_file" onMouseout="window.status='';return true;">
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
							<xsl:value-of select="lang_version"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[version]" value="{value_version}" size="12" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_version_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>			
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_link"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[link]" value="{value_link}" size="50" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_link_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>			
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_title"></xsl:value-of>
						</td>
						<td>
							<input type="text" name="values[title]" value="{value_title}" size="50" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_title_statustext"></xsl:value-of>
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
							<textarea cols="60" rows="6" name="values[descr]" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_descr_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
								<xsl:value-of select="value_descr"></xsl:value-of>		
							</textarea>
						</td>
					</tr>
					<xsl:call-template name="vendor_form"></xsl:call-template>
					<tr>
						<td>
							<xsl:value-of select="lang_category"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="categories"></xsl:call-template>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="location_type='form'">
							<xsl:call-template name="location_form"></xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view"></xsl:call-template>
						</xsl:otherwise>
					</xsl:choose>
					<tr>
						<td>
							<xsl:value-of select="lang_coordinator"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="user_id_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_status"></xsl:value-of>
						</td>
						<td>
							<xsl:call-template name="status_select"></xsl:call-template>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<xsl:value-of select="lang_document_date"></xsl:value-of>
						</td>
						<td>
							<input type="text" id="values_document_date" name="values[document_date]" size="10" value="{value_document_date}" readonly="readonly" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_document_date_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
							<img id="values_document_date-trigger" src="{img_cal}" alt="{lang_datetitle}" title="{lang_datetitle}" style="cursor:pointer; cursor:hand;"></img>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="lang_branch"></xsl:value-of>
						</td>
						<td>
							<xsl:variable name="lang_branch_statustext"><xsl:value-of select="lang_branch_statustext"></xsl:value-of></xsl:variable>
							<select name="values[branch_id]" class="forms" onMouseover="window.status='{$lang_branch_statustext}'; return true;" onMouseout="window.status='';return true;">
								<option value=""><xsl:value-of select="lang_no_branch"></xsl:value-of></option>
								<xsl:apply-templates select="branch_list"></xsl:apply-templates>
							</select>
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
			<hr noshade="noshade" width="100%" align="center" size="1"></hr>
			<table width="80%" cellpadding="2" cellspacing="2" align="center">
				<xsl:choose>
					<xsl:when test="record_history=''">
						<tr>
							<td class="th_text" align="center">
								<xsl:value-of select="lang_no_history"></xsl:value-of>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_history"></xsl:value-of>
							</td>
						</tr>
						<!--  DATATABLE 0-->
						<!--  <xsl:apply-templates select="table_header_history"/><xsl:apply-templates select="record_history"/> -->
						<tr><td class="th_text" colspan="3"><div id="paging_0"></div><div id="datatable-container_0"></div></td></tr>	

					</xsl:otherwise>
				</xsl:choose>
			</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"></hr>

		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>
		</script>			
	</xsl:template>


	<xsl:template match="branch_list">
		<xsl:variable name="id"><xsl:value-of select="id"></xsl:value-of></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"></xsl:value-of></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="table_header_history">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_date"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"></xsl:value-of>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_action"></xsl:value-of>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="record_history">
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
			<td align="left">
				<xsl:value-of select="value_date"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_user"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_action"></xsl:value-of>
			</td>
			<td align="left">
				<xsl:value-of select="value_new_value"></xsl:value-of>
			</td>
		</tr>
	</xsl:template>

<!-- view -->
	<xsl:template match="view">
		<div align="left">
			<table cellpadding="2" cellspacing="2" width="80%" align="center">
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_document_name"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_document_name"></xsl:value-of>
					</td>
				</tr>
				<tr>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_version"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_version"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_title"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_title"></xsl:value-of>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_descr"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_descr"></xsl:value-of>		
					</td>
				</tr>
				<xsl:call-template name="vendor_view"></xsl:call-template>
				<tr>
					<td>
						<xsl:value-of select="lang_category"></xsl:value-of>
					</td>
					<xsl:for-each select="cat_list">
						<xsl:choose>
							<xsl:when test="selected='selected'">
								<td>
									<xsl:value-of select="name"></xsl:value-of>
								</td>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</tr>
				<xsl:call-template name="location_view"></xsl:call-template>
				<tr>
					<td>
						<xsl:value-of select="lang_coordinator"></xsl:value-of>
					</td>
					<xsl:for-each select="user_list">
						<xsl:choose>
							<xsl:when test="selected">
								<td>
									<xsl:value-of select="name"></xsl:value-of>
								</td>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_status"></xsl:value-of>
					</td>
					<xsl:for-each select="status_list">
						<xsl:choose>
							<xsl:when test="selected">
								<td>
									<xsl:value-of select="name"></xsl:value-of>
								</td>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</tr>
				<tr>
					<td valign="top">
						<xsl:value-of select="lang_document_date"></xsl:value-of>
					</td>
					<td>
						<xsl:value-of select="value_document_date"></xsl:value-of>			
					</td>
				</tr>
				<tr>
					<td>
						<xsl:value-of select="lang_branch"></xsl:value-of>
					</td>
					<xsl:for-each select="branch_list">
						<xsl:choose>
							<xsl:when test="selected">
								<td>
									<xsl:value-of select="name"></xsl:value-of>
								</td>
							</xsl:when>
						</xsl:choose>
					</xsl:for-each>
				</tr>
				<tr height="50">
					<td>
						<xsl:variable name="done_action"><xsl:value-of select="done_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_done"><xsl:value-of select="lang_done"></xsl:value-of></xsl:variable>
						<form method="post" action="{$done_action}">
							<input type="submit" class="forms" name="done" value="{$lang_done}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_done_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>

						</form>
						<xsl:variable name="edit_action"><xsl:value-of select="edit_action"></xsl:value-of></xsl:variable>
						<xsl:variable name="lang_edit"><xsl:value-of select="lang_edit"></xsl:value-of></xsl:variable>
						<form method="post" action="{$edit_action}">
							<input type="submit" class="forms" name="edit" value="{$lang_edit}" onMouseout="window.status='';return true;">
								<xsl:attribute name="onMouseover">
									<xsl:text>window.status='</xsl:text>
									<xsl:value-of select="lang_edit_statustext"></xsl:value-of>
									<xsl:text>'; return true;</xsl:text>
								</xsl:attribute>
							</input>
						</form>
					</td>
				</tr>
			</table>
			<hr noshade="noshade" width="100%" align="center" size="1"></hr>
			<table width="80%" cellpadding="2" cellspacing="2" align="center">
				<xsl:choose>
					<xsl:when test="record_history=''">
						<tr>
							<td class="th_text" align="center">
								<xsl:value-of select="lang_no_history"></xsl:value-of>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>
						<tr>
							<td class="th_text" align="left">
								<xsl:value-of select="lang_history"></xsl:value-of>
							</td>
						</tr>
						<xsl:apply-templates select="table_header_history"></xsl:apply-templates>
						<xsl:apply-templates select="record_history"></xsl:apply-templates>
					</xsl:otherwise>
				</xsl:choose>
			</table>
		</div>
		<hr noshade="noshade" width="100%" align="center" size="1"></hr>
	</xsl:template>
