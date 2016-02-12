<!-- $Id$ -->
<xsl:template match="section" xmlns:php="http://php.net/xsl">

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>
	
	<xsl:variable name="tab_selected">
		<xsl:value-of select="tab_selected"/>
	</xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<xsl:choose>
						<xsl:when test="normalize-space(//header/selected_location) != ''">
							<div class="toolbar-container">
								<div>
									<xsl:for-each select="filters">
										<xsl:variable name="name">
											<xsl:value-of select="name"/>
										</xsl:variable>
										<select id="{$name}" name="{$name}">
											<xsl:for-each select="list">
												<xsl:variable name="id">
													<xsl:value-of select="id"/>
												</xsl:variable>
												<xsl:choose>
													<xsl:when test="id = 'NEW'">
														<option value="{$id}" selected="selected">
															<xsl:value-of select="name"/>
														</option>
													</xsl:when>
													<xsl:otherwise>
														<xsl:choose>
															<xsl:when test="selected = 'selected'">
																<option value="{$id}" selected="selected">
																	<xsl:value-of select="name"/>
																</option>
															</xsl:when>
															<xsl:otherwise>
																<option value="{$id}">
																	<xsl:value-of select="name"/>
																</option>
															</xsl:otherwise>
														</xsl:choose>
													</xsl:otherwise>
												</xsl:choose>
											</xsl:for-each>
										</select>									
									</xsl:for-each>
								</div>
							</div>
							<div class="tickets">
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
						</xsl:when>
						<xsl:otherwise>
							<div class="tickets">
								<xsl:value-of select="php:function('lang', 'no_buildings')"/>
							</div>
						</xsl:otherwise>
					</xsl:choose>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />	
			</div>
		</div>
	</div>
	<script type="text/javascript" class="init">

		<xsl:for-each select="filters">
			<xsl:if test="type = 'filter'">
				$('select#<xsl:value-of select="name"/>').change( function() 
				{
				<xsl:value-of select="extra"/>
				filterData('<xsl:value-of select="name"/>', $(this).val());
				});
			</xsl:if>
		</xsl:for-each>

		<![CDATA[
			function filterData(param, value)
			{
				oTable0.dataTableSettings[0]['ajax']['data'][param] = value;
				oTable0.fnDraw();
			}
		]]>

	</script>
</xsl:template>

<xsl:template match="lightbox_name" xmlns:php="http://php.net/xsl">
</xsl:template>

<xsl:template match="add_ticket" xmlns:php="http://php.net/xsl">

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>

	<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="details">
				<xsl:if test="noform != 1">
					<div class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'category')" />
							</label>
							<select name="values[cat_id]" >
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'category')" />
								</xsl:attribute>
								<xsl:apply-templates select="category_list"/>
							</select>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'subject')" />
							</label>
							<input type="text" name="values[title]" value="{title}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'locationdesc')" />
							</label>
							<input type="text" name="values[locationdesc]" value="{locationdesc}"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'description')" />
							</label>
							<textarea cols="50" rows="10" name="values[description]" wrap="virtual" onMouseout="window.status='';return true;">
								<xsl:value-of select="description"/>
							</textarea>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'file')" />
							</label>
							<input type="file" name="file" size="50">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'file')" />
								</xsl:attribute>
							</input>
						</div>
						<div class="pure-control-group">
							<xsl:variable name="lang_send">
								<xsl:value-of select="php:function('lang', 'send')" />
							</xsl:variable>
							<label>
								<input type="submit" class="pure-button pure-button-primary" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
							</label>
						</div>
					</div>
					<div class="ticket_content attributes">
						<xsl:apply-templates select="custom_attributes/attributes"/>
					</div>
				</xsl:if>
			</div>
		</div>
	</form>
</xsl:template>

<xsl:template match="category_list">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


