<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content">

    <dl class="form">
            <dt class="heading">
                    <xsl:choose>
                            <xsl:when test="new_form">
                                    <xsl:value-of select="php:function('lang', 'Add Resource')" />
                            </xsl:when>
                            <xsl:otherwise>
                                    <xsl:value-of select="php:function('lang', 'Edit Resource')" />
                            </xsl:otherwise>
                    </xsl:choose>
            </dt>
    </dl-->

    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
            <div id="resource" class="booking-container"> 
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Name')" />
                    </label>
                    <input name="name" id="field_name" type="text" value="{resource/name}">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>								
					</input>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Activity')" />
                    </label>
                    <select id="field_activity_id" name="activity_id">
                        <xsl:for-each select="activitydata/results">
                            <option value="{id}">
                                <xsl:if test="resource_id=id">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="name" />
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Sort order')" />
                    </label>
                    <input name="sort" id="field_sort" type="text" value="{resource/sort}"/>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Building')" />
                    </label>
                    <!--div class="autocomplete"-->
                    <xsl:if test="new_form or resource/permission/write/building_id">
                        <input id="field_building_id" name="building_id" type="hidden" value="{resource/building_id}"/>
                    </xsl:if>
                    <input id="field_building_name" name="building_name" type="text" value="{resource/building_name}">
                        <xsl:if test="not(new_form) and not(resource/permission/write/building_id)">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>					
                    </input>
                    <div id="building_container" class="custom-container"></div>
                    <!--/div-->
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Resource Type')" />
                    </label>
                    <select name='type' id='field_type'>
                        <option value=''><xsl:value-of select="php:function('lang', 'Select Type')" />...</option>
                        <xsl:for-each select="resource/types/*">
                            <option value="{local-name()}">
                                <xsl:if test="../../type = local-name()">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="php:function('lang', string(node()))"/>
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</label>
						<select id="field_active" name="active">
							<option value="1">
								<xsl:if test="resource/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</option>
							<option value="0">
								<xsl:if test="resource/active=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Inactive')"/>
							</option>
						</select>
					</div>
				</xsl:if>             
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'Description')" />
                    </label>
                    <div class="custom-container">
                        <textarea id="field_description" name="description" type="text"><xsl:value-of select="resource/description"/></textarea>
                    </div>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'organzations_ids')" />
                    </label>
                </div>
                <div class="pure-control-group">
                    <label>
                        <xsl:value-of select="php:function('lang', 'organzations_ids_description')" />
                    </label>
                    <input name="organizations_ids" id="field_organizations_ids" type="text" value="{resource/organizations_ids}"/>
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" id="button" class="pure-button pure-button-primary">
                <xsl:attribute name="value">
                    <xsl:choose>
                        <xsl:when test="new_form">
                            <xsl:value-of select="php:function('lang', 'Create')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="php:function('lang', 'Update')"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
            </input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="resource/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Cancel')" /></xsl:attribute>	
			</input>
        </div>
    </form>
    <!--/div-->	
    <script type="text/javascript">
        /*
            <![CDATA[
            YAHOO.util.Event.addListener(window, "load", function() {
            YAHOO.booking.rtfEditorHelper('field_description');
            });
            ]]>
        */
    </script>
</xsl:template>
