<xsl:template match="data" xmlns:php="http://php.net/xsl">
          
                        
                        
	<div class="container new-application-page pt-5" id="new-application-page">
            <form action="" data-bind='' method="POST" id='application_form' enctype='multipart/form-data' name="form">
            <div class="row">
                
                <div class="col-md-8 offset-md-2">                   
                    
                    <h1 class="font-weight-bold"><xsl:value-of select="php:function('lang', 'New application')"/></h1>
                    
                    <p><xsl:value-of select="config/application_new_application"/></p>
                    <hr class="mt-5 mb-5"></hr>

                    <div class="mb-4"><xsl:call-template name="msgbox"/></div>
                    
                    <input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId" />
                    <input name="formstage" value="partial1" hidden="hidden"/>
					<h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Building (2018)')" /></h5>

					<p>
						<xsl:value-of select="php:function('lang', 'Application for')"/>:
						<xsl:value-of select="application/building_name"/><br/>
						<a>
							<xsl:attribute name="href">
								<xsl:value-of select="application/frontpage_link"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Choose another building')"/>
						</a>
					</p>
                                        
                    <div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Resource (2018)')" />*</label>
                        <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                            <xsl:value-of select="php:function('lang', 'choose')" /> 
                            <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu px-2 resourceDropdown" data-bind="foreach: bookableresource">
                            <li>
                                <div class="form-check checkbox checkbox-primary">
                                    <label class="check-box-label">
                                        <input class="form-check-input choosenResource" type="checkbox" name="resources[]" data-bind="textInput: id, checked: selected" />
										<span class="label-text" data-bind="html: name"></span>

                                    </label>
                                </div>
                            </li>
                            
                        </ul>
                    </div>
                    
                    <div class="form-group">
						<span class="font-weight-bold d-block mt-2 span-label">
							<xsl:value-of select="php:function('lang', 'Chosen resources (2018)')" />
						</span>
                        <div data-bind="foreach: bookableresource">
                            <span class="selectedItems mr-2" data-bind='html: selected() ? name : "", visible: selected()'></span>
                        </div>
						<span data-bind="ifnot: isResourceSelected" class="isSelected validationMessage">
							<xsl:value-of select="php:function('lang', 'No resource chosen (2018)')" />
						</span>
                    </div>
                    
                    <div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Date and time')" />*</label>
                        <div class="form-group">
                        <div class="row">
                                <div class="form-group col-lg-5 col-sm-12 col-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" for="date" onkeydown="return false" class="form-control datepicker-btn" data-bind="textInput: bookingDate">
                                            <xsl:attribute name="placeholder">
                                                <xsl:value-of select="php:function('lang', 'Date')"/>
                                            </xsl:attribute>
                                        </input>
                                    </div>                                    
                                </div>    

                                <div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="text" for="timestart" onkeydown="return false" class="form-control bookingStartTime mr-2" data-bind="textInput: bookingStartTime">
                                            <xsl:attribute name="placeholder">
                                                <xsl:value-of select="php:function('lang', 'from')"/>
                                            </xsl:attribute>
                                        </input>
                                    </div>                                   
                                </div>

                                <div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="text" for="timeend" onkeydown="return false" class="form-control bookingEndTime" data-bind="textInput: bookingEndTime">
                                            <xsl:attribute name="placeholder">
                                                <xsl:value-of select="php:function('lang', 'to')"/>
                                            </xsl:attribute>
                                        </input>
                                    </div>                                   
                                </div>

                            </div>
                                                      
                        </div>
                                                
						<!--<button class="btn btn-outline-light btn-sm mt-2 border-0" type="button" data-bind="click: addDate">
							<i class="fas fa-plus"></i>&#160;
							<xsl:value-of select="php:function('lang', 'Add date and time')" />
						</button>-->
                    </div>
                    
                    <div class="form-group">
						<span class="font-weight-bold d-block mt-2 span-label">
							<xsl:value-of select="php:function('lang', 'Selected date and time')" />
						</span>
                        <div data-bind="foreach: date">
                            <div class="d-block">
                                <input name="from_[]" hidden="hidden" data-bind="value: from_"/>
                                <input name="to_[]" hidden="hidden" data-bind="value: to_"/>                              
                                <span class="selectedItems" data-bind='text: formatedPeriode'></span>
                                <butoon class="ml-2" data-bind="click: $parent.removeDate"><i class="fas fa-minus-circle"></i></butoon>
                            </div>
                            
                        </div>
                                                
						<span data-bind="if: date().length == 0" class="validationMessage applicationSelectedDates">
							<xsl:value-of select="php:function('lang', 'Select a date and time')" />
						</span>
                    </div>
                    
                    <hr class="mt-5 mb-5"></hr>
                    
                    <h5 class="font-weight-bold mb-4"><xsl:value-of select="php:function('lang', 'Information about the event')" /></h5>
                    
                    <div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Target audience')" />*</label>
                        
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-secondary dropdown-toggle d-inline mr-2 btn-sm" id="audienceDropdownBtn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <xsl:value-of select="php:function('lang', 'choose')" />      
                            </button>
                            <div class="dropdown-menu" data-bind="foreach: audiences" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected" href="#"></a>
                            </div>
                            <input type="text" name="audience[]" hidden="hidden" data-bind="value: audienceSelectedValue" />
                        </div>

                    </div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Event name')" />*</label>
						<input type="text" class="form-control" name="name" value="{application/name}"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organizer')" />*</label>
						<input type="text" class="form-control" name="organizer" value="{application/organizer}"/>
					</div>

					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Homepage for the event')" /></label>
						<input type="text" class="form-control" name="homepage" value="{application/homepage}"/>
					</div>

                    <div class="form-group">
                        <label class="text-uppercase"><xsl:value-of select="php:function('lang', 'description')" /></label>
                        
                        <textarea id="field_description" class="form-control" rows="3" name="description">
                                <xsl:value-of select="application/description"/>
                        </textarea>
                    </div>                  
                   
					<div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="config/application_equipment"/></label>
						<textarea class="form-control" name="equipment">
							<xsl:value-of select="application/equipment"/>
						</textarea>
					</div>

                    <div class="form-group">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Estimated number of participants')" />*</label>
                        <div class="p-2 border">
                            <div class="row mb-2">
                                <div class="col-3">
                                    <span class="span-label mt-2"></span>
                                </div>
                                <div class="col-4">
                                    <span><xsl:value-of select="php:function('lang', 'Male')" /></span>
                                </div>
                                <div class="col-4">
                                     <xsl:value-of select="php:function('lang', 'Female')" />
                                </div>
                            </div>
                            
                            <div class="row mb-2" data-bind="foreach: agegroup">
                                <span data-bind="text: id, visible: false"/>
                                <div class="col-3">
                                    <span class="mt-2" data-bind="text: agegroupLabel"></span>
                                </div>
                                <div class="col-4">                                    
                                    <input class="form-control sm-input maleInput" data-bind=""/>
                                </div>
                                <div class="col-4">
                                    <input class="form-control sm-input femaleInput" data-bind=""/>
                                </div>
                            </div>
                                                                                       
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Attachment')" /></label>
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
                    <div class="form-group termAccept mt-5 mb-5">
						<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'legal condition')" />*</label>
                        <span data-bind="ifnot: termAccept" class="validationMessage"><xsl:value-of select="config/application_terms2"/></span>                         
                        <div class="form-check checkbox" data-bind="foreach: termAcceptDocs">
                            <div>
                                <label class="check-box-label d-inline">
                                    <input class="form-check-input" type="checkbox" data-bind="checked: checkedStatus"/>
                                    <span class="label-text" data-bind=""></span>
                                </label>
                                <a class="d-inline termAcceptDocsUrl" target="_blank" data-bind=""> </a>
                            </div>
                        </div>                     
                        
                    </div>
                    
                    <hr class="mt-5 mb-5"></hr>
                    
                    <div class="form-group float-right">
						<button class="btn btn-light" type="submit">
							<xsl:value-of select="php:function('lang', 'Add application')" />
						</button>
                    </div>
                </div>
            </div>
            </form>
                                   
            <!--<pre data-bind="text: ko.toJSON(am, null, 2)"></pre>-->
                
            <div class="push"></div>
        </div>
	<script type="text/javascript">
            var initialSelection = <xsl:value-of select="application/resources_json"/>;
            var initialAudience = <xsl:value-of select="application/audience_json"/>;
            var initialDates = <xsl:value-of select="application/dates_json"/>;
            var initialAgegroups = <xsl:value-of select="application/agegroups_json"/>;
            var initialAcceptedDocs = <xsl:value-of select="application/accepted_documents_json"/>;
		var errorAcceptedDocs = '<xsl:value-of select="config/application_terms2"/>';
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/application_new.js";
           //script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/application_new.js";

            document.head.appendChild(script);			
        </script>
</xsl:template>
