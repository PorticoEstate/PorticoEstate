<xsl:template match="data" xmlns:php="http://php.net/xsl">
    
    <div class="container-fluid main-container application-background">
        <div class="container"> 
            <form action="" method="POST" id='application_form' enctype='multipart/form-data' name="form">
                
                <div class="row">
                    <div class="col-lg-12">
                        <h3>
                            <xsl:value-of select="php:function('lang', 'New application')"/>
                        </h3>
                        <xsl:if test="config/application_new_application">
                            <p >
                                <xsl:value-of select="config/application_new_application"/>
                            </p>
                        </xsl:if>
            
                        <xsl:call-template name="msgbox"/>
                    </div>
            
            
                    <!-- Steg 1 -->
                    <div class="col-lg-12 application-group bg-light">
                                    
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Why?')" />
                        </div>
                        
                        <div class="form-group">
                            <label for="field_activity">
                                <xsl:value-of select="php:function('lang', 'Activity')" />
                            </label>
                            <xsl:if test="config/application_activities">
                                <p>
                                    <xsl:value-of select="config/application_activities"/>
                                </p>
                            </xsl:if>
                        
                            
                            <select name="activity_id" class="form-control col-lg-4 col-md-7 col-sm-12" id="field_activity">
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please select an activity')" />
                                </xsl:attribute>
                                <option value="">
                                    <xsl:value-of select="php:function('lang', '-- select an activity --')" />
                                </option>
                                <xsl:for-each select="activities">
                                    <option>
                                        <xsl:if test="../application/activity_id = id">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="id"/>
                                        </xsl:attribute>
                                        <xsl:value-of select="name"/>
                                    </option>
                                </xsl:for-each>
                            </select>
                        </div>
                   
                        <label for="field_description">
                            <xsl:value-of select="php:function('lang', 'Information about the event')" />
                        </label>
                        <xsl:if test="config/application_description">
                            <p>
                                <xsl:value-of select="config/application_description"/>
                            </p>
                        </xsl:if>
                     
                        <div class="form-group">
                            <textarea id="field_description" class="form-control" rows="3" name="description">
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please enter a descripction')" />
                                </xsl:attribute>
                                <xsl:value-of select="application/description"/>
                            </textarea>
                        </div>
                    
                      
                        <xsl:if test="config/application_equipment">
                            <p>
                                <xsl:value-of select="config/application_equipment"/>
                            </p>
                        </xsl:if>
                      
                        <div class="form-group">
                            <textarea id="field_equipment" class="form-control" rows="6" name="equipment">
                                <xsl:value-of select="application/equipment"/>
                            </textarea>
                        </div>
                    
                    </div>
                
                
                
                    <!-- Steg 2-->
                    <div class="col-lg-12 application-group bg-light">
                    
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'How many?')" />
                        </div>
                        <xsl:if test="config/application_howmany">
                            <p>
                                <xsl:value-of select="config/application_howmany"/>
                            </p>
                        </xsl:if>
              
                        <label for="field_agegroups">
                            <xsl:value-of select="php:function('lang', 'Estimated number of participants')" />
                        </label>
              
       
                        <input type="hidden" data-validation="number_participants">
                            <xsl:attribute name="data-validation-error-msg">
                                <xsl:value-of select="php:function('lang', 'Number of participants is required')" />
                            </xsl:attribute>
                        </input>
                    
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
                                                <input type="text" class="form-control">
                                                    <xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
                                                    <xsl:attribute name="value">
                                                        <xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
                                                    </xsl:attribute>
                                                </input>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control">
                                                    <xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
                                                    <xsl:attribute name="value">
                                                        <xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/>
                                                    </xsl:attribute>
                                                </input>
                                            </td>
                                        </tr>
                                    </xsl:for-each>
                                </tbody>
                            </table>
                        </div>
                    
                    </div>
                
                
                    <!-- Steg 3-->
                    <div class="col-lg-12 application-group bg-light">
                    
                  
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Where?')" />
                        </div>
                        <xsl:if test="config/application_where">
                            <p>
                                <xsl:value-of select="config/application_where"/>
                            </p>
                        </xsl:if>
                      
                        <label for="field_building">
                            <xsl:value-of select="php:function('lang', 'Building')" />
                        </label>
                      
                    
                        <div class="autocomplete form-group">
                            <input id="field_building_id" name="building_id" type="hidden">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/building_id"/>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please enter a building name')" />
                                </xsl:attribute>
                            </input>
                            <input id="field_building_name" class="form-control col-lg-4 col-md-7 col-sm-12" name="building_name" type="text">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/building_name"/>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please enter a building name')" />
                                </xsl:attribute>
                            </input>
                            <div id="building_container"/>
                        </div>
                    
                        
                        <label for="field_resources">
                            <xsl:value-of select="php:function('lang', 'Resources')" />
                        </label>
                      
                 
                        <input type="hidden" data-validation="application_resources">
                            <xsl:attribute name="data-validation-error-msg">
                                <xsl:value-of select="php:function('lang', 'Please choose at least 1 resource')" />
                            </xsl:attribute>
                        </input>
                        <div id="resources_container">
                            <span class="select_first_text">
                                <xsl:value-of select="php:function('lang', 'Select a building first')" />
                            </span>
                                
                            <!-- Fiksa tabel design, sett rett klasse på tabellen som er appenda med js--> 
                        </div>
          
                    </div>
                
                
                    <!-- Steg 4-->
                
                    <div class="col-lg-12 application-group bg-light">
                            <div class="heading"> <xsl:value-of select="php:function('lang', 'When?')" /></div>
                            <xsl:if test="config/application_when">
                                <p><xsl:value-of select="config/application_when"/></p>
                            </xsl:if>
                            
                            <div class="form-group" id="dates-container">
                                <input type="hidden" class="form-control" data-validation="application_dates">
                                    <xsl:attribute name="data-validation-error-msg">
                                        <xsl:value-of select="php:function('lang', 'Invalid date')" />
                                    </xsl:attribute>
                                </input>
                                <input type="hidden" id="date_format" />
                                <xsl:for-each select="application/dates">
                                    <xsl:variable name="index" select="position()-2"/>
                                    <xsl:choose>
                                        <xsl:when test="position() > 1">
                                            <div class="date-container">
                                                <a href="javascript:void(0);" class="btnclose">
                                                    <xsl:value-of select="php:function('lang', 'Remove date')" />
                                                </a>
                                                <div class="form-group">
                                                    <label for="start_date_{$index}">
                                                        <xsl:value-of select="php:function('lang', 'From')" />
                                                    </label>
                                                    <input class="newaddedpicker datetime form-control" id="start_date_{$index}" type="text" name="from_[]">
                                                        <xsl:attribute name="value">
                                                            <xsl:value-of select="from_" />
                                                        </xsl:attribute>
                                                        <xsl:attribute name="readonly">
                                                            <xsl:text>readonly</xsl:text>
                                                        </xsl:attribute>
                                                    </input>
                                                </div>
                                                <div class="form-group">
                                                    <label for="end_date_{$index}">
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
                                                    <input class="newaddedpicker datetime form-control" id="end_date_{$index}" type="text" name="to_[]">
                                                        <xsl:attribute name="value">
                                                            <xsl:value-of select="to_"/>
                                                        </xsl:attribute>
                                                        <xsl:attribute name="readonly">
                                                            <xsl:text>readonly</xsl:text>
                                                        </xsl:attribute>
                                                    </input>
                                                </div>
                                            </div>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <div class="date-container">
                                                <a href="javascript:void(0);" class="btnclose">
                                                    <xsl:value-of select="php:function('lang', 'Remove date')" />
                                                </a>
                                                <div class="form-group">
                                                    <label for="start_date">
                                                        <xsl:value-of select="php:function('lang', 'From')" />
                                                    </label>
                                                    <input class="datetime form-control" id="start_date" type="text" name="from_[]">
                                                        <xsl:attribute name="value">
                                                            <xsl:value-of select="from_"/>
                                                        </xsl:attribute>
                                                        <xsl:attribute name="readonly">
                                                            <xsl:text>readonly</xsl:text>
                                                        </xsl:attribute>
                                                    </input>
                                                </div>
                                                <div class="form-group">
                                                    <label for="end_date">
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
                                                    <input class="datetime form-control" id="end_date" type="text" name="to_[]">
                                                        <xsl:attribute name="value">
                                                            <xsl:value-of select="to_"/>
                                                        </xsl:attribute>
                                                        <xsl:attribute name="readonly">
                                                            <xsl:text>readonly</xsl:text>
                                                        </xsl:attribute>
                                                    </input>
                                                </div>
                                            </div>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:for-each>
                            </div>
                            <div class="pure-control-group">
                                <a href="javascript:void(0);" id="add-date-link">
                                    <xsl:value-of select="php:function('lang', 'Add another date')" />
                                </a>
                            </div>
                    </div>
                
                
                
                    <!-- Steg 5 -->
                
                    <div class="col-lg-12 application-group bg-light">
   
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Who?')" />
                        </div>
                        <xsl:if test="config/application_who">
                            <p>
                                <xsl:value-of select="config/application_who"/>
                            </p>
                        </xsl:if>
                    
                        <label for="field_from">
                            <xsl:value-of select="php:function('lang', 'Target audience')" />
                        </label>
                     
                        <div class="custom-controls-stacked">
                            <input type="hidden" data-validation="target_audience">
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please choose at least 1 target audience')" />
                                </xsl:attribute>
                            </input>
              
                            <xsl:for-each select="audience">
                    
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="audience[]">
                                        <xsl:attribute name="value">
                                            <xsl:value-of select="id"/>
                                        </xsl:attribute>
                                        <xsl:if test="../application/audience=id">
                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                        </xsl:if>
                                    </input>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description">
                                        <xsl:value-of select="name"/>
                                    </span>
                                            
                                </label>
                   
                            </xsl:for-each>
                        </div>
                              
                    </div>
                
                
                    <!-- Steg 6-->
                
                    <div class="col-lg-12 application-group bg-light">
                    
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Contact information')" />
                        </div>
                        <xsl:if test="config/application_contact_information">
                            <p>
                                <xsl:value-of select="config/application_contact_information"/>
                            </p>
                        </xsl:if>
                            
                   
                        <div class="form-group col-lg-4 col-md-7 col-sm-12 no-padding-left">
                            <label for="field_contact_name">
                                <xsl:value-of select="php:function('lang', 'Name')" />
                            </label>
                 
                
                            <input id="field_contact_name" class="form-control" name="contact_name" type="text">
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please enter a contact name')" />
                                </xsl:attribute>
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/contact_name"/>
                                </xsl:attribute>
                            </input>
                        </div>
                   
                        <div class="form-group col-lg-4 col-md-7 col-sm-12 no-padding-left">
                            <label for="field_contact_email">
                                <xsl:value-of select="php:function('lang', 'E-mail address')" />
                            </label>
                   
                 
                            <input id="field_contact_email" class="form-control" name="contact_email" type="text">
                                <xsl:attribute name="data-validation">
                                    <xsl:text>required</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'Please enter a contact email')" />
                                </xsl:attribute>
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/contact_email"/>
                                </xsl:attribute>
                            </input>
                        </div>
                
                        <div class="form-group col-lg-4 col-md-7 col-sm-12 no-padding-left">
                            <label for="field_contact_email2">
                                <xsl:value-of select="php:function('lang', 'Confirm e-mail address')" />
                            </label>
                  
                
                            <input id="field_contact_email2" class="form-control" name="contact_email2" type="text">
                                <xsl:attribute name="data-validation">
                                    <xsl:text>confirmation</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-confirm">
                                    <xsl:text>contact_email</xsl:text>
                                </xsl:attribute>
                                <xsl:attribute name="data-validation-error-msg">
                                    <xsl:value-of select="php:function('lang', 'The e-mail addresses you entered do not match')" />
                                </xsl:attribute>
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/contact_email2"/>
                                </xsl:attribute>
                            </input>
                        </div>
                  
                        <div class="form-group col-lg-4 col-md-7 col-sm-12 no-padding-left">
                            <label for="field_contact_phone">
                                <xsl:value-of select="php:function('lang', 'Phone')" />
                            </label>
                 
               
                            <input id="field_contact_phone" class="form-control" name="contact_phone" type="text">
                                <xsl:attribute name="value">
                                    <xsl:value-of select="application/contact_phone"/>
                                </xsl:attribute>
                            </input>
                        </div>
                  
 
                    </div>
                
                
                    <!-- Steg 7-->
                
                    <div class="col-lg-12 application-group bg-light">

                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" />
                        </div>
                        <xsl:if test="config/application_responsible_applicant">
                            <p>
                                <xsl:value-of select="config/application_responsible_applicant"/>
                            </p>
                        </xsl:if>
                        <xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
                        <br />
                        <xsl:if test="config/application_invoice_information">
                            <p>
                                <xsl:value-of select="config/application_invoice_information"/>
                            </p>
                        </xsl:if>    
                    </div> 
                
                
                    <!-- Steg 8 -->
                
                    <div class="col-lg-12 application-group bg-light">
              
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Terms and conditions')" />
                        </div>
                        <input type="hidden" data-validation="regulations_documents">
                            <xsl:attribute name="data-validation-error-msg">
                                <xsl:value-of select="php:function('lang', 'You must accept to follow all terms and conditions of lease first')" />
                            </xsl:attribute>
                        </input>
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
                
                
                    <!-- Steg 9 -->
                
                    <div class="col-lg-12 application-group bg-light">
                        
                        <div class="heading">
                            <xsl:value-of select="php:function('lang', 'Attachment')" />
                        </div>

                    
                        <label for="field_name">
                            <xsl:value-of select="php:function('lang', 'Document')" />
                        </label>
                    
                        <br/>
                        
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
                    
               
                
                    </div>
                    

                    <div class="col-lg-12" style="margin-top: 2em">
                    
                        <input type="submit" class="btn btn-main">
                            <xsl:attribute name="value">
                                <xsl:value-of select="php:function('lang', 'Send')"/>
                            </xsl:attribute>
                        </input> 
                    
                        <a class="cancel" style="margin-left: 20px">
                            <xsl:attribute name="href">
                                <xsl:value-of select="application/cancel_link"/>
                            </xsl:attribute>
                            <xsl:value-of select="php:function('lang', 'Cancel')" />
                        </a>
                        <p style="margin-top: 10px;">Trykker du <strong>SEND</strong>-knappen får du opp en rød melding øverst om noen opplysninger mangler, er alt OK kommer det opp en grønn melding. Det blir sendt en bekreftelse til din e-post, og en lenke hvor du kan gå inn og se status og legge til ekstra opplysninger i saken.<br />
                            <br />
                            Trykker du <strong>Avbryt</strong> blir søknaden ikke sendt eller lagret, og du går tilbake til kalenderen.</p>
                
                    </div>
                
                </div>

        
        


          
             
       
            </form>
        </div>
    </div>

    <script type="text/javascript">
        var initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
        var initialAcceptAllTerms = false;
        var initialSelection = <xsl:value-of select="application/resources_json"/>;
        var initialAudience = <xsl:value-of select="application/audience_json"/>;
        var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
        $('#field_customer_identifier_type').attr("data-validation","customer_identifier").attr("data-validation-error-msg", "<xsl:value-of select="php:function('lang', 'Customer identifier type is required')" />");
    </script>
</xsl:template>
