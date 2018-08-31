<xsl:template match="data" xmlns:php="http://php.net/xsl">


    <div class="container new-application-page">
        <form action="" data-bind='submit: postApplication' method="POST" id='application_form' name="form">
        <div class="col-md-8 offset-md-2">

            <h1 class="font-weight-bold">Kontakt og fakturainformasjon</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>

            <hr class="mt-5 mb-5"></hr>
            
            <h5 class="font-weight-bold mb-4">Søknader</h5>
            <div class="applications p-2 mb-2">
                <div class="row">
                    <span class="col-6">Stavanger idrettshall</span>
                    <span class="col-4">Sal A, Sal B</span>
                    <div class="col-2 text-right">
                        <a href="" class="far fa-trash-alt mr-2"></a>
                        <a href="" class="far fa-edit"></a>
                    </div>
                </div>
                <div class="row">
                    <span class="col-6">20 feb 2018</span>
                    <span class="col-6">17:00 - 19:00</span>
                </div>
                <span>Repeter ukentlig</span>
            </div>
            
            <hr class="mt-5 mb-5"></hr>
            
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typeApplicationRadio" id="privateRadio" data-bind="checked: typeApplicationRadio" value="private"/>
                <label class="form-check-label" for="privateRadio">Privat</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typeApplicationRadio" id="orgRadio" data-bind="checked: typeApplicationRadio" value="org"/>
                <label class="form-check-label" for="orgRadio">Organisasjon</label>
            </div>
            <p data-bind="ifnot: typeApplicationSelected, visible: typeApplicationValidationMessage" class="isSelected validationMessage">Ingen ressurs valgt!</p>

            
            <div class="form-group" data-bind="visible: typeApplicationRadio() === 'org'">
                <label>ORG.NUMMER</label>
                <input type="text" class="form-control" data-bind="textInput: orgnr"/>  
            </div>
            
            <div class="form-group" data-bind="visible: typeApplicationRadio() === 'private'">
                <label>FØDSELSNUMMER</label>
                <input type="text" class="form-control" data-bind="textInput: personnr"/>  
            </div>
            
            <div class="form-group">
                <label>NAVN</label>
                <input type="text" class="form-control" data-bind="textInput: contactName"/>  
            </div>
            
            <div class="form-group">
                <label>E-POST</label>
                <input type="text" class="form-control" data-bind="textInput: contactMail"/>  
            </div>
            
            <div class="form-group">
                <label>TELEFON</label>
                <input type="text" class="form-control" data-bind="textInput: contactPhone"/>  
            </div>
            
            <hr class="mt-5 mb-5"></hr>
            
            <div class="form-group">
                <label><xsl:value-of select="php:function('lang', 'Attachment')" /></label>
                <input type="file" data-bind="textInput: attachment"/>  
            </div>
                                
            
            
            <button class="btn btn-light" type="submit">Send søknad</button>
        
        </div>
       </form>
        <div class="mt-5"><pre data-bind="text: ko.toJSON(am, null, 2)"></pre></div>  
        <div class="push"></div>
    </div>
    <script type="text/javascript">
        var script = document.createElement("script"); 
        script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/application_new_confirm.js";

        document.head.appendChild(script);
    </script>
</xsl:template>

