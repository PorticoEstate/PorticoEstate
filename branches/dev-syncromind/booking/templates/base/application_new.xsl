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

	<h3><xsl:value-of select="php:function('lang', 'New application')"/></h3-->
	<xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

	<form action="" method="POST" id='application_form' class="pure-form pure-form-aligned" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="application/tabs"/>
                <div id="application_add">
                    <xsl:if test="config/application_new_application">
                    <p style="width: 750px;">
                            <xsl:value-of select="config/application_new_application"/>
                    </p>		
                    </xsl:if>

                    <div class="pure-control-group">
                            <div class="heading">1. <xsl:value-of select="php:function('lang', 'Why?')" /></div>
                    </div>
                    <div class="pure-control-group">
                                    <label><xsl:value-of select="php:function('lang', 'Activity')" /></label>
                                    <xsl:if test="config/application_activities">
                                    <p>
                                            <xsl:value-of select="config/application_activities"/>
                                    </p>		
                                    </xsl:if>
                                    <select name="activity_id" id="field_activity">
                                            <option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
                                            <xsl:for-each select="activities">
                                                    <option>
                                                            <xsl:if test="../application/activity_id = id">
                                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                            </xsl:if>
                                                            <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                                            <xsl:value-of select="name"/>
                                                    </option>
                                            </xsl:for-each>
                                    </select>
                    </div>
                    <div class="pure-control-group">
                            <label><xsl:value-of select="php:function('lang', 'Information about the event')" /></label>
                            <xsl:if test="config/application_description">
                            <p>
                                    <xsl:value-of select="config/application_description"/>
                            </p>		
                            </xsl:if>
                            <textarea id="field_description" class="full-width" name="description"><xsl:value-of select="application/description"/></textarea>
                    </div>
                    <div class="pure-control-group">
                            <label></label>
                            <xsl:if test="config/application_equipment">
                                <p>
                                    <xsl:value-of select="config/application_equipment"/>
                                </p>
                            </xsl:if>
                            <textarea id="field_equipment" class="full-width" name="equipment"><xsl:value-of select="application/equipment"/></textarea>
                    </div>
                    <div class="pure-control-group">
                        <div class="heading">2. <xsl:value-of select="php:function('lang', 'How many?')" /></div>
                    </div>
                    <div class="pure-control-group">
                        <xsl:if test="config/application_howmany">
                        <p>
                                <xsl:value-of select="config/application_howmany"/>
                        </p>		
                        </xsl:if>
                        <label><xsl:value-of select="php:function('lang', 'Estimated number of participants')" /></label>
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
                                                                        <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                                </input>
                                                        </td>
                                                        <td>
                                                                <input type="text">
                                                                        <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                        <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                                </input>
                                                        </td>
                                                </tr>
                                        </xsl:for-each>
                                </table>
                        </dd>
                    </div>
                        <div class="clr"/>
                    <div class="pure-control-group">
                                <div class="heading">3. <xsl:value-of select="php:function('lang', 'Where?')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_where">
                                        <p>
                                                <xsl:value-of select="config/application_where"/>
                                        </p>		
                                </xsl:if>
                                <label><xsl:value-of select="php:function('lang', 'Building')" /></label>
                                        <!--div class="autocomplete"-->
                                                <input id="field_building_id" name="building_id" type="hidden">
                                                        <xsl:attribute name="value"><xsl:value-of select="application/building_id"/></xsl:attribute>
                                                </input>
                                                <input id="field_building_name" name="building_name" type="text">
                                                        <xsl:attribute name="value"><xsl:value-of select="application/building_name"/></xsl:attribute>
                                                </input>
                                                <div id="building_container"/>
                                        <!--/div-->
                    </div>
                    <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Resources')" /></label>
                                <xsl:value-of select="php:function('lang', 'Select a building first')" />
                    </div>
                    <div class="pure-control-group">
                                <div class="heading">4. <xsl:value-of select="php:function('lang', 'When?')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_when">
                                        <p>
                                                <xsl:value-of select="config/application_when"/>
                                        </p>		
                                </xsl:if>
                                <div id="dates-container">
                                        <xsl:for-each select="application/dates">
                                                <div class="date-container">
                                                        <a href="javascript:void(0);" class="close-btn btnclose">-</a>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="php:function('lang', 'From')" />
                                                                </label>
                                                                <input class="datetime" id="start_date" name="start_date" type="text">
                                                                        <xsl:if test="activity/start_date != ''">
                                                                                <xsl:attribute name="value">
                                                                                        <xsl:value-of select="php:function('date', $datetime_format, number(activity/start_date))"/>
                                                                                </xsl:attribute>
                                                                        </xsl:if>

                                                                        <xsl:attribute name="data-validation">
                                                                                <xsl:text>required</xsl:text>
                                                                        </xsl:attribute>
                                                                </input>
                                                        </div>
                                                        <div class="pure-control-group">
                                                                <label>
                                                                        <xsl:value-of select="php:function('lang', 'To')" />
                                                                </label>
                                                                <xsl:if test="activity/error_msg_array/end_date != ''">
                                                                        <xsl:variable name="error_msg">
                                                                                <xsl:value-of select="activity/error_msg_array/end_date" />
                                                                        </xsl:variable>
                                                                        <div class='input_error_msg'>
                                                                                <xsl:value-of select="php:function('lang', $error_msg)" />
                                                                        </div>
                                                                </xsl:if>
                                                                
                                                                <input class="datetime" id="end_date" name="end_date" type="text">
                                                                        <xsl:if test="activity/end_date != ''">
                                                                                <xsl:attribute name="value">
                                                                                        <xsl:value-of select="php:function('date', $datetime_format, number(activity/end_date))"/>
                                                                                </xsl:attribute>
                                                                        </xsl:if>
                                                                </input>
                                                        </div>
                                                        <!--div class="help_text">
                                                                <xsl:value-of select="php:function('lang','Give end date to activity')" />
                                                        </div-->
                                                </div>
                                        </xsl:for-each>
                                </div>
                                <dt><a href="javascript:;" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a></dt>
                    </div>
                    <div class="pure-control-group">
                                <div class="heading">5. <xsl:value-of select="php:function('lang', 'Who?')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_who">
                                        <p>
                                                <xsl:value-of select="config/application_who"/>
                                        </p>		
                                </xsl:if>
                                <label><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
                                        <div id="audience_container">&nbsp;</div>
                    </div>
                        <div class="clr"/>
                    <div class="pure-control-group">
                                <div class="heading"><br />6. <xsl:value-of select="php:function('lang', 'Contact information')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_contact_information">
                                        <p>
                                                <xsl:value-of select="config/application_contact_information"/>
                                        </p>		
                                </xsl:if>
                                <label><xsl:value-of select="php:function('lang', 'Name')" /></label>
                                        <input id="field_contact_name" name="contact_name" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="application/contact_name"/></xsl:attribute>
                                        </input>
                    </div>
                    <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'E-mail address')" /></label>
                                        <input id="field_contact_email" name="contact_email" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="application/contact_email"/></xsl:attribute>
                                        </input>
                    </div>
                    <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Confirm e-mail address')" /></label>
                                        <input id="field_contact_email2" name="contact_email2" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="application/contact_email2"/></xsl:attribute>
                                        </input>
                    </div>
                    <div class="pure-control-group">
                                <label><xsl:value-of select="php:function('lang', 'Phone')" /></label>
                                        <input id="field_contact_phone" name="contact_phone" type="text">
                                                <xsl:attribute name="value"><xsl:value-of select="application/contact_phone"/></xsl:attribute>
                                        </input>
                    </div>
                    <div class="pure-control-group">
                                <div class="heading">7. <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_responsible_applicant">
                                        <p>
                                                <xsl:value-of select="config/application_responsible_applicant"/>
                                        </p>		
                                </xsl:if>
                                <!--xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/-->
                                <br />
                                <xsl:if test="config/application_invoice_information">
                                        <p>
                                                <xsl:value-of select="config/application_invoice_information"/>
                                        </p>		
                                </xsl:if>
                    </div>
                    <div class="pure-control-group">
                                <div class="heading"><br />8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></div>
                    </div>
                    <div class="pure-control-group">
                                <xsl:if test="config/application_terms">
                                        <p>
                                                <xsl:value-of select="config/application_terms"/>
                                        </p>		
                                </xsl:if>
                                <br />
                                <div id='regulation_documents'>&nbsp;</div>
                                <br />
                                <xsl:if test="config/application_terms2">
                                        <p>
                                                <xsl:value-of select="config/application_terms2"/>
                                        </p>		
                                </xsl:if>
                    </div>
                </div>
            </div>
            <div class="form-buttons">
                    <input type="submit" class="pure-button pure-button-primary">
                            <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Send')"/></xsl:attribute>
                    </input>
                    <a class="cancel">
                            <xsl:attribute name="href"><xsl:value-of select="application/cancel_link"/></xsl:attribute>
                            <xsl:value-of select="php:function('lang', 'Cancel')" />
                    </a>
                    <p style="width: 750px; margin-top: 10px;">Trykker du <strong>SEND</strong>-knappen får du opp en rød melding øverst om noen opplysninger mangler, er alt OK kommer det opp en grønn melding. Det blir sendt en bekreftelse til din e-post, og en lenke hvor du kan gå inn og se status og legge til ekstra opplysninger i saken.<br /><br />
                            Trykker du <strong>Avbryt</strong> blir søknaden ikke sendt eller lagret, og du går tilbake til kalenderen.</p>
            </div>
	</form>
	<!--/div-->
	<script type="text/javascript">
		YAHOO.booking.initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		YAHOO.booking.initialAcceptAllTerms = false;
		YAHOO.booking.initialSelection = <xsl:value-of select="application/resources_json"/>;
		YAHOO.booking.initialAudience = <xsl:value-of select="application/audience_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
	</script>
</xsl:template>
