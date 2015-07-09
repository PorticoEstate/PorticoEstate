<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        
        .date-container {
            width: 31%;
        }
        
        .date-container .close-btn {
            background: transparent url("phpgwapi/js/yahoo/assets/skins/sam/sprite.png") no-repeat scroll 0 -300px;
            border: medium none;
            color: white;
            cursor: pointer;
            display: block;
            //float: right;
            height: 15px;
            text-decoration: none;
            width: 25px;
            margin: 4px 0 0 296px;
        }
        
    </style>
    <!--div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'New event')"/></dt>
	</dl-->
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="event/tabs"/>
                <div id="event_new">
                    <input type="hidden" name="application_id" value="{event/application_id}"/>
                    <div class="pure-control-group">
                            <dt class="heading"><xsl:value-of select="php:function('lang', 'Why')" /></dt>
                    </div>                
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Activity')" /></label>
                                            <select name="activity_id" id="field_activity">
                                                    <option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
                                                    <xsl:for-each select="activities">
                                                            <option>
                                                                    <xsl:if test="../event/activity_id = id">
                                                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                                                    </xsl:if>
                                                                    <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                                    <xsl:value-of select="name"/>
                                                            </option>
                                                    </xsl:for-each>
                                            </select>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Description')" /></label>
                                    <textarea id="field_description" class="full-width" name="description"><xsl:value-of select="event/description"/></textarea>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Event type')"/></label>
                                        <select id="field_public" name="is_public">
                                              <option value="1">
                                                    <xsl:if test="event/is_public=1">
                                                      <xsl:attribute name="selected">checked</xsl:attribute>
                                                    </xsl:if>
                                                      <xsl:value-of select="php:function('lang', 'Public event')"/>
                                              </option>
                                              <option value="0">
                                                    <xsl:if test="event/is_public=0">
                                                      <xsl:attribute name="selected">checked</xsl:attribute>
                                                    </xsl:if>
                                                      <xsl:value-of select="php:function('lang', 'Private event')"/>
                                              </option>
                                        </select>
                    </div>
                    <div class="pure-control-group">
                                    <dt class="heading"><xsl:value-of select="php:function('lang', 'Where')" /></dt>
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Building')" /></label>
                            <!--div class="autocomplete"-->
                                <input id="field_building_id" name="building_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="event/building_id"/></xsl:attribute>
                                </input>
                                <input id="field_building_name" name="building_name" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="event/building_name"/></xsl:attribute>
                                </input>
                                <!--div id="building_container"/>
                            </div-->
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Resources')" /></label>
                            <!--div id="resources_container"--><xsl:value-of select="php:function('lang', 'Select a building first')" /><!--/div-->
                    </div>
                    <div class="pure-control-group">
                                    <div class="heading"><xsl:value-of select="php:function('lang', 'When?')" /></div>
                    </div>
                    <div class="pure-control-group">
                                    <div id="dates-container">
                                            <xsl:for-each select="event/dates">
                                                    <div class="date-container">
                                                            <a href="#" class="close-btn">-</a>
                                                        <div class="pure-control-group">
                                                            <label>
                                                                    <xsl:value-of select="php:function('lang', 'From')" />
                                                            </label>
                                                            <input class="datetime" id="start_date" name="start_date" type="text">
                                                            </input>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="php:function('lang', 'To')" />
                                                                </label>                                                                
                                                                <input class="datetime" id="end_date" name="end_date" type="text">
                                                                </input>
                                                        </div>
                                                    </div>
                                            </xsl:for-each>
                                    </div>
                    </div>
                    <div class="pure-control-group">
                        <label></label>
                                    <a href="#" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a>
                    </div>
                            <dl class="form-col">
                                    <dt class="heading"><xsl:value-of select="php:function('lang', 'Who')" /></dt>
                                    <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Target audience')" /></label></dt>
                                    <dd>
                                            <ul>
                                                    <xsl:for-each select="audience">
                                                            <li>
                                                                    <input type="checkbox" name="audience[]">
                                                                            <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                                            <xsl:if test="../event/audience=id">
                                                                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                                                            </xsl:if>
                                                                    </input>
                                                                    <label><xsl:value-of select="name"/></label>
                                                            </li>
                                                    </xsl:for-each>
                                            </ul>
                                    </dd>
                                    <dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label></dt>
                                    <dd>
                                            <table id="agegroup">
                                                    <tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
                                                        <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
                                                    <xsl:for-each select="agegroups">
                                                            <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                                            <tr>
                                                                    <th><xsl:value-of select="name"/></th>
                                                                    <td>
                                                                            <input type="text">
                                                                                    <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                                    <xsl:attribute name="value"><xsl:value-of select="../event/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                                            </input>
                                                                    </td>
                                                                    <td>
                                                                            <input type="text">
                                                                                    <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                                    <xsl:attribute name="value"><xsl:value-of select="../event/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                                            </input>
                                                                    </td>
                                                            </tr>
                                                    </xsl:for-each>
                                            </table>
                                    </dd>
                            </dl>
                            <div class="clr"/>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Cost')" /></label>
                            <input id="field_cost" name="cost" type="text">
                                <xsl:attribute name="value"><xsl:value-of select="event/cost"/></xsl:attribute>
                            </input>
                    </div>
                    <div class="pure-control-group">
                                    <dt class="heading"><xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" /></dt>
                    </div>
                    <div class="pure-control-group">
                        <label></label>
                                            <select name="reminder" id="field_reminder">
                                                    <xsl:choose>
                                                            <xsl:when test="event/reminder = 1">
                                                                    <option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                            </xsl:when>
                                                            <xsl:otherwise test="event/reminder = 0">
                                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                                    <option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                            </xsl:otherwise>
                                                    </xsl:choose>
                                            </select>
                    </div>
                            <div class="clr"/>
                    <div class="pure-control-group">
                        <dt class="heading"><xsl:value-of select="php:function('lang', 'Get all contact and invoice information from organization')" /></dt> 
                    </div>
                    <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Organization')" /></label>
                            <!--div class="autocomplete"-->
                                <input id="field_org_id" name="organization_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="event/customer_organization_id"/></xsl:attribute>
                                </input>
                                <input id="field_org_name" name="organization_name" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="event/customer_organization_name"/></xsl:attribute>
                                </input>
                                <!--div id="org_container"/>
                            </div-->
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'Or')" />
                        </label>
                    </div>
                    <div class="pure-control-group">
                            <label><xsl:value-of select="php:function('lang', 'Organization_number')" /></label>
                                    <input id="field_org_id2" name="org_id2" type="text">
                                            <xsl:attribute name="value"><xsl:value-of select="event/org_id2"/></xsl:attribute>
                                    </input>
                    </div>
                    <div class="pure-control-group">
                                    <dt class="heading"><xsl:value-of select="php:function('lang', 'Contact information')" /></dt>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Name')" /></label>
                                            <input id="field_contact_name" name="contact_name" type="text">
                                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_name"/></xsl:attribute>
                                            </input>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Email')" /></label>
                                            <input id="field_contact_mail" name="contact_email" type="text">
                                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_email"/></xsl:attribute>
                                            </input>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Phone')" /></label>
                                            <input id="field_contact_phone" name="contact_phone" type="text">
                                                    <xsl:attribute name="value"><xsl:value-of select="event/contact_phone"/></xsl:attribute>
                                            </input>
                    </div>
                    <div class="pure-control-group">
                            <dt class="heading"><xsl:value-of select="php:function('lang', 'Invoice information')" /></dt>
                    </div>
                    <div class="pure-control-group">
                            <xsl:copy-of select="phpgw:booking_customer_identifier(event, '')"/>
                    </div>
                    <div class="pure-control-group">
                            <label><xsl:value-of select="php:function('lang', 'Internal Customer')"/></label>
                            <xsl:copy-of select="phpgw:option_checkbox(event/customer_internal, 'customer_internal')"/>
                    </div>
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" class="button pure-button pure-button-primary">
                                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
                            </input>
                <a class="cancel">
                    <xsl:attribute name="href"><xsl:value-of select="event/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                </a>
            </div>
    </form>
    <!--/div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="event/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type')"/>;
    </script-->
</xsl:template>
