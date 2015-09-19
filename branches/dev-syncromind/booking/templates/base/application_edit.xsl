<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style type="text/css">
        #agegroup td {padding: 0 0.3em;}
    </style>
	<!--div id="content"-->
	<h3></h3>
	<xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->
	<form action="" method="POST" id='application_form' class= "pure-form pure-form-stacked" name="application_form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
                <xsl:value-of disable-output-escaping="yes" select="application/tabs"/>
                <div id="application_edit">
                    <fieldset>
                        <h1>
                            <xsl:value-of select="php:function('lang', 'Application')"/> (<xsl:value-of select="application/id"/>)
                        </h1>
                        
                        <div class="pure-g pure-form pure-form-aligned">
                            <div class="pure-u-1">
                                <div class="pure-control-group">
                                    <label>
                                        <h4><xsl:value-of select="php:function('lang', 'Active')"/></h4>
                                    </label>
                                    <select id="field_active" name="active">
                                        <option value="1">
                                            <xsl:if test="application/active=1">
                                                <xsl:attribute name="selected">checked</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="php:function('lang', 'Active')"/>
                                        </option>
                                        <option value="0">
                                            <xsl:if test="application/active=0">
                                                <xsl:attribute name="selected">checked</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="php:function('lang', 'Inactive')"/>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pure-g">
                            <div class="pure-u-1">
                                <div class="heading">
                                    <legend><h3>1. <xsl:value-of select="php:function('lang', 'Why?')" /></h3></legend>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_activity">
                                        <h4><xsl:value-of select="php:function('lang', 'Activity')" /></h4>
                                    </label>
                                    <select name="activity_id" id="field_activity" class="pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3">
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
                                    <label for="field_description">
                                        <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                                    </label>
                                    <textarea id="field_description" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="description"><xsl:value-of select="application/description"/></textarea>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_equipment">
                                        <h4><xsl:value-of select="php:function('lang', 'Equipment')" /></h4>
                                    </label>
                                    <textarea id="field_equipment" class="full-width pure-u-1 pure-u-sm-1-2 pure-u-lg-1-3" name="equipment"><xsl:value-of select="application/equipment"/></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pure-g">
                            <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                <div class="heading">
                                    <legend><h3>2. <xsl:value-of select="php:function('lang', 'Where?')" /></h3></legend>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_building_name">
                                        <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                                    </label>
                                    <input id="field_building_id" name="building_id" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="application/building_id"/></xsl:attribute>
                                    </input>
                                    <input id="field_building_name" name="building_name" type="text">
                                        <xsl:attribute name="value"><xsl:value-of select="application/building_name"/></xsl:attribute>
                                    </input>
                                    <div id="building_container"></div>
                                </div>
                                <div class="pure-control-group">
                                    <label>
                                        <h4><xsl:value-of select="php:function('lang', 'Resources')" /></h4>
                                    </label>
                                    <div id="resources_container" style="display:inline-block;"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
                                </div>
                            </div>
                            <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                <div class="heading">
                                    <legend><h3>3. <xsl:value-of select="php:function('lang', 'When?')" /></h3></legend>
                                </div>
                                <div id="dates-container">
                                    <xsl:for-each select="application/dates">
                                        <div class="date-container">
                                            <a href="javascript:void(0);" class="close-btn btnclose">-</a>
                                            <div class="pure-control-group">
                                                <label for="start_date">
                                                    <h4><xsl:value-of select="php:function('lang', 'From')" /></h4>
                                                </label>
                                                <input class="datetime pure-input-2-3" id="start_date" name="field_{position()}_from" type="text">
                                                <!--input id="field_{position()}_from" name="from_[]" type="text"-->
                                                    <xsl:attribute name="value"><xsl:value-of select="from_"/></xsl:attribute>
                                                </input>
                                            </div>
                                            <div class="pure-control-group">
                                                <label for="end_date">
                                                    <h4><xsl:value-of select="php:function('lang', 'To')" /></h4>
                                                </label>
                                                <!--input id="field_{position()}_to" name="to_[]" type="text"-->
                                                <input class="datetime pure-input-2-3" id="end_date" name="field_{position()}_to" type="text">
                                                    <xsl:attribute name="value"><xsl:value-of select="to_"/></xsl:attribute>
                                                </input>
                                            </div>
                                        </div>
                                    </xsl:for-each>
                                </div>
                                <div>
                                    <a href="javascript:;" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a>
                                </div>
                            </div>
                            <div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">
                                <div class="heading">
                                    <legend><h3>4. <xsl:value-of select="php:function('lang', 'Who?')" /></h3></legend>
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
                                                            <xsl:if test="../application/audience=id">
                                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                                            </xsl:if>
                                                        </input>
                                                        <xsl:value-of select="name"/>
                                                    </label>
                                                </li>
                                            </xsl:for-each>
                                        </ul>
                                    </div>
                                    <div class="pure-control pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                        <label for="field_from">
                                            <h4><xsl:value-of select="php:function('lang', 'Number of participants')" /></h4>
                                        </label>
                                        <table id="agegroup" class="pure-table pure-table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><xsl:value-of select="php:function('lang', 'Male')" /></th>
                                                    <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
                                            </thead>
                                            <tbody>
                                                <xsl:for-each select="agegroups">
                                                    <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                                    <tr>
                                                        <th><xsl:value-of select="name"/></th>
                                                        <td>
                                                            <input type="text" class="input50">
                                                                <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
                                                            </input>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="input50">
                                                                <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                                <xsl:attribute name="value"><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
                                                            </input>
                                                        </td>
                                                    </tr>
                                                </xsl:for-each>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pure-g">
                            <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                <div class="heading">
                                    <legend><h3><xsl:value-of select="php:function('lang', 'Contact information')" /></h3></legend>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_name">
                                        <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                                    </label>
                                    <input id="field_contact_name" name="contact_name" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_name"/></xsl:attribute>
                                    </input>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_email">
                                        <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                                    </label>
                                    <input id="field_contact_email" name="contact_email" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_email"/></xsl:attribute>
                                    </input>
                                </div>
                                <div class="pure-control-group">
                                    <label for="field_contact_phone">
                                        <h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4>
                                    </label>
                                    <input id="field_contact_phone" name="contact_phone" type="text" class="pure-u-1 pure-u-sm-1-2 pure-u-md-1">
                                        <xsl:attribute name="value"><xsl:value-of select="application/contact_phone"/></xsl:attribute>
                                    </input>
                                </div>
                            </div>
                            <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                <div class="heading">
                                    <legend><h3><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></h3></legend>
                                </div>
                                <div class="pure-control-group">
                                    <xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
                                </div>
                            </div>
                            <div class="pure-u-1 pure-u-md-1 pure-u-lg-1-3">                                
                                <div class="pure-g">
                                    <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1">
                                        <div class="heading">
                                            <legend><h3><xsl:value-of select="php:function('lang', 'Terms and conditions')" /></h3></legend>
                                        </div>
                                        <div id='regulation_documents'></div>
                                    </div>
                                    <div class="pure-control-group pure-u-1 pure-u-md-1-2 pure-u-lg-1"></div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="pure-control-group">
                <input type="submit" class="pure-button pure-button-primary">
                    <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
                </input>
                <a class="cancel">
                    <xsl:attribute name="href"><xsl:value-of select="application/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
                </a>
            </div>
	</form>
        <script type="text/javascript">
            $('#field_customer_identifier_type,#field_customer_ssn,#field_customer_organization_number').removeClass('pure-input-1').addClass('pure-u-1 pure-u-sm-1-2 pure-u-md-1');
            var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
            var initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
            var initialAcceptAllTerms = true;
            var initialSelection = <xsl:value-of select="application/resources_json"/>;
            console.log(initialDocumentSelection);
        </script>
	<!--/div-->
	<!--script type="text/javascript">
		YAHOO.booking.initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		YAHOO.booking.initialAcceptAllTerms = true;
		YAHOO.booking.initialSelection = <xsl:value-of select="application/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
	</script-->
</xsl:template>
