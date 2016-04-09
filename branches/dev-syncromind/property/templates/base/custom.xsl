
<!-- $Id$ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
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
						<xsl:value-of select="lang_add_statustext"/>
					</xsl:attribute>
				</input>
			</form>
		</td>
	</tr>
</xsl:template>

<!-- add / edit -->
<xsl:template match="edit">
	<script type="text/javascript">
		self.name="first_Window";
		<xsl:value-of select="lookup_functions"/>
	</script>
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<xsl:variable name="edit_url">
		<xsl:value-of select="edit_url"/>
	</xsl:variable>
	<form name="form" class="pure-form pure-form-aligned" id="form" method="post" action="{$edit_url}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="general">
				<xsl:choose>
					<xsl:when test="value_custom_id!=''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_custom_id"/>
							</label>
                                                                        
							<xsl:value-of select="value_custom_id"/>
						</div>
					</xsl:when>
				</xsl:choose>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_name"/>
					</label>
					<input type="text" name="values[name]" data-validation="required" value="{value_name}">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_name_statustext"/>
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="lang_sql_text"/>
					</label>
					<textarea cols="60" rows="6" name="values[sql_text]" data-validation="required">
						<xsl:attribute name="title">
							<xsl:value-of select="lang_sql_statustext"/>
						</xsl:attribute>
						<xsl:value-of select="value_sql_text"/>
					</textarea>
				</div>
				<xsl:choose>
					<xsl:when test="value_custom_id != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="lang_columns"/>
							</label>
							<!--xsl:call-template name="columns"/-->
							<div class="pure-custom">
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

							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_name"/>
								</label>
								<input type="text" name="values[new_name]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_new_name_statustext"/>
									</xsl:attribute>
								</input>
							</div>

							<div class="pure-control-group">
								<label for="name">
									<xsl:value-of select="lang_descr"/>
								</label>
								<input type="text" name="values[new_descr]">
									<xsl:attribute name="title">
										<xsl:value-of select="lang_new_descr_statustext"/>
									</xsl:attribute>
								</input>
							</div>
						</div>
					</xsl:when>
				</xsl:choose>
			</div>
		</div>
		<div class="pure-control-group">
			<xsl:variable name="lang_save">
				<xsl:value-of select="lang_save"/>
			</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_save}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_save_statustext"/>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_apply">
				<xsl:value-of select="lang_apply"/>
			</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="values[apply]" value="{$lang_apply}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_apply_statustext"/>
				</xsl:attribute>
			</input>
			<xsl:variable name="lang_cancel">
				<xsl:value-of select="lang_cancel"/>
			</xsl:variable>
			<input type="submit" class="pure-button pure-button-primary" name="values[cancel]" value="{$lang_cancel}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_cancel_statustext"/>
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
