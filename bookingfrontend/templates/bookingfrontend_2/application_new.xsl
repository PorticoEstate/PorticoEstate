<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div class="container new-application-page" id="new-application-page">
        <form action="{add_action}" data-bind='' method="POST" id='application_form' enctype='multipart/form-data'
              name="form" novalidate="true" class="needs-validationm">
            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <!-- New top part -->
                    <div class="pb-3">
                        <a class="exitBtn text-decoration-none" href="{application/frontpage_link}">
                            &lt;
                            <xsl:value-of select="php:function('lang', 'Exit to homepage')"/>
                        </a>
                        <h1 class="text-primary text-md-start text-center mb-0 pt-3">
                            <xsl:value-of select="php:function('lang', 'New application')"/>
                        </h1>
                    </div>
                    <!-- Heading replaced with building name -->
                    <h2 class="font-weight-bold mb-4">
                        <xsl:value-of select="application/building_name"/>
                    </h2>

                    <!-- Retaining the msgbox and hidden input -->
                    <div class="mb-4">
                        <xsl:call-template name="msgbox"/>
                    </div>
                    <input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId"/>

                    <!-- Retaining the original formstage -->
                    <input name="formstage" value="partial1" hidden="hidden"/>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <div class=" mb-4">
                                    <ul class="row py-2 d-flex g-2 list-unstyled" data-bind="foreach: bookableresource">
                                        <li>
                                            <label class="choice user-select-none">
                                                <input type="checkbox" name="resources[]"
                                                       data-bind="textInput: id, checked: selected"
                                                       class="form-check-input choosenResource"/>
                                                <span class="label-text" data-bind="html: name"></span>
                                                <span class="choice__check"></span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <!-- TODO:  missing priser-->
                            <div class="row mb-5">
                                <div class="row gx-3 pb-3">
                                    <div class="col d-flex ">
                                        <div class=" col bg-white rounded-small border p-2 d-flex flex-column">
                                            <h4 class="mb-1">Bygg</h4>
                                            <span>
                                                <xsl:value-of select="building/street"/>
                                            </span>
                                            <span>
                                                <xsl:value-of select="building/zip_code"/>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gx-3">
                                    <!--                            <div class="col d-flex ">-->
                                    <!--                                <div class=" col bg-white rounded-small border p-2">-->
                                    <!--                                    <h4 class="mb-1">Priser</h4>-->
                                    <!--                                    <p>Disse varierer avhengig av hvilken aktivitet som skal utføres og hvem som skal arrangerer-->
                                    <!--                                        dette-->
                                    <!--                                    </p>-->
                                    <!--                                    <button class="w-100 pe-btn pe-btn-primary ">Se priser</button>-->
                                    <!--                                </div>-->
                                    <!--                            </div>-->
                                    <div class="col  d-flex ">
                                        <div class="col bg-white rounded-small border p-2">
                                            <h4 class="mb-1">Fasiliteter</h4>
                                            <ul class="pl-1" data-bind="foreach: selectedResourcesWithFacilities">
                                                <li data-bind="foreach: facilities, visible: $parent.selectedResourcesWithFacilities().length === 1">
                                                    <span data-bind="text: name"></span>
                                                </li>

                                                <li data-bind="visible: $parent.selectedResourcesWithFacilities().length > 1">
                                                    <strong data-bind="text: resourceName"></strong>
                                                    <ul data-bind="foreach: facilities">
                                                        <li data-bind="text: name"></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--                    <div id="dropdownContainer">-->
                    <!--                        <input type="text" id="timeInput" data-bind="value: selectedTime" placeholder="Choose time..." />-->
                    <!--                        <div id="timeDropdown" data-bind="foreach: timeOptions" style="display: none;">-->
                    <!--                            <div data-bind="text: $data, click: $root.selectTime"></div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                    <time-picker params="selectedTime: startTime"></time-picker>-->


                    <!-- Select Time and Date Section -->
                    <!--					<div class="form-group">-->
                    <!--						&lt;!&ndash; Display Time Chosen &ndash;&gt;-->
                    <!--						<div class="form-group">-->
                    <!--							<span class="font-weight-bold d-block mt-2 span-label">-->
                    <!--								<xsl:value-of select="php:function('lang', 'Chosen rent period')" />-->
                    <!--							</span>-->
                    <!--							<div data-bind="foreach: date">-->
                    <!--								<div class="d-block">-->
                    <!--									<input class="datetime" required="true" name="from_[]" hidden="hidden" data-bind="value: from_"/>-->
                    <!--									<input class="datetime" required="true" name="to_[]" hidden="hidden" data-bind="value: to_"/>-->
                    <!--									<span data-bind='text: formatedPeriode'></span>-->

                    <!--									<button class="ml-2" data-bind="click: $parent.removeDate">-->
                    <!--										<i class="fas fa-minus-circle"></i>-->
                    <!--									</button>-->
                    <!--								</div>-->
                    <!--							</div>-->
                    <!--							<span id="inputTime" data-bind="if: date().length == 0" class="validationMessage applicationSelectedDates">-->
                    <!--								<xsl:value-of select="php:function('lang', 'Select a date and time')" />-->
                    <!--							</span>-->
                    <!--						</div>-->
                    <!--						<div class="form-group">-->
                    <!--							<div class="row">-->
                    <!--								&lt;!&ndash; Date Pick &ndash;&gt;-->
                    <!--								<div class="form-group col-lg-5 col-sm-12 col-12">-->
                    <!--									<div class="input-group">-->
                    <!--										<div class="input-group-prepend">-->
                    <!--											<span class="input-group-text">-->
                    <!--												<i class="far fa-calendar-alt"></i>-->
                    <!--											</span>-->
                    <!--										</div>-->
                    <!--										<input type="text" onkeydown="return false" class="bookingDate form-control datepicker-btn" data-bind="textInput: bookingDate">-->
                    <!--											<xsl:attribute name="placeholder">-->
                    <!--												<xsl:value-of select="php:function('lang', 'Date')"/>-->
                    <!--											</xsl:attribute>-->
                    <!--										</input>-->
                    <!--									</div>-->
                    <!--								</div>-->

                    <!--							</div>-->
                    <!--						</div>-->
                    <!--					</div>-->

                    <div class="form-group">

                        <div class="form-group">
                            <div class="row  pt-4">
                                <h2>
                                    Leieperiode
                                </h2>
                            </div>
                            <!-- Display Time Chosen -->
                            <div class="form-group mb-2 ">
                                <span class="font-weight-bold d-block span-label">
                                    <xsl:value-of select="php:function('lang', 'Chosen rent period')"/>
                                </span>
                                <div data-bind="foreach: date">
                                    <div class="d-block">
                                        <input class="datetime" required="true" name="from_[]" hidden="hidden"
                                               data-bind="value: from_"/>
                                        <input class="datetime" required="true" name="to_[]" hidden="hidden"
                                               data-bind="value: to_"/>
                                        <span data-bind='text: formatedPeriode'></span>
<!--                                        <pre data-bind="text: ko.toJSON($data)"></pre>-->
                                        <button class="ml-2" data-bind="click: $parent.removeDate">
                                            <i class="fas fa-minus-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <span id="inputTime" data-bind="if: date().length == 0"
                                      class="validationMessage applicationSelectedDates">
                                    <xsl:value-of select="php:function('lang', 'Select a date and time')"/>
                                </span>
                            </div>


                            <div class="form-group">
                                <!-- Other HTML code... -->

                                <div class="row">
                                    <div class="col-12 mb-4 d-flex flex-column align-items-center">
                                        <label class="input-icon w-100" aria-labelledby="input-text-icon">
                                            <span class="far fa-calendar-alt icon" aria-hidden="true"></span>
                                            <input type="text" onkeydown="return false"
                                                   class="js-basic-datepicker bookingDate"
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

                                    <time-picker
                                            class="col-5 mb-4 d-flex flex-column align-items-center"
                                            params="selectedTime: bookingStartTime, placeholderText: '{php:function('lang', 'from')}'"></time-picker>

                                    <time-picker
                                            class="col-5 mb-4 d-flex flex-column align-items-center"
                                            params="selectedTime: bookingEndTime, placeholderText: '{php:function('lang', 'to')}'"></time-picker>
                                    <!-- From Time Pick -->
                                    <!--                                <div class="col-5 mb-4 d-flex flex-column align-items-center">-->
                                    <!--                                        <label class="input-icon w-100" aria-labelledby="input-text-icon">-->
                                    <!--                                            <span class="far fa-clock icon" aria-hidden="true"></span>-->
                                    <!--                                            <input type="text" class="form-control bookingStartTime mr-2"-->
                                    <!--                                                   data-bind="textInput: bookingStartTime">-->
                                    <!--                                                <xsl:attribute name="placeholder">-->
                                    <!--                                                    <xsl:value-of select="php:function('lang', 'from')"/>-->
                                    <!--                                                </xsl:attribute>-->
                                    <!--                                            </input>-->
                                    <!--                                        </label>-->
                                    <!--                                    </div>-->
                                    <!-- To Time Pick -->
                                    <!--                                    <div class="col-5 mb-4 d-flex flex-column align-items-center">-->
                                    <!--                                        <label class="input-icon w-100" aria-labelledby="input-text-icon">-->
                                    <!--                                            <span class="far fa-clock icon" aria-hidden="true"></span>-->
                                    <!--                                            <input type="text" class="form-control bookingEndTime"-->
                                    <!--                                                   data-bind="textInput: bookingEndTime">-->
                                    <!--                                                <xsl:attribute name="placeholder">-->
                                    <!--                                                    <xsl:value-of select="php:function('lang', 'to')"/>-->
                                    <!--                                                </xsl:attribute>-->
                                    <!--                                            </input>-->
                                    <!--                                        </label>-->
                                    <!--                                    </div>-->
                                    <div class="col-2 d-flex justify-content-center">
                                        <div>
                                            <button class="btn btn-success" data-bind="click: addDate">+</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!--							<div class="row">-->
                            <!--								<fieldset>-->
                            <!--									<legend class="text-bold text-body">Ønsker du en repeterende søknad? ###</legend>-->
                            <!--									<div class="row">-->
                            <!--										<div class="col-sm-4 mb-4">-->
                            <!--											<label class="choice">-->
                            <!--												<input type="checkbox" name="multiHall"/>-->
                            <!--												<span class="invisible">placeHolder</span>-->
                            <!--												<span class="choice__check"></span>-->
                            <!--											</label>-->
                            <!--										</div>-->

                            <!--									</div>-->
                            <!--								</fieldset>-->
                            <!--							</div>-->
                        </div>
                    </div>
                    <xsl:if test="config/activate_application_articles !=''">
                        <input type="hidden" data-validation="application_articles">
                            <xsl:attribute name="data-validation-error-msg">
                                <xsl:value-of select="php:function('lang', 'Please choose at least 1 Article')"/>
                            </xsl:attribute>
                        </input>
                        <article-table params="selectedResources: selectedResources, date: date,selectedResources: selectedResources"></article-table>
                    </xsl:if>





                    <!-- Information About Event -->
                    <div class="mt-5 mb-5"/>
                    <h2 class="font-weight-bold mb-4">
                        <xsl:value-of select="php:function('lang', 'Information about the event')"/>
                    </h2>

                    <!-- Target Audience Section-->
                    <div class="col-sm-6 mb-4">
                        <label class="mb-2 text-bold" for="audienceDropdown">
                            <xsl:value-of select="php:function('lang', 'Target audience')"/>
                        </label>

                        <select class="js-select-basic" id="audienceDropdown" name="audience[]"
                                data-bind="options: audiences,
                       optionsText: 'name',
                       optionsValue: 'id',
                       value: audienceSelectedValue,
                       optionsCaption: '{php:function('lang', 'Choose target audience')}'">
                            <!-- KnockoutJS will populate this select element based on the 'audiences' array -->
                        </select>
                        <!--                        <input class="form-control" id="inputTargetAudience" required="true" type="text"-->
                        <!--                               style="display: none" name="audience[]" data-bind="value: audienceSelectedValue"/>-->
                    </div>


                    <!-- Event Name -->
                    <div class="col-12 mb-4">
                        <label for="inputEventName" class="mb-2 text-bold">
                            <xsl:value-of select="php:function('lang', 'Name for event/activity')"/>
                        </label>
                        <input required="true" id="inputEventName" type="text" class="w-100" name="name"
                               value="{application/name}">
                            <xsl:attribute name="placeholder">
                                <xsl:value-of select="config/application_description"/>
                            </xsl:attribute>
                        </input>
                    </div>

                    <!-- Organizer -->
                    <div class="col-12 mb-4">
                        <label for="inputOrganizerName" class="mb-2 text-bold">
                            <xsl:value-of select="php:function('lang', 'organizer/responsible seeker')"/>
                        </label>
                        <input required="true" id="inputOrganizerName" type="text" class="w-100" name="organizer"
                               value="{application/organizer}" placeholder="Navn på arrangør">
                            <xsl:attribute name="placeholder">
                                <xsl:value-of select="php:function('lang', 'organizer/responsible seeker')"/>
                            </xsl:attribute>
                        </input>
                    </div>

                    <!-- Homepage -->
                    <div class="col-12 mb-4">
                        <label for="homepage" class="mb-2 text-bold">
                            <xsl:value-of select="php:function('lang', 'Event/activity homepage')"/>
                        </label>
                        <input placeholder="Hjemmeside for aktiviteten/arrangementet" type="text" class="w-100"
                               name="homepage" value="{application/homepage}">
                            <xsl:attribute name="placeholder">
                                <xsl:value-of select="php:function('lang', 'Event/activity homepage')"/>
                            </xsl:attribute>
                        </input>
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-4">
                        <label for="field_description" class="mb-2 text-bold">
                            <xsl:value-of select="php:function('lang', 'Event/activity description')"/>
                        </label>
                        <textarea id="field_description" style="resize: none;" class="w-100" rows="3" name="description"
                                  value="{application/description}">
                            <xsl:attribute name="placeholder">
                                <xsl:value-of select="php:function('lang', 'write here...')"/>
                            </xsl:attribute>
                            <xsl:value-of select="application/description"/>
                        </textarea>
                    </div>

                    <xsl:if test="direct_booking !=1 and config/application_equipment !=''">
                        <div class="col-12 mb-4">
                            <label for="equipment" class="mb-2 text-bold">
                                <xsl:value-of select="config/application_equipment"/>
                            </label>
                            <textarea style="resize: none;" class="w-100" name="equipment">
                                <xsl:attribute name="placeholder">
                                    <xsl:value-of select="php:function('lang', 'Extra information for the event')"/>
                                </xsl:attribute>
                                <xsl:value-of select="application/equipment"/>
                            </textarea>
                        </div>
                    </xsl:if>


                    <!-- Estimated Number of Participants -->
                    <div class="form-group border-bottom border-2 border-top pt-4 pb-4 mt-4">
                        <div class="row pt-2 pb-2">
                            <h2>
                                <xsl:value-of select="php:function('lang', 'Estimated number of participants')"/>
                            </h2>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-4">
                                <!-- Headers for Male/Female -->
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-4">
                                        <span class="text-bold">
                                            <xsl:value-of select="php:function('lang', 'Male')"/>
                                        </span>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-bold">
                                            <xsl:value-of select="php:function('lang', 'Female')"/>
                                        </span>
                                    </div>
                                </div>

                                <!-- Data Binding Iteration -->
                                <div data-bind="foreach: agegroup">
                                    <div class="row mb-2">
                                        <span data-bind="text: id, visible: false"></span>
                                        <div class="col-3">
                                            <span class="mt-2" data-bind="text: agegroupLabel"></span>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="w-100 sm-input maleInput" data-bind=""/>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" class="w-100 sm-input femaleInput" data-bind=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Upload Attachment -->
                    <xsl:if test="config/enable_upload_attachment =1">
                        <div class="form-group">
                            <div class="row">
                                <h2>
                                    Vedlegg
                                </h2>
                                <p>
                                    <xsl:value-of select="php:function('lang', 'Upload Attachment')"/>
                                    <br/>
                                    <xsl:value-of select="php:function('lang', 'optional')"/>
                                </p>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <label for="field_name" class="w-100 pe-btn pe-btn-secondary ">
                                        <xsl:value-of select="php:function('lang', 'Upload')"/>
                                    </label>
                                    <input id='field_name' style="display: none!important" name="name"
                                           accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.txt,.pdf,.odt,.ods"
                                           type="file"
                                           class="w-100 pe-btn pe-btn-secondary "/>
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


                    <!-- Terms and Conditions -->

                    <div class="form-group border-bottom border-2 border-top pt-4 pb-4 mt-4">
                        <div class="row pt-2 pb-2">
                            <h2>
                                <xsl:value-of select="php:function('lang', 'legal condition')"/>
                            </h2>
                        </div>
                        <div class="row">
                            <fieldset>
                                <div class="row ">
                                    <div class="col-12 mb-4">
                                        <!--										<label class="choice">-->
                                        <!--											<input type="checkbox" name="multiHall"/>-->
                                        <!-- Placeholder text or leave it empty if it's populated by JavaScript -->
                                        <span id="regulation_documents"></span>
                                        <!--											<span class="choice__check"></span>-->
                                        <!--										</label>-->
                                        <span class="validationMessage" style="display: none">
                                            <!-- Additional validation message here if needed -->
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>


                    <!--                    <hr class="mt-5 mb-5"></hr>-->
                    <!-- Submit -->
                    <div id="submitContainer" class="form-group float-right text-center">
                        <button id="submitBtn" class="btn btn-light" type="submit">
                            <xsl:value-of select="php:function('lang', 'Next step')"/>
                        </button>
                        <div id="submit-error" style="display: none">
                            <xsl:value-of select="php:function('lang', 'Please fill all fields')"/>
                        </div>
                    </div>

                    <!-- Submit error modal -->
                    <!-- <div id="errorModal" class="modal fade">
                        <div class="modal-dialog modal-confirm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="icon-box">
                                        <i class="material-icons"></i>
                                    </div>
                                    <button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <h4>Ooops!</h4>
                                    <p>Something went wrong. File was not uploaded.</p>
                                    <button class="btn btn-success" data-dismiss="modal">Try Again</button>
                                </div>
                            </div>
                        </div>
                    </div>      -->
                </div>
            </div>
        </form>

                <pre data-bind="text: ko.toJSON(am, null, 2)"></pre>

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
        var lang =<xsl:value-of select="php:function('js_lang', 'article', 'Select', 'price', 'unit', 'quantity',
		 'Selected', 'Delete', 'Sum', 'unit cost')"/>;
    </script>
</xsl:template>
