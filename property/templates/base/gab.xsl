
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
		<xsl:when test="list_gab_detail">
			<xsl:apply-templates select="list_gab_detail"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="list_gab"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<!-- New template-->
<xsl:template match="list_gab_detail">
	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:for-each select="datatable_def">
			<xsl:if test="container = 'datatable-container_0'">
				<xsl:call-template name="table_setup">
					<xsl:with-param name="container" select ='container'/>
					<xsl:with-param name="requestUrl" select ='requestUrl' />
					<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
					<xsl:with-param name="tabletools" select ='tabletools' />
					<xsl:with-param name="data" select ='data' />
					<xsl:with-param name="config" select ='config' />
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<xsl:for-each select="info">
					<div>
						<xsl:value-of select="name"/>:<xsl:value-of select="value"/>
					</div>
				</xsl:for-each>
			</div>
			<div class="pure-u-2-3">
				<xsl:for-each select="top_toolbar">
					<a class="pure-button pure-button-primary" href="{url}">
						<xsl:value-of select="value"/>
					</a>
				</xsl:for-each>
			</div>
		</div>
	</div>
</xsl:template>
		

<!-- New template-->
<xsl:template match="table_add">
	<tr>
		<td height="50">
			<xsl:variable name="add_action">
				<xsl:value-of select="add_action"/>
			</xsl:variable>
			<xsl:variable name="lang_add">
				<xsl:value-of select="lang_add"/>
			</xsl:variable>
			<form method="post" action="{$add_action}">
				<input type="submit" name="add" value="{$lang_add}">
					<xsl:attribute name="title">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_add_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="table_done">
	<tr>
		<td height="50">
			<xsl:variable name="done_action">
				<xsl:value-of select="done_action"/>
			</xsl:variable>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<form method="post" action="{$done_action}">
				<input type="submit" name="done" value="{$lang_done}">
					<xsl:attribute name="title">
						<xsl:text>window.status='</xsl:text>
						<xsl:value-of select="lang_done_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- add / edit -->
<xsl:template match="edit">
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<dl>
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</dl>
		</xsl:when>
	</xsl:choose>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form method="post" id="form" name="form" action="{$form_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<xsl:choose>
						<xsl:when test="gaards_nr&gt;0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_kommune_nr"/>
								</label>
								<xsl:value-of select="kommune_nr"/>
								<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_gaards_nr"/>
								</label>
								<xsl:value-of select="gaards_nr"/>
								<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_bruksnr"/>
								</label>
								<xsl:value-of select="bruks_nr"/>
								<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_feste_nr"/>
								</label>
								<xsl:value-of select="feste_nr"/>
								<input type="hidden" name="values[feste_nr]" value="{feste_nr}"/>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_seksjons_nr"/>
								</label>
								<xsl:value-of select="seksjons_nr"/>
								<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"/>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_kommune_nr"/>
								</label>
								<input type="text" name="values[kommune_nr]" maxlength="4" size="8" value="{kommune_nr}">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_kommune_nr_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_gaards_nr"/>
								</label>
								<input type="text" name="values[gaards_nr]" maxlength="5" size="8">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_gaards_nr_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_bruksnr"/>
								</label>
								<input type="text" name="values[bruks_nr]" maxlength="4" size="8">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_bruks_nr_statustext"/>
									</xsl:attribute>
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>

								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_feste_nr"/>
								</label>
								<input type="text" name="values[feste_nr]" maxlength="4" size="8">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_feste_nr_statustext"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_seksjons_nr"/>
								</label>
								<input type="text" name="values[seksjons_nr]" maxlength="3" size="8">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_seksjons_nr_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:otherwise>
					</xsl:choose>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_remark"/>
						</label>
						<textarea cols="60" rows="6" name="values[remark]">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_remark_statustext"/>
							</xsl:attribute>
							<xsl:value-of select="value_remark"/>
						</textarea>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_owner"/>
						</label>
						<xsl:choose>
							<xsl:when test="value_owner = 'yes'">
								<input type="checkbox" name="values[owner]" value="yes" checked="checked">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_lookup_form_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:when>
							<xsl:otherwise>
								<input type="checkbox" name="values[owner]" value="yes">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_lookup_form_statustext"/>
									</xsl:attribute>
								</input>
							</xsl:otherwise>
						</xsl:choose>
					</div>
					<xsl:choose>
						<xsl:when test="lookup_type='form2'">
							<xsl:call-template name="location_form2"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="lang_propagate"/>
								</label>
								<input type="checkbox" name="values[propagate]" value="True">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_propagate_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="location_view2"/>
						</xsl:otherwise>
					</xsl:choose>
				</fieldset>
			</div>
			<xsl:choose>
				<xsl:when test="attributes_group!=''">
					<xsl:call-template name="attributes_values"/>
				</xsl:when>
			</xsl:choose>

		</div>
		<div class="proplist-col">
			<input type="hidden" name="values[action]" value="{action}"/>
			<xsl:variable name="lang_save">
				<xsl:value-of select="lang_save"/>
			</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_save_statustext"/>
				</xsl:attribute>
			</input>

			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_done_statustext"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form method="post" name="done_form" id="done_form" action="{$done_action}"></form>

</xsl:template>

<!-- view -->
<xsl:template match="view">
	<xsl:variable name="edit_action">
		<xsl:value-of select="edit_action"/>
	</xsl:variable>
	<form method="post" id="form" name="form" action="{$edit_action}" class= "pure-form pure-form-aligned">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="generic">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_kommune_nr"/>
						</label>
						<xsl:value-of select="kommune_nr"/>
						<input type="hidden" name="values[kommune_nr]" value="{kommune_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_gaards_nr"/>
						</label>
						<xsl:value-of select="gaards_nr"/>
						<input type="hidden" name="values[gaards_nr]" value="{gaards_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_bruksnr"/>
						</label>
						<xsl:value-of select="bruks_nr"/>
						<input type="hidden" name="values[bruks_nr]" value="{bruks_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_feste_nr"/>
						</label>
						<xsl:value-of select="feste_nr"/>
						<input type="hidden" name="values[feste_nr]" value="{feste_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_seksjons_nr"/>
						</label>
						<xsl:value-of select="seksjons_nr"/>
						<input type="hidden" name="values[seksjons_nr]" value="{seksjons_nr}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_remark"/>
						</label>
						<xsl:value-of select="value_remark"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="lang_owner"/>
						</label>
						<xsl:value-of select="value_owner"/>
					</div>
					<xsl:call-template name="location_view"/>
				</fieldset>
			</div>
		</div>
		<div class="proplist-col">
			<xsl:variable name="lang_edit">
				<xsl:value-of select="lang_edit"/>
			</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="edit" value="{$lang_edit}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_edit_statustext"/>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_done">
				<xsl:value-of select="lang_done"/>
			</xsl:variable>
			<input type="button" class="pure-button pure-button-primary" name="done" value="{$lang_done}" onClick="document.done_form.submit();">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_done_statustext"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
	<xsl:variable name="done_action">
		<xsl:value-of select="done_action"/>
	</xsl:variable>
	<form method="post" name="done_form" id="done_form" action="{$done_action}"></form>
		
</xsl:template>
