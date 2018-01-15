<func:function name="phpgw:conditional">
    <xsl:param name="test"/>
    <xsl:param name="true"/>
    <xsl:param name="false"/>

    <func:result>
        <xsl:choose>
            <xsl:when test="$test">
                <xsl:value-of select="$true"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$false"/>
            </xsl:otherwise>
        </xsl:choose>
    </func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:call-template name="jquery_phpgw_i18n"/>
    <div class="container-fluid main-container application-background">
        <div class="container"> 
                
            <div class="row">
                <div class="col-lg-12">
                    
                    <ul class="pathway">
                  
                        <li>
                            <a>
                                <xsl:attribute name="href">
                                    <xsl:value-of select="application/applications_link"/>
                                </xsl:attribute>
                                <xsl:value-of select="php:function('lang', 'Applications')" />
                            </a>
                        </li>
                        <li>#<xsl:value-of select="application/id"/></li>
                        <li>
                            <xsl:if test="frontend and application/status='ACCEPTED'">
                                <form method="POST">
                                    <input type="hidden" name="print" value="ACCEPTED"/>
                                    <input type="submit" value="{php:function('lang', 'Print as PDF')}" />
                                </form>
                            </xsl:if>
                        </li>
                    </ul>
                    <xsl:call-template name="msgbox"/>
                </div>

		
                <div class="col-lg-12">
                    <xsl:if test="not(frontend)">
                        <div style="border: 3px solid red; padding: 3px 4px 3px 4px">
                            <xsl:choose>
                                <xsl:when test="not(application/case_officer)">
                                    <xsl:value-of select="php:function('lang', 'In order to work with this application, you must first')"/>
                                    <xsl:text> </xsl:text>
                                    <a href="#assign">
                                        <xsl:value-of select="php:function('lang', 'assign yourself')"/>
                                    </a>
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
                                </xsl:when>
                                <xsl:when test="application/case_officer and not(application/case_officer/is_current_user)">
                                    <xsl:value-of select="php:function('lang', 'The user currently assigned as the responsible case officer for this application is')"/>'<xsl:text> </xsl:text>
                                    <xsl:value-of select="application/case_officer/name"/>'.
                                    <br/>
                                    <xsl:value-of select="php:function('lang', 'In order to work with this application, you must therefore first')"/>
                                    <xsl:text> </xsl:text>
                                    <a href="#assign">
                                        <xsl:value-of select="php:function('lang', 'assign yourself')"/>
                                    </a>
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:attribute name="style">display:none</xsl:attribute>
                                </xsl:otherwise>
                            </xsl:choose>
                        </div>
                    </xsl:if>

                    <xsl:if test="not(frontend)">
                        <dl class="proplist-col">
                            <dt>
                                <xsl:value-of select="php:function('lang', 'Status')" />
                            </dt>
                            <dd>
                                <xsl:value-of select="php:function('lang', string(application/status))"/>
                            </dd>
                        </dl>
                        <dl class="proplist-col">
                            <dt>
                                <xsl:value-of select="php:function('lang', 'Created')" />
                            </dt>
                            <dd>
                                <xsl:value-of select="php:function('pretty_timestamp', application/created)"/>
                            </dd>
                        </dl>
                        <dl class="proplist-col">
                            <dt>
                                <xsl:value-of select="php:function('lang', 'Modified')" />
                            </dt>
                            <dd>
                                <xsl:value-of select="php:function('pretty_timestamp', application/modified)"/>
                            </dd>
                        </dl>
                    </xsl:if>
                    <xsl:if test="frontend">
                        <dl class="proplist">
                            <span style="font-size: 110%; font-weight: bold;">Din søknad har status <xsl:value-of select="php:function('lang', string(application/status))"/></span>
                            <span class="text">, opprettet <xsl:value-of select="php:function('pretty_timestamp', application/created)"/>, sist endret <xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></span>
                            <span class="text">
                                <br />Melding fra saksbehandler ligger under historikk, deretter vises kopi av din søknad.<br /> Skal du gi en melding til saksbehandler skriver du denne inn i feltet under "Legg til en kommentar"</span>
                        </dl>
                    </xsl:if>

                    <div class="form-group">
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Add a comment')" />
                        </div>
                            
                        <form method="POST">
                            <textarea name="comment" class="form-control" rows="6"></textarea>
                            <br/>
                            <input type="submit" class="btn btn-default" value="{php:function('lang', 'Add comment')}" />
                        </form>
                            
                    </div>
                    
                </div>
           
                    <!-- Steg 1 -->
                    
                    <div class="col-lg-12 application-group bg-light">
                  <div class="col-lg-12">
                        <div class="heading"><xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" /></div>
                        <xsl:for-each select="application/comments[author]">
                          
                            <xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
                           
                            <xsl:choose>
                                <xsl:when test='contains(comment,"bookingfrontend.uidocument_building.download")'>
                                     
                                    <xsl:value-of select="comment" disable-output-escaping="yes"/>
                                      
                                </xsl:when>
                                <xsl:otherwise>
                                      
                                    <div style="width: 80%;">
                                        <xsl:value-of select="comment" disable-output-escaping="yes"/>
                                    </div>
                                           
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                    </div> 
                    
                    
                    <!-- Steg 1.1 -->
                    
                     
                        <div class="heading no-border">
                            1.1 <xsl:value-of select="php:function('lang', 'attachments')" />
                        </div>
                        <div class="col-lg-12">
                        <div id="attachments_container"/>
              
                        <br/>
                        <form method="POST" enctype='multipart/form-data' id='file_form'>
                            <input name="name" id='field_name' type='file' >
                                <xsl:attribute name='title'>
                                    <xsl:value-of select="document/name"/>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation">
                                    <xsl:text>mime size</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-allowing">
                                    <xsl:text>jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-max-size">
                                    <xsl:text>2M</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:text>Max 2M:: jpg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
                                </xsl:attribute>
                            </input>
                            <br/>
                            <br/>
                            <input type="submit" class="btn btn-default" value="{php:function('lang', 'Add attachment')}" />
                        </form>
                
                    </div>
                </div>

                    <!-- Steg 2 -->
                    
                    
                    <div class="col-lg-12 application-group bg-light">
                        
                        <div class="heading"><xsl:value-of select="php:function('lang', 'Why?')" /></div>

                        <xsl:value-of select="php:function('lang', 'Activity')" />
                    
                     
                        <xsl:value-of select="application/activity_name"/>
                    
               
                        <xsl:value-of select="php:function('lang', 'Information about the event')" />
              
   
                        <div style="width: 80%;">
                            <xsl:value-of disable-output-escaping="yes" select="application/description"/>
                        </div>
                 
                    </div>

                        
                        
                        
                        
                        
                    <!-- Steg 3 -->
                    <div class="col-lg-12 application-group bg-light">
                        <div class="heading"><xsl:value-of select="php:function('lang', 'Where?')" /></div>
                        <xsl:value-of select="php:function('lang', 'Building')" />
                      
                        <xsl:value-of select="application/building_name"/>
                        (<a href="javascript: void(0)"
                            onclick="window.open('{application/schedule_link}',
                                 '', 
                                   'width=1048, height=600, scrollbars=yes');
                                      return false;">
                            <xsl:value-of select="php:function('lang', 'Building schedule')" />
                        </a>)
                          
                        <div id="resources_container"/>
                    </div>
                    
                    
                    
                    
                    <!-- Steg 4 -->
                    <div class="col-lg-12 application-group bg-light">
                   
                        <div class="heading"><xsl:value-of select="php:function('lang', 'When?')" /></div>
                        <script type="text/javascript">
                            var allocationParams = {};
                            var bookingParams = {};
                            var eventParams = {};
                        </script>
                        <xsl:for-each select="application/dates">
                   
                            <span style="font-weight:bold;">
                                <xsl:value-of select="php:function('lang', 'From')" />: &nbsp;</span>
                            <span>
                                <xsl:value-of select="php:function('pretty_timestamp', from_)"/>
                            </span>
                            <br/>
  
                            <span style="font-weight:bold;">
                                <xsl:value-of select="php:function('lang', 'To')" />: &nbsp;</span>
                            <span>
                                <xsl:value-of select="php:function('pretty_timestamp', to_)"/>
                            </span>
                  
                            <xsl:if test="../edit_link">
                                <script type="text/javascript">
                                    allocationParams[<xsl:value-of select="id"/>] = <xsl:value-of select="allocation_params"/>;
                                    bookingParams[<xsl:value-of select="id"/>] = <xsl:value-of select="booking_params"/>;
                                    eventParams[<xsl:value-of select="id"/>] = <xsl:value-of select="event_params"/>;
                                    var allocationaddURL = phpGWLink('bookingfrontend/index.php', {menuaction:'booking.uiallocation.add'});
                                    var bookingaddURL = phpGWLink('bookingfrontend/index.php', {menuaction:'booking.uibooking.add'});
                                    var eventaddURL = phpGWLink('bookingfrontend/index.php', {menuaction:'booking.uievent.add'});
                                </script>
                                <select name="create" onchange="if(this.selectedIndex==1) JqueryPortico.booking.postToUrl(allocationaddURL, allocationParams[{id}]); if(this.selectedIndex==2) JqueryPortico.booking.postToUrl(bookingaddURL, eventParams[{id}]); if(this.selectedIndex==3) JqueryPortico.booking.postToUrl(eventaddURL, eventParams[{id}]);">
                                    <xsl:if test="not(../case_officer/is_current_user)">
                                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                                    </xsl:if>

                                    <option>
                                        <xsl:value-of select="php:function('lang', '- Actions -')" />
                                    </option>
                                    <option>
                                        <xsl:value-of select="php:function('lang', 'Create allocation')" />
                                    </option>
                                    <option>
                                        <xsl:value-of select="php:function('lang', 'Create booking')" />
                                    </option>
                                    <option>
                                        <xsl:value-of select="php:function('lang', 'Create event')" />
                                    </option>
                                </select>
                            </xsl:if>
                        </xsl:for-each>
                
                    </div>



                    <!-- Steg 5 -->
                    <div class="col-lg-12 application-group bg-light">
             
                        <div class="heading"><xsl:value-of select="php:function('lang', 'Who?')" /></div>
                   
                        <xsl:value-of select="php:function('lang', 'Target audience')" />
                          
                            
                        <ul>
                            <xsl:for-each select="audience">
                                <xsl:if test="../application/audience=id">
                                    <li>
                                        <xsl:value-of select="name"/>
                                    </li>
                                </xsl:if>
                            </xsl:for-each>
                        </ul>
                 
             
                        <xsl:value-of select="php:function('lang', 'Number of participants')" />
                         
                        <div class="col-lg-7 col-md-11 col-sm-12">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th/>
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
                                        <xsl:variable name="id">
                                            <xsl:value-of select="id"/>
                                        </xsl:variable>
                                        <tr>
                                            <th>
                                                <xsl:value-of select="name"/>
                                            </th>
                                            <td>
                                                <xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
                                            </td>
                                            <td>
                                                <xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </div>
                    </div>
                        
                        
                        <!-- Steg 6 -->
                    <div class="col-lg-12 application-group bg-light">
                        <dl class="form-col">
                            <div class="heading"><xsl:value-of select="php:function('lang', 'Contact information')" /></div>
                            <dt>
                                <label for="field_contact_name">
                                    <xsl:value-of select="php:function('lang', 'Name')" />
                                </label>
                            </dt>
                            <dd>
                                <xsl:value-of select="application/contact_name"/>
                            </dd>
                            <dt>
                                <label for="field_contact_email">
                                    <xsl:value-of select="php:function('lang', 'Email')" />
                                </label>
                            </dt>
                            <dd>
                                <xsl:value-of select="application/contact_email"/>
                            </dd>
                            <dt>
                                <label for="field_contact_phone">
                                    <xsl:value-of select="php:function('lang', 'Phone')" />
                                </label>
                            </dt>
                            <dd>
                                <xsl:value-of select="application/contact_phone"/>
                            </dd>
                        </dl>
                    </div>
                    
                    <!-- Steg 7 -->
                    <div class="col-lg-12 application-group bg-light">
                        <dl class="form-col">
                            <div class="heading"><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
                            <xsl:if test="application/customer_identifier_type = 'organization_number'">
                                <dt>
                                    <label for="field_organization_number">
                                        <xsl:value-of select="php:function('lang', 'organization number')" />
                                    </label>
                                </dt>
                                <dd>
                                    <xsl:value-of select="application/customer_organization_number"/>
                                </dd>
                            </xsl:if>
                            <xsl:if test="application/customer_identifier_type = 'ssn'">
                                <dt>
                                    <label for="field_ssn_number">
                                        <xsl:value-of select="php:function('lang', 'Date of birth or SSN')" />
                                    </label>
                                </dt>
                                <dd>
                                    <xsl:value-of select="application/customer_ssn"/>
                                </dd>
                            </xsl:if>
                        </dl>
                    </div>
         
                    <!-- Steg 8 -->
                    <div class="col-lg-12 application-group bg-light">
                       
                                <div class="heading">
                                    <br /><xsl:value-of select="php:function('lang', 'Terms and conditions')" />
                                </div>
                                <xsl:if test="config/application_terms">
                                    <p>
                                        <xsl:value-of select="config/application_terms"/>
                                    </p>
                                </xsl:if>
                                <br />
                                <div id='regulation_documents'>&nbsp;</div>
                                <br />
                                <p>
                                    <xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" />
                                </p>
                     
                    </div>

                    <xsl:if test="application/edit_link">
                        <button class="btn btn-default">
                            <xsl:if test="application/case_officer/is_current_user">
                                <xsl:attribute name="onclick">window.location.href='<xsl:value-of select="application/edit_link"/>'</xsl:attribute>
                            </xsl:if>
                            <xsl:if test="not(application/case_officer/is_current_user)">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="php:function('lang', 'Edit')" />
                        </button>
                    </xsl:if>
                    <xsl:if test="not(frontend)">
                        <div class="pure-g">
                            <div class="pure-u-1">
                                <dl class="proplist">
                                    <dt class="heading">
                                        <xsl:value-of select="php:function('lang', 'Associated items')" />
                                    </dt>
                                    <dd>
                                        <div id="associated_container"/>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </xsl:if>

                    <xsl:if test="application/edit_link">
                        <dl class="proplist">
                            <dt class="heading">
                                <xsl:value-of select="php:function('lang', 'Actions')" />
                            </dt>
                            <dt>
                                <xsl:if test="application/case_officer/is_current_user">
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="unassign_user"/>
                                        <input type="submit" value="{php:function('lang', 'Unassign me')}"/>
                                    </form>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="display_in_dashboard" value="{phpgw:conditional(application/display_in_dashboard='1', '0', '1')}"/>
                                        <input type="submit" value="{php:function('lang', phpgw:conditional(application/display_in_dashboard='1', 'Hide from my Dashboard until new activity occurs', 'Display in my Dashboard'))}"/>
                                    </form>
                                </xsl:if>

                                <xsl:if test="not(application/case_officer/is_current_user)">
                                    <a name="assign"/>
                                    <form method="POST">
                                        <input type="hidden" name="assign_to_user"/>
                                        <input type="hidden" name="status" value="PENDING"/>
                                        <input type="submit" value="{php:function('lang', phpgw:conditional(application/case_officer, 'Re-assign to me', 'Assign to me'))}"/>

                                        <xsl:if test="application/case_officer">
                                            <xsl:value-of select="php:function('lang', 'Currently assigned to user:')"/>
                                            <xsl:text> </xsl:text>
                                            <xsl:value-of select="application/case_officer/name"/>
                                        </xsl:if>
                                    </form>
                                </xsl:if>
                            </dt>

                            <xsl:if test="application/status!='REJECTED'">
                                <dt>
                                    <form method="POST">
                                        <input type="hidden" name="status" value="REJECTED"/>
                                        <input onclick="return confirm('{php:function('lang', 'Are you sure you want to delete?')}')" type="submit" value="{php:function('lang', 'Reject application')}">
                                            <xsl:if test="not(application/case_officer)">
                                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                                            </xsl:if>
                                        </input>
                                    </form>
                                </dt>
                            </xsl:if>
                            <xsl:if test="application/status='PENDING'">
                                <xsl:if test="num_associations='0'">
                                    <input type="submit" disabled="" value="{php:function('lang', 'Accept application')}"/>
                                    <xsl:value-of select="php:function('lang', 'One or more bookings, allocations or events needs to be created before an application can be Accepted')"/>
                                </xsl:if>
                                <xsl:if test="num_associations!='0'">
                                    <dt>
                                        <form method="POST">
                                            <input type="hidden" name="status" value="ACCEPTED"/>
                                            <input type="submit" value="{php:function('lang', 'Accept application')}">
                                                <xsl:if test="not(application/case_officer)">
                                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                        </form>
                                    </dt>
                                </xsl:if>
                            </xsl:if>
                            <dd>
                                <br/>
                                <a href="{application/dashboard_link}">
                                    <xsl:value-of select="php:function('lang', 'Back to Dashboard')" />
                                </a>
                            </dd>
                        </dl>
                    </xsl:if>
             
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var resourceIds = '<xsl:value-of select="application/resource_ids" />';
        if (!resourceIds || resourceIds == "") {
        resourceIds = false;
        }
        var lang = <xsl:value-of select="php:function('js_lang', 'Resources', 'Resources Type', 'ID', 'Type', 'From', 'To', 'Document', 'Name')" />;
        var app_id = <xsl:value-of select="application/id" />;
        var building_id = <xsl:value-of select="application/building_id" />;
        var resources = <xsl:value-of select="application/resources" />;

        <![CDATA[
            var resourcesURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uiresource.index_json', sort:'name'}, true) +'&' + resourceIds;
            var applicationURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uiapplication.associated', filter_application_id:app_id}, true);
            var documentURL = phpGWLink('bookingfrontend/index.php', {menuaction:'booking.uidocument_view.regulations', sort:'name'}, true) + '&owner[]=building::' + building_id;
                documentURL += '&owner[]=resource::'+ resources;
			var attachmentsResourceURL = phpGWLink('bookingfrontend/index.php', {menuaction:'bookingfrontend.uidocument_application.index', sort:'name', no_images:1, filter_owner_id:app_id}, true);
        ]]>

        if (resourceIds) {
        var colDefsResource = [{key: 'name', label: lang['Resources'], formatter: genericLink}, {key: 'type', label: lang['Resource Type']}];
        createTable('resources_container', resourcesURL, colDefsResource, 'results');
        var colDefsApplication = [
        {key: 'id', label: lang['ID'], formatter: genericLink},
        {key: 'type', label: lang['Type']},
        {key: 'from_', label: lang['From']},
        {key: 'to_', label: lang['To']}
        ];
        createTable('associated_container', applicationURL, colDefsApplication);
        }

        var colDefsDocument = [{key: 'name', label: lang['Document'], formatter: genericLink}];
        createTable('regulation_documents', documentURL, colDefsDocument);

        var colDefsAttachmentsResource = [{key: 'name', label: lang['Name'], formatter: genericLink}];
        createTable('attachments_container', attachmentsResourceURL, colDefsAttachmentsResource);

    </script>
</xsl:template>
