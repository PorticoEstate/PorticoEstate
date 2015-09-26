<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        #agegroup td {padding: 0 0.3em;}
    </style>
    <!--div id="content"-->

    <!--ul class="pathway">
        <li><xsl:value-of select="php:function('lang', 'Bookings')" /></li>
        <li>#<xsl:value-of select="booking/id"/></li>
    </ul-->
    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id='form' class="pure-form pure-form-stacked" name="form">
        <input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
            <div id="booking_edit">
                <fieldset>                
                    <h1>
                        #<xsl:value-of select="booking/id"/>
                    </h1>
                    
                    <div class="pure-g">
                        <div class="pure-u-1">
                            <div class="pure-control-group">
                                <label>
                                    <h4><xsl:value-of select="php:function('lang', 'Application')"/></h4>
                                </label>
                                <xsl:if test="booking/application_id != ''">
                                    <a href="{booking/application_link}">#<xsl:value-of select="booking/application_id"/></a>
                                </xsl:if>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_active">
                                    <h4><xsl:value-of select="php:function('lang', 'Active')"/></h4>
                                </label>
                                <select id="field_active" name="active">
                                    <option value="1">
                                        <xsl:if test="booking/active=1">
                                            <xsl:attribute name="selected">checked</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="php:function('lang', 'Active')"/>
                                    </option>
                                    <option value="0">
                                        <xsl:if test="booking/active=0">
                                            <xsl:attribute name="selected">checked</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                                    </option>
                                </select>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_activity">
                                    <h4><xsl:value-of select="php:function('lang', 'Activity')" /></h4>
                                </label>
                                <select name="activity_id" id="field_activity">
                                    <option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
                                    <xsl:for-each select="activities">
                                        <option>
                                            <xsl:if test="../booking/activity_id = id">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                            <xsl:value-of select="name"/>
                                        </option>
                                    </xsl:for-each>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend>&nbsp;</legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_building_name">
                                    <h4><xsl:value-of select="php:function('lang', 'Building')"/></h4>
                                </label>
                                <!--div class="autocomplete"-->
                                <input id="field_building_id" name="building_id" type="hidden" value="{booking/building_id}"/>
                                <input id="field_building_name" name="building_name" type="text" value="{booking/building_name}"/>
                                <div id="building_container"></div>
                                <!--/div-->
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <h4><xsl:value-of select="php:function('lang', 'Season')"/></h4>
                                </label>
                                <div id="season_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <h4><xsl:value-of select="php:function('lang', 'Resources')"/></h4>
                                </label>
                                <div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
                            </div>
                        </div>                        

                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <div class="heading">
                                <legend>&nbsp;</legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_org_name">
                                    <h4><xsl:value-of select="php:function('lang', 'Organization')"/></h4>
                                </label>
                                <!--div class="autocomplete"-->
                                <input id="field_org_id" name="organization_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/organization_id"/></xsl:attribute>
                                </input>
                                <input id="field_org_name" name="organization_name" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/organization_name"/></xsl:attribute>
                                </input>
                                <div id="org_container"></div>
                                <!--/div-->
                            </div>
                            <div class="pure-control-group">
                                <label>
                                    <h4><xsl:value-of select="php:function('lang', 'Group')"/></h4>
                                </label>
                                    <div id="group_container"><xsl:value-of select="php:function('lang', 'Select a building first')"/></div>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_from">
                                    <h4><xsl:value-of select="php:function('lang', 'From')"/></h4>
                                </label>
                                <input class="datetime" id="field_from" name="from_" type="text" style="display:inline-block;">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
                                </input>
                                <!--div class="datetime-picker">
                                <input id="field_from" name="from_" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
                                </input>
                                </div-->
                            </div>
                            <div class="pure-control-group">
                                <label for="field_to">
                                    <h4><xsl:value-of select="php:function('lang', 'To')"/></h4>
                                </label>
                                <input class="datetime" id="field_to" name="to_" type="text" style="display:inline-block;">
                                        <xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
                                </input>
                                <!--div class="datetime-picker">
                                <input id="field_to" name="to_" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
                                </input>
                                </div-->
                            </div>
                            <div class="pure-control-group">
                                <label for="field_cost">
                                    <h4><xsl:value-of select="php:function('lang', 'Cost')" /></h4>
                                </label>
                                <input id="field_cost" name="cost" type="text" value="{booking/cost}"/>
                            </div>
                        </div>

                        <div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
                            <div class="heading">
                                <legend></legend>
                            </div>
                            <div class="pure-g">
                                <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                    <label for="field_from">
                                        <h4><xsl:value-of select="php:function('lang', 'Target audience')" /></h4>
                                    </label>
                                    <ul style="list-style:none;padding-left:10px;">
                                        <xsl:for-each select="audience">
                                            <li>
                                                <label>
                                                    <input type="checkbox" name="audience[]">
                                                            <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                            <xsl:if test="../booking/audience=id">
                                                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="name"/>
                                                </label>
                                            </li>
                                        </xsl:for-each>
                                    </ul>
                                </div>
                                <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                    <div class="pure-control-group">
                                        <label for="field_from">
                                            <h4><xsl:value-of select="php:function('lang', 'Number of participants')" /></h4>
                                        </label>
                                        <table id="agegroup" class="pure-table pure-table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><xsl:value-of select="php:function('lang', 'Male')" /></th>
                                                    <th><xsl:value-of select="php:function('lang', 'Female')" /></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <xsl:for-each select="agegroups">
                                                    <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                                    <tr>
                                                        <th><xsl:value-of select="name"/></th>
                                                        <td>
                                                            <input type="text" class="input50">
                                                                <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                <xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                            </input>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input50">
                                                                <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                <xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                            </input>
                                                        </td>
                                                    </tr>
                                                </xsl:for-each>
                                            </tbody>
                                        </table>
                                    </div>                                    
                                    <div class="pure-control-group">
                                        <label for="sms_total">
                                            <h4><xsl:value-of select="php:function('lang', 'SMS total')" /></h4>
                                        </label>
                                        <input type="text" name="sms_total" id="sms_total">
                                            <xsl:attribute name="value"><xsl:value-of select="booking/sms_total"/></xsl:attribute>
                                        </input>
                                    </div>                                    
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" /></h4>
                                        </label>
                                        <select name="reminder" id="field_reminder">
                                            <xsl:choose>
                                                <xsl:when test="booking/reminder = 1">
                                                    <option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                                </xsl:when>
                                                <xsl:when test="booking/reminder = 0">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                                </xsl:when>
                                                <xsl:when test="booking/reminder = 2">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2" selected="selected"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                                </xsl:when>
                                                <xsl:when test="booking/reminder = 3">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3" selected="selected"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                                </xsl:when>
                                            </xsl:choose>
                                            <!--xsl:if test="booking/reminder = 1">
                                                    <option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                            </xsl:if>
                                            <xsl:if test="booking/reminder = 0">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                            </xsl:if>
                                            <xsl:if test="booking/reminder = 2">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2" selected="selected"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                            </xsl:if>
                                            <xsl:if test="booking/reminder = 3">
                                                    <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                    <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                    <option value="2"><xsl:value-of select="php:function('lang', 'User has responded to the reminder')" /></option>
                                                    <option value="3" selected="selected"><xsl:value-of select="php:function('lang', 'Reminder sent. Not responded to')" /></option>
                                            </xsl:if-->
                                        </select>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pure-g">
                        <div class="pure-u-1">
                            <div class="heading">
                                <legend></legend>
                            </div>
                            <div class="pure-control-group">
                                <label for="field_mail">
                                    <h4><xsl:value-of select="php:function('lang', 'Inform contact persons')" /></h4>
                                </label>
                                <xsl:value-of select="php:function('lang', 'Text written in the text area below will be sent as an email to all registered contact persons.')" />
                                <textarea id="field_mail" name="mail" class="full-width"></textarea>
                            </div>
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
                        <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
                        </input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')"/>
            </a>
        </div>
    </form>
    <!--/div-->
    <script>
        var season_id = '<xsl:value-of select="booking/season_id"/>';
        var group_id = '<xsl:value-of select="booking/group_id"/>';
        var initialSelection = <xsl:value-of select="booking/resources_json"/>;
	var lang = <xsl:value-of select="php:function('js_lang','Name', 'Resource Type')"/>;
    </script>
    <!--script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.group_id = '<xsl:value-of select="booking/group_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
	var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script-->
</xsl:template>
