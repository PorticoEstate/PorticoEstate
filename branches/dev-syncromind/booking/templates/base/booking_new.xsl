<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->

	<!--dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'New Booking')"/></dt>
	</dl-->
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="booking/tabs"/>
                <div id="booking_new">
		<input type="hidden" name="application_id" value="{booking/application_id}"/>
                <div class="pure-control-group">
			<label><xsl:value-of select="php:function('lang', 'Activity')" /></label>
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
                <div class="pure-control-group">
                    
                        <label for="field_building"><xsl:value-of select="php:function('lang', 'Building')"/></label>
                            <!--div class="autocomplete"-->
                                <input id="field_building_id" name="building_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/building_id"/></xsl:attribute>
                                </input>
                                <input id="field_building_name" name="building_name" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/building_name"/></xsl:attribute>
                                </input>
                                <!--div id="building_container"/>
                            </div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Season')"/></label>
                            <!--div id="season_container"--><xsl:value-of select="php:function('lang', 'Select a building first')"/><!--/div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Resources')"/></label>
                            <!--div id="resources_container"--><xsl:value-of select="php:function('lang', 'Select a building first')"/><!--/div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Organization')"/></label>
                            <!--div class="autocomplete"-->
                                <input id="field_org_id" name="organization_id" type="hidden">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/organization_id"/></xsl:attribute>
                                </input>
                                <input id="field_org_name" name="organization_name" type="text">
                                    <xsl:attribute name="value"><xsl:value-of select="booking/organization_name"/></xsl:attribute>
                                </input>
                                <!--div id="org_container"/>
                            </div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Group')"/></label>
                            <!--div id="group_container"--><xsl:value-of select="php:function('lang', 'Select an organization first')"/><!--/div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'From')"/></label>
                            <input class="datetime" id="start_date" name="start_date" type="text">
                                    <xsl:attribute name="data-validation">
                                            <xsl:text>required</xsl:text>
                                    </xsl:attribute>
                            </input>
                            <!--div class="datetime-picker"-->
                            <!--input id="field_from" name="from_" type="text">
                                <xsl:attribute name="value"><xsl:value-of select="booking/from_"/></xsl:attribute>
                            </input-->
                            <!--/div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'To')"/></label>
                            
                            <input class="datetime" id="end_date" name="end_date" type="text"></input>
                            <!--div class="datetime-picker"-->
                            <!--input id="field_to" name="to_" type="text">
                                <xsl:attribute name="value"><xsl:value-of select="booking/to_"/></xsl:attribute>
                            </input-->
                            <!--/div-->
                </div>
                <div class="pure-control-group">
                        <label><xsl:value-of select="php:function('lang', 'Cost')" /></label>
                        <input id="field_cost" name="cost" type="text" value="{booking/cost}"/>
                </div>
                <div class="pure-control-group">
                                    <label for="field_repeat_until"><xsl:value-of select="php:function('lang', 'Recurring booking')" /></label>
                </div>
                <div class="pure-control-group">                   
                    <label></label>                
                                                    <input type="checkbox" name="outseason" id="outseason">
                                                            <xsl:if test="outseason='on'">
                                                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="php:function('lang', 'Out season')" />
                                            
                </div>
                <div class="pure-control-group">
                    <label></label>                                   
                                                    
                                                    <input type="checkbox" name="recurring" id="recurring">
                                                            <xsl:if test="recurring='on'">
                                                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="php:function('lang', 'Repeat until')" />
                                            
                </div>
                <div class="pure-control-group">
                    <label></label>            
                                            <!--input id="field_repeat_until" name="repeat_until" type="text">
                                                    <xsl:attribute name="value"><xsl:value-of select="repeat_until"/></xsl:attribute>
                                            </input-->
                                            <input class="datetime" id="start" name="start" type="text">
                                                    <xsl:attribute name="data-validation">
                                                            <xsl:text>required</xsl:text>
                                                    </xsl:attribute>
                                            </input>
                                    
                </div>
                <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Interval')" /></label>
                                            <xsl:value-of select="../field_interval" />
                                            <select id="field_interval" name="field_interval">
                                                    <option value="1">
                                                            <xsl:if test="interval=1">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', '1 week')" />
                                                    </option>
                                                    <option value="2">
                                                            <xsl:if test="interval=2">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', '2 weeks')" />
                                                    </option>
                                                    <option value="3">
                                                            <xsl:if test="interval=3">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', '3 weeks')" />
                                                    </option>
                                                    <option value="4">
                                                            <xsl:if test="interval=4">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:value-of select="php:function('lang', '4 weeks')" />
                                                    </option>
                                            </select>
                </div>
                <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
                                <dd>
                                        <ul>
                                                <xsl:for-each select="audience">
                                                        <li>
                                                                <input type="checkbox" name="audience[]">
                                                                        <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                                        <xsl:if test="../booking/audience=id">
                                                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                                                        </xsl:if>
                                                                </input>
                                                                <label><xsl:value-of select="name"/></label>
                                                        </li>
                                                </xsl:for-each>
                                        </ul>
                                </dd>
                </div>
                <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Number of participants')" /></label>
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
                                                                                <xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                                        </input>
                                                                </td>
                                                                <td>
                                                                        <input type="text">
                                                                                <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                                <xsl:attribute name="value"><xsl:value-of select="../booking/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                                        </input>
                                                                </td>
                                                        </tr>
                                                </xsl:for-each>
                                        </table>
                                </dd>
                </div>
                <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'send reminder for participants statistics')" /></label>
                                        <select name="reminder" id="field_reminder">
                                                <xsl:choose>
                                                        <xsl:when test="booking/reminder = 1">
                                                                <option value="1" selected="selected"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                                <option value="0"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                        </xsl:when>
                                                        <xsl:otherwise test="booking/reminder = 0">
                                                                <option value="1"><xsl:value-of select="php:function('lang', 'Send reminder')" /></option>
                                                                <option value="0" selected="selected"><xsl:value-of select="php:function('lang', 'Do not send reminder')" /></option>
                                                        </xsl:otherwise>
                                                </xsl:choose>
                                        </select>
                </div>
                </div>
            </div>
            <div class="form-buttons">
                <input type="submit" class="button pure-button pure-button-primary">
                                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
                            </input>
                <a class="cancel">
                    <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')"/>
                </a>
            </div>
    </form>
    <!--/div-->
    <!--script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.group_id = '<xsl:value-of select="booking/group_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script-->
</xsl:template>
