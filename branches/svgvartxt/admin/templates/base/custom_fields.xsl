<!--
	Custom fields management template
	Written by Dave Hall skwashd at phpgroupware.org 
	$Id$ 
-->
	<xsl:template match="custom_fields">
		<form method="get" action="#">
			<div id="admin_custom_fields">
				<div id="search_options">
					<label for="search"><xsl:value-of select="lang_search" />:</label>
					<input type="text" name="search" id="search" onkeyup="doSearch(this, 'custom_fields_list', 'tr', 'row_', 3);" />
					<!-- not yet implemented 
					<label for="filter"><xsl:value-of select="lang_filter" />:</label>
					<select name="filter" id="filter">
					</select>
					-->
				</div><br />
				<div id="admin_controls">
					<button type="button" onclick="addField('{appname}');">
						<img src="{img_add}" alt="{lang_add}" />
					<xsl:value-of select="lang_add" />
					</button>
					<button type="button" onclick="viewField();">
						<img src="{img_view}" alt="{lang_view}" />
						<xsl:value-of select="lang_view" />
					</button>
					<button type="button" onclick="editField();">
						<img src="{img_edit}" alt="{lang_edit}" />
						<xsl:value-of select="lang_edit" />
					</button>
					<button type="button" onclick="removeField();">
						<img src="{img_remove}" alt="{lang_remove}" />
						<xsl:value-of select="lang_remove" />
					</button>
				</div>
				<table>
					<thead>
						<tr>
							<td><xsl:value-of select="lang_id" /></td>
							<td><xsl:value-of select="lang_field_name" /></td>
							<td class="last"><xsl:value-of select="lang_enabled" /></td>
						</tr>
					</thead>
					<tbody id="custom_fields_list">
						<xsl:if test="count(//custom_fields_field/id) > 0">
							<xsl:apply-templates select="custom_fields_field" />
						</xsl:if>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</form>
		<div id="modal_bg"> &nbsp; </div>
		
		
		<div id="fields_dialog_add" class="dialog">
			<div id="fields_dialog_box_add">
				<div class="content_title">
					<img class="app_icon" src="{app_icon}" alt="{appname} {lang_icon}" />
					<h1><xsl:value-of select="appname" /> - <xsl:value-of select="lang_add_fields" /></h1>
					<img class="close" src="{img_close}" alt="{lang_close}" onclick="dialogCancel('add');"/>
				</div>
				<div class="dialog_content">
					<form>
						<div id="fields_add_panel_0">
							<h1><xsl:value-of select="lang_add_fields" /></h1>
							<p>
								<xsl:value-of select="lang_explain_add" />
							</p>
							<p>
								<xsl:value-of select="lang_click_forward" />
							</p>
							<input type="hidden" class="hidden" id="add_field_appname" name="add_field_appname" value="{appname}" />
						</div>

						<div id="fields_add_panel_1">
							<h1><xsl:value-of select="lang_basics" /></h1>
							
							<p>
								<xsl:value-of select="lang_explain_basics" />
							</p>

							<label for="add_field_name"><xsl:value-of select="lang_field_name" />:</label>
							<input type="text" name="add_field_name" id="add_field_name" onkeyup="addFieldPanel1Updated();" /><br />

							<label for="add_field_label">
								<xsl:value-of select="lang_label" />
							</label>
							<input type="text" name="add_field_label" id="add_field_label" onkeyup="addFieldPanel1Updated();" /><br />

							<label for="add_field_type"><xsl:value-of select="lang_field_type" />:</label>
							<select name="add_field_type" id="add_field_type" onkeyup="addFieldPanel1Updated();" onchange="addFieldPanel1Updated()">
								<xsl:apply-templates select="custom_field_types/field_type" />
							</select><br />
						</div>
						
						<!-- List -->
						<div id="fields_add_panel_2">
							<h1><xsl:value-of select="lang_list" /></h1>
							<p>
								<xsl:value-of select="lang_explain_list" />
							</p>
							<label for="add_value"><xsl:value-of select="lang_value"/>:</label>
							<input type="text" id="add_value" name="add_field_value" /><br />
							<ul>
								<li><xsl:value-of select="lang_values" /></li>
							</ul>
						</div>

						<!-- DB Lookup -->
						<div id="fields_add_panel_3">
							<h1><xsl:value-of select="lang_db_lookup" /></h1>
							TODO :)
						</div>

						<!-- Finish -->
						<div id="fields_add_panel_4">
							<h1><xsl:value-of select="lang_done" /></h1>
							<p>
								<xsl:value-of select="lang_completed_add" />
							</p>
							<p>
								<xsl:value-of select="lang_click_apply" />
							</p>
							
						</div>

						<div id="fields_add_controls" class="popup_buttons">
							<button type="button" id="add_cancel" onclick="dialogCancel('add');">
								<img src="{img_cancel}" alt="{lang_cancel}" />
								<xsl:value-of select="lang_cancel" />
							</button>
							<button type="button" id="add_back" disabled="disabled" onClick="addBack();">
								<img src="{img_back}" alt="{lang_back}" />
								<xsl:value-of select="lang_back" />
							</button>
							<button type="button" id="add_forward" onClick="addForward();">
								<img src="{img_forward}" alt="{lang_forward}" />
								<xsl:value-of select="lang_forward" />
							</button>
							<button type="button" id="add_apply" onClick="addApply();" disabled="disabled">
								<img src="{img_ok}" alt="{lang_apply}" />
								<xsl:value-of select="lang_apply" />
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="fields_dialog_edit" class="dialog">
			<div id="fields_dialog_box_edit">
				<div class="content_title">
					<img class="app_icon" src="{app_icon}" alt="{appname} {lang_icon}" />
					<h1><xsl:value-of select="appname" /> - <xsl:value-of select="lang_edit" /></h1>
					<img class="close" src="{img_close}" alt="{lang_close}" onclick="dialogCancel('edit');"/>
				</div>
				<div class="dialog_content">
					<form>
						<div>
							<p>
								<xsl:value-of select="lang_explain_edit" />
							</p>
							<label for="edit_name"><xsl:value-of select="lang_field_name" />:</label>
							<input type="text" name="edit_name" id="edit_name" /><br />

							<label for="edit_label"><xsl:value-of select="lang_label" />:</label>
							<input type="text" name="edit_label" id="edit_label" /><br />

							<label for="edit_type"><xsl:value-of select="lang_field_type" />:</label>
							<select name="edit_type" id="edit_type">
								<xsl:apply-templates select="custom_field_types/field_type" />
							</select>
							<br />
							<input type="hidden" name="edit_id" id="edit_id" class="hidden" />
						</div>

						<div id="fields_edit_controls" class="popup_buttons">
							<button type="button" id="edit_cancel" onclick="dialogCancel('edit');">
								<img src="{img_cancel}" alt="{lang_cancel}" />
								<xsl:value-of select="lang_cancel" />
							</button>
							<button type="button" id="edit_save" onclick="doEdit();">
								<img src="{img_save}" alt="{lang_save}" />
								<xsl:value-of select="lang_save" />
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div id="fields_dialog_view" class="dialog">
			<div id="fields_dialog_box_view">
				<div class="content_title">
					<img class="app_icon" src="{app_icon}" alt="{appname} {lang_icon}" />
					<h1><xsl:value-of select="appname" /> - <xsl:value-of select="lang_view_field" /></h1>
					<img class="close" src="{img_close}" alt="{lang_close}" onclick="dialogCancel('view');"/>
				</div>
				<div class="dialog_content">
					<span class="mock_label"><xsl:value-of select="lang_id" />:</span>
					<span class="mock_value" id="view_id">&nbsp;</span><br />
				
					<span class="mock_label"><xsl:value-of select="lang_application" />:</span>
					<span class="mock_value" id="view_appname">&nbsp;</span><br />
				
					<span class="mock_label"><xsl:value-of select="lang_field_name" />:</span>
					<span class="mock_value" id="view_name">&nbsp;</span><br />
					
					<span class="mock_label"><xsl:value-of select="lang_label" />:</span>
					<span class="mock_value" id="view_label">&nbsp;</span><br />
					
					<span class="mock_label"><xsl:value-of select="lang_field_type" />:</span>
					<span class="mock_value" id="view_type">&nbsp;</span><br />
					
					<ul id="view_list">
						<li><xsl:value-of select="lang_values"/></li>
					</ul>
					<div id="fields_add_controls" class="popup_buttons">
						<form method="get" action="#">
							<button type="button" id="view_close" onclick="dialogCancel('view');">
								<img src="{img_close_icon}" alt="{lang_close}" />
								<xsl:value-of select="lang_close" />
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div id="fields_dialog_remove" class="dialog">
			<div id="fields_dialog_box_remove">
				<div class="content_title">
					<img class="app_icon" src="{app_icon}" alt="{appname} {lang_icon}" />
					<h1><xsl:value-of select="appname" /> - <xsl:value-of select="lang_remove_field" /></h1>
					<img class="close" src="{img_close}" alt="{lang_close}" onclick="dialogCancel('remove');"/>
				</div>
				<div class="dialog_content">
					<p>
						<xsl:value-of select="lang_explain_remove" />
					</p>
					<span class="mock_label"><xsl:value-of select="lang_id" />:</span>
					<span class="mock_value" id="remove_field_id">&nbsp;</span><br />
				
					<span class="mock_label"><xsl:value-of select="lang_application" />:</span>
					<span class="mock_value" id="remove_appname">&nbsp;</span><br />
				
					<span class="mock_label"><xsl:value-of select="lang_field_name" />:</span>
					<span class="mock_value" id="remove_name">&nbsp;</span><br />

					<p>
						<em><xsl:value-of select="lang_warn_no_undo" /></em>
					</p>					

					<div id="fields_add_controls" class="popup_buttons">
						<form method="get" action="#">
							<input type="hidden" id="remove_id" name="remove_id" class="hidden" value="" />
							<!-- FIXME Wire this up
							<button type="button" id="remove_deactivate" onclick="disableNotRemove();">
								<xsl:value-of select="lang_disable" />
							</button>
							-->
							<button type="button" id="remove_no" onclick="dialogCancel('remove');">
								<img src="{img_no}" alt="{lang_no}" />
								<xsl:value-of select="lang_no" />
							</button>
							<button type="button" id="remove_yes" onclick="doRemove();">
								<img src="{img_yes}" alt="{lang_yes}" />
								<xsl:value-of select="lang_yes" />
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>		
	</xsl:template>

	
	<xsl:template match="field_type">
		<option value="{id}"><xsl:value-of select="value" /></option>
	</xsl:template>
	
	<xsl:template match="custom_fields_field">
		<tr id="row_{id}" onclick="highlight({id});">
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
			<td><xsl:value-of select="id" /></td>
			<td><xsl:value-of select="field_name" /></td>
			<td>
				<span class="cbStyled" id="mockCheckbox{id}">
					<a onkeypress="toggleCheckbox(this, event.keyCode, 'mockCheckbox{id}');" onclick="toggleCheckbox(this,'', 'mockCheckbox{id}' );return false;" href="#">
						<xsl:attribute name="class">
							<xsl:choose>
								<xsl:when test="active = '1'">
									<xsl:text>mock_checkbox_checked</xsl:text>
								</xsl:when>
								<xsl:otherwise>
									<xsl:text>mock_checkbox</xsl:text>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						&nbsp;
					</a>
				</span>
			</td>
		</tr>
	</xsl:template>