<!-- $Id:$ -->

	<!-- add / edit -->
<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">
		<xsl:choose>
			<xsl:when test="mode = 'edit'">
				<script type="text/javascript">
					self.name="first_Window";
					<xsl:value-of select="lookup_functions"/>
				</script>
			</xsl:when>
		</xsl:choose>
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						edit_action:  <xsl:value-of select="edit_action"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>
		</script>
		<div class="yui-navset" id="survey_edit_tabview">
	
		<h1>
			<xsl:value-of select="php:function('lang', 'condition survey')" />
		</h1>


		<xsl:variable name="action_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.save')" />
		</xsl:variable>

			<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
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
						<xsl:when test="value_id !=''">
							<tr>
								<td class="th_text" valign="top">
									<a href="{link_pdf}" target="_blank">PDF</a>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
				<table cellpadding="2" cellspacing="2" width="80%" align="center">
					<tr>
						<td class="th_text">
							<xsl:value-of select="lang_entity"/>
						</td>
						<td class="th_text">
							<xsl:value-of select="entity_name"/>
						</td>
					</tr>
					<xsl:choose>
						<xsl:when test="value_id!=''">
							<tr>
								<td valign="top">
									<xsl:value-of select="lang_id"/>
								</td>
								<td>
									<xsl:value-of select="value_num"/>
									<input type="hidden" name="location_code" value="{location_code}"/>
									<input type="hidden" name="lookup_tenant" value="{lookup_tenant}"/>
									<input type="hidden" name="values[id]" value="{value_id}"/>
									<input type="hidden" name="values[num]" value="{value_num}"/>
								</td>
							</tr>
						</xsl:when>
					</xsl:choose>
				</table>
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div class="yui-content">
					<div id="generic">
					<table>
					<xsl:choose>
						<xsl:when test="location_data!=''">
								<xsl:choose>
									<xsl:when test="editable = 1">
										<xsl:call-template name="location_form"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:call-template name="location_view"/>
									</xsl:otherwise>
								</xsl:choose>
						</xsl:when>
					</xsl:choose>
				<tr>
					<td>
						<label for="name"><xsl:value-of select="php:function('lang', 'name')" /></label>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="editable = 1">
	   							<input id="title" name='title' type="text"
	   								formvalidator:FormField="yes"
	   								formvalidator:Type="TextBaseField">
	   							</input>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/title" />
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>

							<label for="name"><xsl:value-of select="php:function('lang', 'description')" /></label>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="editable = 1">
								<textarea id="description" name="values[description]" rows="5" cols="60"
									formvalidator:FormField="yes"
	   								formvalidator:Type="TextBaseField">
									<xsl:value-of select="project/description" disable-output-escaping="yes"/>
								</textarea>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="project/description" disable-output-escaping="yes"/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>
						<label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="editable = 1">
 								<select id="cat_id" name="values[cat_id]"
									formvalidator:FormField="yes"
	   								formvalidator:Type="SelectField">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
 								<select id="cat_id" disabled="disabled">
									<xsl:apply-templates select="categories/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
				<tr>
					<td>
							<label for="category"><xsl:value-of select="php:function('lang', 'date')" /></label>
					</td>
					<td>
							<input id="report_date" name='values[report_date]' type="text"
								formvalidator:FormField="yes"
								formvalidator:type="TextBaseField"/>

					</td>
				</tr>
				<tr>
					<td>
						<label for="status"><xsl:value-of select="php:function('lang', 'status')" /></label>
					</td>
					<td>
						<xsl:choose>
							<xsl:when test="editable = 1">
 								<select id="status_id" name="values[status_id]"
									formvalidator:FormField="yes"
	   								formvalidator:Type="SelectField">
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</xsl:when>
							<xsl:otherwise>
 								<select id="status_id" disabled="disabled">
									<xsl:apply-templates select="status_list/options"/>
								</select>
							</xsl:otherwise>
						</xsl:choose>

					</td>
				</tr>
				<tr>
					<td>
						<label for="vendor"><xsl:value-of select="php:function('lang', 'vendor')" /></label>
					</td>
					<td>

						<xsl:choose>
							<xsl:when test="editable = 1">
							    <div class="autocomplete">
							        <input type="hidden" id="vendor_id" name="values[vendor_id]"  value="{survey/vendor_id}"/>
							        <input type="text" id="vendor_name" name="vendor_name" value="{survey/vendor_name}">
									</input>
							        <div id="vendor_container"/>
							    </div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="survey/vendor_name" />
							</xsl:otherwise>
						</xsl:choose>

<!--

					</td>
				</tr>
				<tr>
					<td>

						 <div class="label">
 							<label for="age2">Age:</label>						
						 </div>
							<input id="age2" type="text"
								formvalidator:FormField="yes"
								formvalidator:type="IntegerField"
								formvalidator:max="100"
								formvalidator:min="10"/>
						<div class="clearDiv"></div>

						  <div class="label">
 							<label for="income2">Income ($):</label>						
						 </div>
							<input id="income2" type="text"
								formvalidator:FormField="yes"
								formvalidator:type="DoubleField"
								formvalidator:maxDecimalPlaces="2"
								formvalidator:max="40000"
								formvalidator:maxInclusive="true"/>
						<div class="clearDiv"></div>
					
					-->

					</td>
				</tr>

					</table>
					</div>

			<div id="documents">
			</div>
			<div id="import">
			</div>


					<xsl:choose>
						<xsl:when test="files!='' or fileupload = 1">
							<div id="files">
								<script type="text/javascript">
									var fileuploader_action = <xsl:value-of select="fileuploader_action"/>;
								</script>
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<!-- <xsl:call-template name="file_list"/> -->
									<tr>
										<td align="left" valign="top">
											<xsl:value-of select="//lang_files"/>
										</td>
										<td>
											<div id="datatable-container_0"/>
										</td>
									</tr>
									<xsl:choose>
										<xsl:when test="cat_list='' and fileupload = 1 and mode = 'edit'">
											<xsl:call-template name="file_upload"/>
										</xsl:when>
									</xsl:choose>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:choose>
						<xsl:when test="documents != ''">
							<div id="document">
								<!-- Some style for the expand/contract section-->
								<style>
									#expandcontractdiv {border:1px dotted #dedede; margin:0 0 .5em 0; padding:0.4em;}
									#treeDiv1 { background: #fff; padding:1em; margin-top:1em; }
								</style>
								<script type="text/javascript">
									var documents = <xsl:value-of select="documents"/>;
								</script>
								<!-- markup for expand/contract links -->
								<div id="expandcontractdiv">
									<a id="expand" href="#">
										<xsl:value-of select="php:function('lang', 'expand all')"/>
									</a>
									<xsl:text> </xsl:text>
									<a id="collapse" href="#">
										<xsl:value-of select="php:function('lang', 'collapse all')"/>
									</a>
								</div>
								<div id="treeDiv1"/>
							</div>
						</xsl:when>
					</xsl:choose>

					<xsl:choose>
						<xsl:when test="value_id !=''">
							<div id="related">
								<table cellpadding="2" cellspacing="2" width="80%" align="center">
									<tr>
										<td valign='top'>
											<xsl:value-of select="php:function('lang', 'started from')"/>
										</td>
										<td>
											<div id="datatable-container_1"/>
										</td>
									</tr>
									<tr>
										<td valign='top'>
											<xsl:value-of select="php:function('lang', 'used in')"/>
										</td>
										<td>
											<div id="datatable-container_2"/>
										</td>
									</tr>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>

				</div>

				<table>
				<tr>
					<td>
					</td>
					<td>
						<div class="form-buttons">
							<xsl:choose>
								<xsl:when test="editable = 1">
									<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
									<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
									<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
									<input class="submit" type="button" name="cancelButton" id ='cancelButton' value="{$lang_cancel}" title = "{$lang_cancel}" onClick="document.cancel_form.submit();"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
									<xsl:variable name="lang_new_activity"><xsl:value-of select="php:function('lang', 't_new_activity')" /></xsl:variable>
									<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
									<input type="submit" name="new_activity" value="{$lang_new_activity}" title = "{$lang_new_activity}" />
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</td>
				</tr>

			</table>


			</form>
		</div>
		<div id="lightbox-placeholder" style="background-color:#000000;color:#FFFFFF;display:none">
			<div class="hd" style="background-color:#000000;color:#000000; border:0; text-align:center">
				<xsl:value-of select="php:function('lang', 'fileuploader')"/>
			</div>
			<div class="bd" style="text-align:center;"> </div>
		</div>

		<xsl:variable name="cancel_url">
			<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.index')" />
		</xsl:variable>

		<form name="cancel_form" id="cancel_form" action="{$cancel_url}" method="post">
		</form>


	</xsl:template>


<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected'">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
