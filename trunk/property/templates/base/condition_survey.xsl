<!-- $Id:$ -->

<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">

<!--<xsl:call-template name="yui_phpgw_i18n"/>-->
<div class="yui-navset yui-navset-top">
	
	<h1>
			<xsl:value-of select="php:function('lang', 'condition survey')" />
	</h1>
	
		<div class="content-wrp">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.save')" />
			</xsl:variable>

			<xsl:variable name="disabled">
				<xsl:choose>
					<xsl:when test="editable = 1">
						<xsl:text>disabled</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text></xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>


			<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
				<div class="formBody">
					 <div class="row">
						<div class="label">
							<label for="name"><xsl:value-of select="php:function('lang', 'name')" /></label>
						</div>
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
						<div class="clearDiv"></div>
					</div>
					 <div class="row">
						<div class="label">
							<label for="name"><xsl:value-of select="php:function('lang', 'description')" /></label>
						</div>
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
						 <div class="clearDiv"></div>
					</div>
					 <div class="row">
						<div class="label">
							<label for="category"><xsl:value-of select="php:function('lang', 'category')" /></label>
						</div>
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
						<div class="clearDiv"></div>
					</div>

					<div class="row">
						 <div class="label">
							<label for="category"><xsl:value-of select="php:function('lang', 'date')" /></label>
						 </div>
							<input id="report_date" name='values[report_date]' type="text"
								formvalidator:FormField="yes"
								formvalidator:type="TextBaseField"/>
							<div class="clearDiv"></div>
					</div>



					<div class="row">
						 <div class="label">
 							<label for="age2">Age:</label>						
						 </div>
							<input id="age2" type="text"
								formvalidator:FormField="yes"
								formvalidator:type="IntegerField"
								formvalidator:max="100"
								formvalidator:min="10"/>
						<div class="clearDiv"></div>
					</div>
					<div class="row">
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
					</div>
					<div class="form-buttons">
						<xsl:choose>
							<xsl:when test="editable = 1">
								<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
								<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
								<input type="submit" name="save_project" value="{$lang_save}" title = "{$lang_save}" />
								<input type="submit" name="cancel_project" value="{$lang_cancel}" title = "{$lang_cancel}" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
								<xsl:variable name="lang_new_activity"><xsl:value-of select="php:function('lang', 't_new_activity')" /></xsl:variable>
								<input type="submit" name="edit_project" value="{$lang_edit}" title = "{$lang_edit}" />
								<input type="submit" name="new_activity" value="{$lang_new_activity}" title = "{$lang_new_activity}" />
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</div>
			</form>
		</div>


</div>


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
