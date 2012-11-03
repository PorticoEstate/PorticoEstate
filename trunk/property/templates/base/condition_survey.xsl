<!-- $Id:$ -->

<xsl:template match="data" xmlns:formvalidator="http://www.w3.org/TR/html4/" xmlns:php="http://php.net/xsl">

<!--<xsl:call-template name="yui_phpgw_i18n"/>-->
<div class="yui-navset yui-navset-top">
	
	<h1>
			<xsl:value-of select="php:function('lang', 'condition survey')" />
	</h1>
	
	<div id="project_details" class="content-wrp">
		<div id="details">
			<xsl:variable name="action_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:property.uicondition_survey.save')" />
			</xsl:variable>
			<form name="form" id="form" action="{$action_url}" method="post" ENCTYPE="multipart/form-data">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="name"><xsl:value-of select="php:function('lang','title')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable = 1">
							<xsl:if test="project/error_msg_array/name != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/name" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<input class = "required" type="text" name="name" id="name" value="{project/name}" size="100"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="project_type"><xsl:value-of select="php:function('lang','Project_type')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable = 1">
							<xsl:if test="project/error_msg_array/project_type_id != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/project_type_id" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<select  class = "required" id="project_type_id" name="project_type_id">
								<xsl:apply-templates select="categories/options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/project_type_label" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable = 1">
							<xsl:if test="project/error_msg_array/description != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="project/error_msg_array/description" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<div style="margin-left:0; margin-bottom: 3px;" class="help_text line">Angi startdato for aktiviteten</div>
							<textarea id="description" name="description" rows="5" cols="60"><xsl:value-of select="project/description" disable-output-escaping="yes"/></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="project/description" disable-output-escaping="yes"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
				</dl>

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
			</form>
		</div>
	</div>
</div>

        <div class="mainContainer">
            <form id="basicExample1" action="formPost.html">
                <div class="formBody">
                    <h2>Simple Dynamic Validators</h2>
                    <div class="info">
                        This shows the bare minimum needed in order to
                        get the form validator working with your existing form.
                        The form validator will insert indicators and validators
                        for your inputs.
                    </div>
                    <div class="row">
                        <div class="column1Backing"></div>
                        <div class="column2Backing"></div>
                        <div class="label">First Name:</div>
                        <div class="value">
                            <input id="firstName" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:Type="TextBaseField" />
                        </div>
                        <div class="label">Last Name:</div>
                        <div class="value">
                            <input id="lastName" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:Type="TextBaseField" />
                        </div>
                        <div class="clearDiv"></div>
                    </div>
                    <div class="row">
                        <div class="column1Backing"></div>
                        <div class="column2Backing"></div>
                        <div class="label">Age:</div>
                        <div class="value">
                            <input id="age" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:type="IntegerField"
                                formvalidator:max="100"
                                formvalidator:min="10"/>
                        </div>
                        <div class="label">Income ($):</div>
                        <div class="value">
                            <input id="income" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:type="DoubleField"
                                formvalidator:maxDecimalPlaces="2"
                                formvalidator:max="40000"
                                formvalidator:maxInclusive="true"/>
                        </div>
                        <div class="clearDiv"></div>
                    </div>
                    <div class="buttonRow">
                        <input type="submit" id="save" class="button" value="Save" />
                        <input type="button" id="clearButton" class="button" value="Clear" />
                        <input type="reset" class="button" value="Reset" />
                        <input type="button" class="button" value="Cancel" />
                    </div>
                </div>
            </form>
            <form id="basicExample2" action="formPost.html">
                <div class="formBody">
                    <h2>Advanced Dynamic Validators</h2>
                    <div class="info">
                        This will show you how to get creative with the form
                        validator and do more than the basics, while still not
                        changing your HTML very much.
                    </div>
                    <div class="row">
                        <div class="column1Backing"></div>
                        <div class="column2Backing"></div>
                        <div class="label">First Name:</div>
                        <div class="value">
                            <input id="firstName2" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:Type="TextBaseField"/>
                        </div>
                        <div class="label">Last Name:</div>
                        <div class="value">
                            <input id="lastName2" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:Type="TextBaseField"/>
                        </div>
                        <div class="clearDiv"></div>
                    </div>
                    <div class="row">
                        <div class="column1Backing"></div>
                        <div class="column2Backing"></div>
                        <div class="label">Age:</div>
                        <div class="value">
                            <input id="age2" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:type="IntegerField"
                                formvalidator:max="100"
                                formvalidator:min="10"/>
                        </div>
                        <div class="label">Income ($):</div>
                        <div class="value">
                            <input id="income2" type="text"
                                formvalidator:FormField="yes"
                                formvalidator:type="DoubleField"
                                formvalidator:maxDecimalPlaces="2"
                                formvalidator:max="40000"
                                formvalidator:maxInclusive="true"/>
                        </div>
                        <div class="clearDiv"></div>
                    </div>
                    <div class="buttonRow">
                        <input type="submit" class="button" value="Save" />
                        <input type="button" id="clearButton2" class="button" value="Clear" />
                        <input type="reset" class="button" value="Reset" />
                        <input type="button" class="button" value="Cancel" />
                    </div>
                </div>
            </form>
        </div>

<script type="text/javascript">
	var lang = <xsl:value-of select="php:function('js_lang', 'please choose from list', 'please enter a value', 'please choose an entry')"/>;
</script>


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
