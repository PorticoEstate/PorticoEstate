<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        #agegroup td {padding: 0 0.3em;}
        #field_customer_ssn {display:none;}
        #field_customer_organization_number {display:none;}        
    </style>
    <!--div id="content">
        <h3><xsl:value-of select="php:function('lang', 'New application')"/></h3-->
        <xsl:call-template name="msgbox"/>
        <!--xsl:call-template name="yui_booking_i18n"/-->
        <form action="" method="POST" id='application_form' class="pure-form pure-form-stacked" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="application/tabs"/>
                <div id="application_add">
                    <xsl:if test="config/application_new_application">
                        <p style="width: 750px;">
                            <xsl:value-of select="config/application_new_application"/>
                        </p>
                    </xsl:if>
                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-10-24 pure-u-lg-14-24">
                            <fieldset>
                                <div class="heading"><legend><h3>1. <xsl:value-of select="php:function('lang', 'Why?')" /></h3></legend></div>
                                <div class="pure-control-group">
                                    <label for="field_activity"><h4><xsl:value-of select="php:function('lang', 'Activity')" /></h4></label>
                                    <xsl:if test="config/application_activities">
                                        <p>
                                            <xsl:value-of select="config/application_activities"/>
                                        </p>
                                    </xsl:if>
                                    <select name="activity_id" id="field_activity" class="pure-input-1">
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
                                    <label for="field_description"><h4><xsl:value-of select="php:function('lang', 'Information about the event')" /></h4></label>
                                    <xsl:if test="config/application_description">
                                        <p>
                                            <xsl:value-of select="config/application_description"/>
                                        </p>
                                    </xsl:if>
                                    <textarea rows="6" id="field_description" class="full-width pure-input-1" name="description"><xsl:value-of select="application/description"/></textarea>
                                </div>
                            </fieldset>
                        </div>
                        <div class="pure-u-1 pure-u-md-14-24 pure-u-lg-10-24">
                            <fieldset>
                                <div class="heading"><legend><h3>2. <xsl:value-of select="php:function('lang', 'How many?')" /></h3></legend></div>
                                <xsl:if test="config/application_howmany">
                                    <p>
                                        <xsl:value-of select="config/application_howmany"/>
                                    </p>
                                </xsl:if>
                                <label><h4><xsl:value-of select="php:function('lang', 'Estimated number of participants')" /></h4></label>
                                <table id="agegroup" class="pure-table pure-table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <xsl:value-of select="php:function('lang', 'Male')" />
                                            </th>
                                            <th>
                                                <xsl:value-of select="php:function('lang', 'Female')" />
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <xsl:for-each select="agegroups">
                                            <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                            <tr>
                                                <th><xsl:value-of select="name"/></th>
                                                <td>
                                                    <input class="input50" type="text">
                                                        <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                        <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                    </input>
                                                </td>
                                                <td>
                                                    <input class="input50" type="text">
                                                        <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                        <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                    </input>
                                                </td>
                                            </tr>
                                        </xsl:for-each>
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>

                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="heading"><legend><h3>3. <xsl:value-of select="php:function('lang', 'Where?')" /></h3></legend></div>
                                <xsl:if test="config/application_where">
                                    <p>
                                        <xsl:value-of select="config/application_where"/>
                                    </p>
                                </xsl:if>
                                <div class="pure-control-group">
                                    <label for="field_building_id">
                                        <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                                    </label>
                                    <!--div class="autocomplete"-->
                                        <input id="field_building_id" name="building_id" type="hidden">
                                            <xsl:attribute name="value"><xsl:value-of select="application/building_id"/></xsl:attribute>
                                        </input>
                                        <input id="field_building_name" class="pure-input-1" name="building_name" type="text">
                                            <xsl:attribute name="value"><xsl:value-of select="application/building_name"/></xsl:attribute>
                                        </input>
                                        <div id="building_container"></div>
                                    <!--/div-->
                                </div>
                                <div class="pure-control-group">
                                    <label><h4><xsl:value-of select="php:function('lang', 'Resources')" /></h4></label>
                                    <div id="resources_container" style="display:inline-block;"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="heading"><legend><h3>4. <xsl:value-of select="php:function('lang', 'When?')" /></h3></legend></div>
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
                                                <label for="start_date">
                                                    <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                                                </label>
                                                <input class="datetime pure-input-2-3" id="start_date" name="start_date" type="text">
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
                                                <label for="end_date">
                                                    <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                                                </label>
                                                <xsl:if test="activity/error_msg_array/end_date != ''">
                                                    <xsl:variable name="error_msg">
                                                        <xsl:value-of select="activity/error_msg_array/end_date" />
                                                    </xsl:variable>
                                                    <div class='input_error_msg'>
                                                        <xsl:value-of select="php:function('lang', $error_msg)" />
                                                    </div>
                                                </xsl:if>
                                                <input class="datetime pure-input-2-3" id="end_date" name="end_date" type="text">
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
                                <div class="pure-control-group"><a href="javascript:void(0);" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a></div>
                            </fieldset>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="heading"><legend><h3>5. <xsl:value-of select="php:function('lang', 'Who?')" /></h3></legend></div>
                                <xsl:if test="config/application_who">
                                    <p>
                                        <xsl:value-of select="config/application_who"/>
                                    </p>
                                </xsl:if>
                                <label><h4><xsl:value-of select="php:function('lang', 'Target audience')" /></h4></label>
                                <div id="audience_container">&nbsp;</div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="pure-g">
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="heading"><legend><h3>6. <xsl:value-of select="php:function('lang', 'Contact information')" /></h3></legend></div>
                                <div class="pure-control-group">
                                    <xsl:if test="config/application_contact_information">
                                        <p>
                                           <xsl:value-of select="config/application_contact_information"/>
                                        </p>
                                    </xsl:if>
                                    <label for="field_contact_name"><h4><xsl:value-of select="php:function('lang', 'Name')" /></h4></label>
                                    <input id="field_contact_name" class="pure-input-1" name="contact_name" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_name"/></xsl:attribute>
                                    </input>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_email"><h4><xsl:value-of select="php:function('lang', 'E-mail address')" /></h4></label>
                                    <input id="field_contact_email" class="pure-input-1" name="contact_email" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_email"/></xsl:attribute>
                                    </input>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_email2"><h4><xsl:value-of select="php:function('lang', 'Confirm e-mail address')" /></h4></label>
                                    <input id="field_contact_email2" class="pure-input-1" name="contact_email2" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_email2"/></xsl:attribute>
                                    </input>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_phone"><h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4></label>
                                    <input id="field_contact_phone" class="pure-input-1" name="contact_phone" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_phone"/></xsl:attribute>
                                    </input>
                                </div>
                            </fieldset>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="pure-control-group">
                                    <div class="heading"><legend><h3>7.x <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></h3></legend></div>
                                    <p>Ut fra reglementet i pkt. 8 finner du kriterier for fakturering. Når du som privatperson skal låne noe som det ikke skal faktureres for oppgir du kun fødselsdato, men skal du leie noe som koster noe, da må vi ha hele personnummeret. Alle lag og organisasjoner skal oppgi organisasjonsnr.</p>
                                    <xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
                                    <p>
                                        <xsl:value-of select="php:function('lang', 'In order to send the invoice we need information about either customer organization number or norwegian social security number')" />
                                    </p>
                                
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
                            </fieldset>
                        </div>
                        <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                            <fieldset>
                                <div class="heading"><legend><h3>8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></h3></legend></div>
                                <p>Alle som leier lokaler hos Bergen kommune må bekrefte at de har lest betingelsene, dette gjelder som regel brannforskrifter og husreglement.</p>
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
                                    <p><xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" /></p>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <!--div class="pure-control-group">
                        <label></label>
                        <xsl:if test="config/application_equipment">
                            <p>
                                <xsl:value-of select="config/application_equipment"/>
                            </p>
                        </xsl:if>
                        <textarea id="field_equipment" class="full-width" name="equipment"><xsl:value-of select="application/equipment"/></textarea>
                    </div-->
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
        var initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
        var initialAcceptAllTerms = false;
        var initialSelection = <xsl:value-of select="application/resources_json"/>;
        var initialAudience = <xsl:value-of select="application/audience_json"/>;
        var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
    </script>
</xsl:template>
