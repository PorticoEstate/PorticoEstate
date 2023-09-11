<xsl:template match="data" xmlns:php="http://php.net/xsl" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <div class="container new-application-page" id="new-application-page">
        <div class="pb-3">
            <a class="exitBtn text-decoration-none">
                <xsl:attribute name="href">
                    <xsl:value-of select="application/frontpage_link"/>
                </xsl:attribute>
                &lt;
                <xsl:value-of select="php:function('lang', 'Exit to homepage')"/>
            </a>
            <H1 class="text-primary text-md-start text-center mb-0 pt-3">#LAG SØKDNAD</H1>
        </div>

        <form action="{add_action}" data-bind='' method="POST" id='application_form' enctype='multipart/form-data'
              name="form" novalidate="true" class="needs-validationm">


            <!-- Section Lokale -->
            <h2>
                <xsl:value-of select="application/building_name"/>
            </h2>


            <div class="form-group">
                <span class="font-weight-bold d-block mt-2 span-label">
                    <xsl:value-of select="php:function('lang', 'Chosen rent object')"/>
                </span>
            </div>
            <div class=" mb-4">
                <ul class="row py-2 d-flex g-2 list-unstyled" data-bind="foreach: bookableresource">
                    <li>
                        <label class="choice user-select-none">
                            <input type="checkbox" name="resources[]" data-bind="textInput: id, checked: selected"
                                   class="form-check-input choosenResource"/>
                            <span class="label-text" data-bind="html: name"></span>
                            <span class="choice__check"></span>
                        </label>
                    </li>
                </ul>
            </div>
            <!-- TODO: Missing dynamic data fasiliteter og bygg, missing priser-->
            <div class="row mb-5">
                <div class="row gx-3 pb-3">
                    <div class="col d-flex ">
                        <div class=" col bg-white rounded-small border p-2 d-flex flex-column">
                            <h4 class="mb-1">Bygg</h4>
                            <span>Gunnar Warebergs gate 3</span>
                            <span>4009 Stavanger</span>
                        </div>
                    </div>
                </div>
                <div class="row gx-3">
                    <div class="col d-flex ">
                        <div class=" col bg-white rounded-small border p-2">
                            <h4 class="mb-1">Priser</h4>
                            <p>Disse varierer avhengig av hvilken aktivitet som skal utføres og hvem som skal arrangerer
                                dette
                            </p>
                            <button class="w-100 pe-btn pe-btn-primary ">Se priser</button>
                        </div>
                    </div>
                    <div class="col  d-flex ">

                        <div class="col bg-white rounded-small border p-2">
                            <h4 class="mb-1">Fasiliteter</h4>
                            <ul class="pl-1">
                                <li>Høytalere</li>
                                <li>Mikrofon x 2</li>
                                <li>Prosjektor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END Section lokale -->

            <!-- Section Periode -->


            <!-- Select Time and Date Section -->
            <div class="form-group">
                <div class="row  pt-4">
                    <h2>
                        Periode
                    </h2>
                </div>
                <!-- Display Time Chosen -->

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 mb-4 d-flex flex-column align-items-center">
                            <label class="input-icon w-100"
                                   aria-labelledby="input-text-icon">
                                <span class="far fa-calendar-alt icon" aria-hidden="true"></span>
                                <input type="text" onkeydown="return false" class="js-basic-datepicker"
                                       placeholder="Velg dato" id="standard-datepicker"
                                       data-bind="textInput: bookingDate">
                                    <xsl:attribute name="placeholder">
                                        <xsl:value-of select="php:function('lang', 'Date')"/>
                                    </xsl:attribute>
                                </input>
                            </label>
                        </div>
                    </div>
                    <div class="row">

                        <!-- From Time Pick -->
                        <div class="col-6 mb-4 d-flex flex-column align-items-center">
                            <label class="input-icon w-100"
                                   aria-labelledby="input-text-icon">
                                <span class="far fa-clock icon" aria-hidden="true"></span>
                                <input type="text" onkeydown="return false" class="form-control bookingStartTime mr-2"
                                       data-bind="textInput: bookingStartTime">
                                    <xsl:attribute name="placeholder">
                                        <xsl:value-of select="php:function('lang', 'from')"/>
                                    </xsl:attribute>
                                </input>
                            </label>
                        </div>
                        <!-- To Time Pick -->
                        <div class="col-6 mb-4 d-flex flex-column align-items-center">
                            <label class="input-icon w-100"
                                   aria-labelledby="input-text-icon">
                                <span class="far fa-clock icon" aria-hidden="true"></span>
                                <input type="text" onkeydown="return false" class="form-control bookingEndTime"
                                       data-bind="textInput: bookingEndTime">
                                    <xsl:attribute name="placeholder">
                                        <xsl:value-of select="php:function('lang', 'to')"/>
                                    </xsl:attribute>
                                </input>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <fieldset>
                        <legend class="text-bold text-body">Ønsker du en repeterende søknad? ###</legend>
                        <div class="row">
                            <div class="col-sm-4 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    <span class="invisible">placeHolder</span>
                                    <span class="choice__check"></span>
                                </label>
                            </div>

                        </div>
                    </fieldset>
                </div>
            </div>


            <!-- Customer Details Section -->
            <div class="form-group mt-4 mb-4">
                <div class="row">
                    <h2>
                        Dine detaljer
                    </h2>
                </div>                <!-- Display Time Chosen -->

                <div class="row">
                    <fieldset>
<!--                        <legend class="mb-2 text-bold text-body">Radioknapp</legend>-->
                        <div class="row mb-4">
                            <div class="col-6 mb-4">
                                <label class="choice">
                                    <input type="radio" name="hall" value="hall1"/>
                                    Privatperson
                                    <span class="choice__radio"></span>
                                </label>
                            </div>
                            <div class="col-6 mb-4">
                                <label class="choice">
                                    <input type="radio" name="hall" value="hall2"/>
                                    Organisasjon
                                    <span class="choice__radio"></span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <!-- TODO: Should hide if privat person -->
                <div class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Organisasjonsnummer (9 siffer) *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Arrangør *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                </div>
                <!-- end hide -->
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Adresse *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Postnr. *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Poststed *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Tlf. *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="input-text-standard" class="mb-2 text-bold">Epost *</label>
                        <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                    </div>
                </div>

                <div class="row">
                    <fieldset>
                        <legend class="mb-4 mt-4 text-bold text-body">Er avsenderen kontaktperson? *</legend>
                        <div class="row mb-4">
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="radio" name="hall" value="hall1"/>
                                    Ja
                                    <span class="choice__radio"></span>
                                </label>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="radio" name="hall" value="hall2"/>
                                    Nei, oppgi navn/tlf
                                    <span class="choice__radio"></span>
                                </label>
                            </div>
                        </div>
                        <!-- TODO: show if no is select -->
                        <div class="row mb-4">
                            <div class="col-12 col-md-6 mb-4">
                                <label for="input-text-standard" class="mb-2 text-bold">Navn</label>
                                <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label for="input-text-standard" class="mb-2 text-bold">Tlf. *</label>
                                <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard"/>
                            </div>
                        </div>
                        <!-- -->
                    </fieldset>
                </div>
            </div>

            <!-- Arrangement Details Section -->
            <div class="form-group mb-4">
                <div class="row">
                    <h2>
                        Arrangementsdetaljer
                    </h2>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <input type="text" class="w-100"
                               placeholder="Kort beskrivelse av arrangement og behov (valgfritt)"
                               id="input-text-standard"/>
                    </div>
                </div>
            </div>

            <!-- Arrangement Details Section -->
            <div class="form-group mb-4">
                <div class="row">
                    <h2>
                        Ekstrautstyr
                    </h2>
                    <p>Skal det benyttes innretninger/utstyr ved arrangementet? *</p>
                </div>
                <div class="row">
                    <fieldset>
                        <div class="row ">
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Åpen ild (fakler, grill, bål, fyrverkeri ol)
                                    <span class="choice__check"></span>
                                </label>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Flagg/banner
                                    <span class="choice__check"></span>
                                </label>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Scene
                                    <span class="choice__check"></span>
                                </label>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Telt
                                    <span class="choice__check"></span>
                                </label>
                            </div>
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Annet, spesifiser:
                                    <span class="choice__check"></span>
                                </label>
                            </div>

                        </div>
                    </fieldset>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <input type="text" class="w-100" id="input-text-standard"/>
                    </div>
                </div>
            </div>


            <!-- Terms and conditions Section -->
            <div class="form-group">
                <div class="row">
                    <h2>
                        <xsl:value-of select="php:function('lang', 'legal condition')"/>
                    </h2>
                    <p>
                        <xsl:value-of select="config/application_terms2"/>
                    </p>
                </div>
                <div class="row">
                    <fieldset>
                        <div id="regulation_documents" class="row ">
                            <!-- autogenerated ToC checkboxes -->
                        </div>
                    </fieldset>
                </div>
            </div>


            <!-- Upload Attachment -->
            <xsl:if test="config/enable_upload_attachment =1">
                <div class="form-group">
                    <div class="row">
                        <h2>
                            Vedlegg ###
                        </h2>
                        <p>
                            <xsl:value-of select="php:function('lang', 'Upload Attachment')"/>
                            <br/>
                            <xsl:value-of select="php:function('lang', 'optional')"/>
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <label for="field_name" class="w-100  pe-btn pe-btn-secondary ">
                                <xsl:value-of select="php:function('lang', 'Upload')"/>
                            </label>
                            <input id='field_name' style="display: none!important" name="name"
                                   accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.txt,.pdf,.odt,.ods" type="file"
                                   class="w-100  pe-btn pe-btn-secondary "/>
                        </div>
                    </div>
                </div>

                <div id="attachment" class="form-group">
                    <div id="show-attachment">
                        <span id="field_name_input"></span>
                        <a style="display: none" id="attachment-remove">Fjern Vedlegg</a>
                        <!-- Input -->
                    </div>
                    <!-- Remove Attachment -->

                </div>
            </xsl:if>


            <!-- GDPR Section -->
            <div class="form-group border-bottom border-2 border-top pt-4 pb-4 mt-4">
                <div class="row pt-2 pb-2">
                    <h2>
                        Samtykke til lagring av informasjon ####
                    </h2>
                </div>
                <div class="row">
                    <fieldset>
                        <div class="row ">
                            <div class="col-12 mb-4">
                                <label class="choice">
                                    <input type="checkbox" name="multiHall"/>
                                    Jeg godkjenner at informasjonen jeg har oppgitt blir lagret i henhold til
                                    personvernserklæringen
                                    <span class="choice__check"></span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

        </form>

        <pre data-bind="text: ko.toJSON(am, null, 2)">Kockout Broken!!</pre>

        <div class="push"></div>
    </div>
    <script>
        var date_format = `
        <xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/>
        `;
        var initialAcceptAllTerms = false;
        var initialSelection =<xsl:value-of select="application/resources_json"/>;
        var initialAudience =<xsl:value-of select="application/audience_json"/>;
        var initialDates =<xsl:value-of select="application/dates_json"/>;
        var initialAgegroups =<xsl:value-of select="application/agegroups_json"/>;
        var initialAcceptedDocs =<xsl:value-of select="application/accepted_documents_json"/>;
        var errorAcceptedDocs = '<xsl:value-of select="config/application_terms2"/>';
        var cache_refresh_token = `
        <xsl:value-of select="php:function('get_phpgw_info', 'server|cache_refresh_token')"/>
        `;
        var direct_booking = '<xsl:value-of select="direct_booking"/>';
        var building_id = '<xsl:value-of select="application/building_id"/>';
        var lang =`
        <xsl:value-of
                select="php:function('js_lang', 'article', 'Select', 'price', 'unit', 'quantity', 'Selected', 'Delete', 'Sum', 'unit cost')"/>
        `;
    </script>
</xsl:template>
