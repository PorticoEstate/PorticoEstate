<xsl:template match="data" xmlns:php="http://php.net/xsl">
          
                        
                        
	<div class="container new-application-page" id="new-application-page">
               
            <form action="" data-bind='submit: addApplication' method="POST" id='application_form' name="form">
            <div class="row">
                
                <div class="col-md-8 offset-md-2">
                    
                    <h1 class="font-weight-bold"><xsl:value-of select="php:function('lang', 'New application')"/></h1>
                    
                    <p><xsl:value-of select="config/application_new_application"/></p>
                    <hr class="mt-5 mb-5"></hr>
                    
                    <h5 class="font-weight-bold mb-4">Anlegg</h5>
                    
                    <div class="form-group">
                        <label>RESSURS</label>
                        <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                            Velg lokaler 
                            <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu px-2 resourceDropdown" data-bind="foreach: bookableresource">
                            <li>
                                <div class="form-check checkbox checkbox-primary">
                                    <label class="check-box-label">
                                        <input class="form-check-input choosenResource" type="checkbox"  data-bind="textInput: name, checked: selected"/>
                                        <span class="label-text" data-bind="text: name"></span>
                                    </label>
                                </div>
                            </li>
                            
                        </ul>
                        
                    </div>
                    
                    <div class="form-group">
                        <span class="font-weight-bold d-block mt-2 span-label">Valgte ressurser</span>
                        <div data-bind="foreach: bookableresource">
                            <span class="seperateByComma mr-2" data-bind='text: selected() ? name : ""'></span>
                        </div>
                        <span data-bind="ifnot: isResourceSelected" class="isSelected validationMessage">Ingen ressurs valgt!</span> 
                    </div>
                    
                    <div class="form-group" >
                        <div class="form-inline">
                            <div class="form-group mr-5">
                                <input type="text" for="date" onkeydown="return false" class="form-control datepicker-btn" data-bind="textInput: bookingDate" placeholder="Dato"/>
                            </div>
                            <div class="form-group">
                                <input type="text" for="timestart" onkeydown="return false" class="form-control bookingStartTime mr-2" data-bind="textInput: bookingStartTime" placeholder="Fra"/>
                            </div>
                            <div class="form-group">
                                <input type="text" for="timeend" onkeydown="return false" class="form-control bookingEndTime" data-bind="textInput: bookingEndTime" placeholder="Til"/>
                            </div>

                            <!--<label class="check-box-label">
                                <input class="form-check-input repeatEvent" type="checkbox"  data-bind="checked: repeat"/>
                                <span class="label-text">Repeter ukentlig</span>
                            </label>-->
                                                      
                        </div>
                        
                        
                        <button class="btn btn-outline-light btn-sm mt-2 border-0" type="button" data-bind="click: addDate"><i class="fas fa-plus"></i> Legg til dato</button>
                    </div>
                    
                    <div class="form-group">
                        <span class="font-weight-bold d-block mt-2 span-label">Valgte datoer</span>
                        <div data-bind="foreach: date">
                            <div class="d-block">
                                <span data-bind='text: formatedPeriode'></span>
                                <!--<span data-bind='text: repeat == true ? " (repeter)" : ""'></span>-->
                                <butoon class="ml-2" data-bind="click: $parent.removeDate"><i class="fas fa-minus-circle"></i></butoon>
                            </div>
                            
                        </div>
                        
                        
                        
                        <span data-bind="if: date().length == 0" class="validationMessage applicationSelectedDates">Ingen dato valgt</span>
                    </div>
                    
                    <hr class="mt-5 mb-5"></hr>
                    
                    <h5 class="font-weight-bold mb-4">Om arrangementet</h5>
                    
                    <div class="form-group">
                        <label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Target audience')" /></label>
                        
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-secondary dropdown-toggle d-inline mr-2 btn-sm" id="audienceDropdownBtn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Velg      
                            </button>
                            <div class="dropdown-menu" data-bind="foreach: audiences" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected" href="#"></a>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>ARRANGØR</label>
                        <input type="text" class="form-control" data-bind="textInput: organizer"/>  
                    </div>
                    <div class="form-group">
                        <label>NAVN PÅ ARRANGEMENT</label>
                        <input type="text" class="form-control" data-bind="textInput: arrangementName"/>
                    </div>
                    <div class="form-group">
                        <label>OM ARRANGEMENT</label>
                        <textarea class="form-control" data-bind="textInput: aboutArrangement"></textarea>
                    </div>
                    
                    <!--<div class="form-group">
                        <label>ESTIMERT ANTALL DELTAGERE</label>
                        <div class="p-2 border">
                            <div class="row mb-2">
                                <div class="col-3">
                                    <span class="span-label mt-2"></span>
                                </div>
                                <div class="col-4">
                                    <span>Menn</span>
                                </div>
                                <div class="col-4">
                                     <span>Kvinner</span>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-3">
                                    <span class="mt-2">0 - 12 år</span>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="mann" data-bind="textInput: participantMenU12"/>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="kvinne" data-bind="textInput: participantWomenU12"/>
                                </div>
                            </div>
                            
                            <div class="row mb-2">    
                                <div class="col-3">
                                    <span class="mt-2">13 - 19 år</span>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="mann" data-bind="textInput: participantMenO13"/>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="kvinne" data-bind="textInput: participantWomenO13"/>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-3">
                                    <span class="mt-2">20+ år</span>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="mann" data-bind="textInput: participantMenO20"/>
                                </div>
                                <div class="col-4">
                                     <input class="form-control sm-input" placeholder="kvinne" data-bind="textInput: participantWomenO20"/>
                                </div>
                            </div>    
                                                                                       
                        </div>
                    </div>-->

                    <div class="form-group">
                        <label>ESTIMERT ANTALL DELTAGERE</label>
                        <div class="p-2 border">
                            <div class="row mb-2">
                                <div class="col-3">
                                    <span class="span-label mt-2"></span>
                                </div>
                                <div class="col-4">
                                    <span>Menn</span>
                                </div>
                                <div class="col-4">
                                     <span>Kvinner</span>
                                </div>
                            </div>
                            
                            <div class="row mb-2" data-bind="foreach: agegroup">
                                <span data-bind="text: id, visible: false"/>
                                <div class="col-3">
                                    <span class="mt-2" data-bind="text: agegroupLabel"></span>
                                </div>
                                <div class="col-4">                                    
                                    <input class="form-control sm-input" placeholder="mann" data-bind="textInput: inputCountMale"/>
                                </div>
                                <div class="col-4">
                                    <input class="form-control sm-input" placeholder="kvinne" data-bind="textInput: inputCountFemale"/>
                                </div>
                            </div>
                                                                                       
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label>BEHOV FOR SPESIELL TILRETTELEGGING</label>
                        <textarea class="form-control" data-bind="textInput: specialRequirements"></textarea>
                    </div>

                    <div class="form-group">
                        <label><xsl:value-of select="php:function('lang', 'Attachment')" /></label>
                        <input type="file" data-bind="textInput: attachment"/>  
                    </div>
                    <div class="form-group termAccept mt-5 mb-5">
                        <!--<label><input type="checkbox" data-bind="checked: termAccept"/>&#160; <xsl:value-of select="php:function('lang', 'You must accept to follow all terms and conditions of lease first')" /></label>
                        -->
                        <label>JURDISKE BETINGELSER</label>
                        <span data-bind="ifnot: termAccept" class="validationMessage">Jurdiske betingelser er ikke godtatt</span>                         
                        <div class="form-check checkbox" data-bind="foreach: termAcceptDocs">
                            <div>
                                <label class="check-box-label d-inline">
                                    <input class="form-check-input" type="checkbox" data-bind="checked: checkedStatus"/>
                                    <span class="label-text" data-bind=""></span>
                                </label>
                                <a class="d-inline termAcceptDocsUrl" data-bind=""> </a>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mt-5 mb-5"></hr>
                    
                    <div class="container" data-bind="foreach: msgboxes">
                        <div class="alert alert-warning" data-bind="text: msg" role="alert">
                        
                        </div>
                    </div>

                    <div class="form-group float-right">
                        <button class="btn btn-outline-light btn-sm border-0" type="submit">Legg til søknad</button>
                        <button id="goToConfirmPage" class="btn btn-light" data-bind='click: GoToConfirmPage'>Gå til kontakt og fakturainformasjon</button>                        
                    </div>
                </div>
            </div>
            </form>
                                   
            <!--<pre data-bind="text: ko.toJSON(am, null, 2)"></pre> -->
                
            <div class="push"></div>
        </div>
	<script type="text/javascript">
            var script = document.createElement("script"); 
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/application_new.js";
           //script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/application_new.js";

            document.head.appendChild(script);			
        </script>
</xsl:template>
