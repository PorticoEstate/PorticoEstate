<xsl:template match="data" xmlns:php="http://php.net/xsl">


    <div class="container new-application-page" id="new-application-partialtwo">    

        <form action="" data-bind='submit: postApplication' method="POST" id='application_form' name="form">
        <div class="col-md-8 offset-md-2" data-bind="visible: !applicationSuccess()">

            <h1 class="font-weight-bold">Kontakt og fakturainformasjon</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>

            <hr class="mt-5 mb-5"></hr>
            
            <h5 class="font-weight-bold mb-4">Søknader</h5>
            <p class="validationMessage" data-bind="visible: applicationCartItems().length == 0">Du har ingen lagrede søknader.</p>
            
            <div data-bind="visible: applicationCartItems().length != 0">
                <div data-bind="foreach: applicationCartItems">
                <div class="applications p-4 mb-2">
                    <div class="row">
                        <span class="col-6" data-bind="text: building_name"></span>
                        <div data-bind="foreach: resources" class="col-5"><span class="mr-3" data-bind="text: name"></span></div>
                        <div class="col-1 text-right">
                            <span data-bind="click: $parent.deleteItem" class="far fa-trash-alt mr-2"></span>
                            <!--<a href="" class="far fa-edit"></a>-->
                        </div>
                    </div>
                    <div class="row" data-bind="foreach: dates">
                        <span class="col-6" data-bind="text: date"></span>
                        <span class="col-6" data-bind="text: periode"></span>
                    </div>
                </div>
                </div>
                
                <hr class="mt-5 mb-5"></hr>
                
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="typeApplicationRadio" id="privateRadio" data-bind="checked: typeApplicationRadio" value="ssn"/>
                    <label class="form-check-label" for="privateRadio">Privat</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="typeApplicationRadio" id="orgRadio" data-bind="checked: typeApplicationRadio" value="organization_number"/>
                    <label class="form-check-label" for="orgRadio">Organisasjon</label>
                </div>
                <p data-bind="ifnot: typeApplicationSelected, visible: typeApplicationValidationMessage" class="isSelected validationMessage">Ingen valgt</p>

                
                <div class="form-group" data-bind="visible: typeApplicationRadio() === 'organization_number'">
                    <label>ORG.NUMMER</label>
                    <input type="text" class="form-control" data-bind="textInput: orgnr"/>  
                </div>
                
                <div class="form-group" data-bind="visible: typeApplicationRadio() === 'ssn'">
                    <label>FØDSELSNUMMER</label>
                    <input type="text" class="form-control" data-bind="textInput: ssn"/>  
                </div>
                
                <div class="form-group">
                    <label>NAVN</label>
                    <input type="text" class="form-control" data-bind="textInput: contactName"/>  
                </div>

                <div class="form-group">
                    <label>GATE</label>
                    <input type="text" class="form-control" data-bind="textInput: responsible_street"/>  
                </div>                

                <div class="form-group">
                    <label>POSTNUMMER</label>
                    <input type="text" class="form-control" data-bind="textInput: responsible_zip_code"/>  
                </div>

                <div class="form-group">
                    <label>STEDSNAVN</label>
                    <input type="text" class="form-control" data-bind="textInput: responsible_city"/>  
                </div>
                
                <div class="form-group">
                    <label>E-POST</label>
                    <input type="text" class="form-control" data-bind="textInput: contactMail"/>  
                </div>

                <div class="form-group">
                    <label>BEKREFT E-POST</label>
                    <input type="text" class="form-control" data-bind="textInput: contactMail2"/>  
                </div>
                
                <div class="form-group">
                    <label>TELEFON</label>
                    <input type="text" class="form-control" data-bind="textInput: contactPhone"/>  
                </div>
                
                <hr class="mt-5"></hr>

                <div class="container" data-bind="foreach: msgboxes">
                        <div class="alert alert-warning" data-bind="text: msg" role="alert">
                        
                        </div>
                </div>
                      
                <button class="btn btn-light" type="submit">Send søknad</button>
            </div>
        
        </div>
       </form>

       <div  class="col-md-8 offset-md-2" data-bind="visible: applicationSuccess">
        <h1 class="font-weight-bold">Kontakt og fakturainformasjon</h1>

           <div class="alert alert-success" role="alert">
                            <i class="far fa-check-circle"></i><span> Din søknad er lagret!</span>
                        </div>
                        
                </div>
        <!--<div class="mt-5"><pre data-bind="text: ko.toJSON(am, null, 2)"></pre></div> --> 
        <div class="push"></div>
    </div>
    <script type="text/javascript">
        var script = document.createElement("script"); 
        script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/application_contact.js";

        document.head.appendChild(script);
    </script>
</xsl:template>

