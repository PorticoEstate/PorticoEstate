<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div class="container new-application-page" id="new-application-page">
        <form action="{add_action}" data-bind='' method="POST" id='application_form' enctype='multipart/form-data'
              name="form" novalidate="true" class="needs-validationm">
            <div class="row">
                <div class="col-md-10 offset-md-1">

                    <!-- New top part -->
                    <!--                    <div class="pb-3">-->
                    <!--                        <a class="exitBtn text-decoration-none" href="{application/frontpage_link}">-->
                    <!--                            &lt;-->
                    <!--                            <xsl:value-of select="php:function('lang', 'Exit to homepage')"/>-->
                    <!--                        </a>-->
                    <!--                        <h1 class="text-primary text-md-start text-center mb-0 pt-3">-->
                    <!--                            <xsl:value-of select="php:function('lang', 'New application')"/>-->
                    <!--                        </h1>-->
                    <!--                    </div>-->
                    <!-- Heading replaced with building name -->
                    <div data-bind="visible: formStep() === 0">

                        <!-- Heading with title -->
                        <div class="row gx-3">
                            <div class="col d-flex flex-column">
                                <div class="font-weight-bold gap-3 d-flex align-items-center">
                                    <h1>
                                        <xsl:value-of
                                                select="php:function('lang', 'New application')"/>
                                    </h1>
                                </div>
                            </div>
                        </div>

                        <div class="row gx-3  mb-4">
                            <div class="col d-flex flex-column">
                                <div class="font-weight-bold gap-3 d-flex mb-1">
                                    <h3 class="m-0 fa-solid fa-location-dot line-height-h1"></h3>
                                    <div class="d-flex flex-column gap-1">
                                        <h2 class="m-0">
                                            <xsl:value-of select="building/name"/>
                                        </h2>

                                    </div>

                                </div>
                                <map-modal>
                                    <span>
                                        <xsl:value-of select="building/street"/>,
                                    </span>
                                    <span>
                                        <xsl:value-of select="building/zip_code"/>
                                    </span>
                                    <span style="display: none">
                                        <xsl:value-of select="building/city"/>
                                    </span>
                                </map-modal>
                                <div class="text-overline">
                                    <xsl:value-of select="building/part_of_town"/>
                                </div>
                            </div>
                        </div>
                        <!-- Retaining the msgbox and hidden input -->
                        <div class="mb-2">
                            <xsl:call-template name="msgbox"/>
                        </div>
                        <input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId"/>

                        <!-- Retaining the original formstage -->
                        <input name="formstage" value="partial1" hidden="hidden"/>

                        <!-- Modal -->
                        <div class="modal fade" id="facilities" tabindex="-1" aria-labelledby="facilities"
                             aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">

                                    <div class="modal-body d-flex flex-column pt-0 pb-4">
                                        <h2 class="mb-1" style="font-weight:400">Fasiliteter</h2>
                                        <p class="mb-1 small">Fasiliteter som er tilgjengelige på utleieobjektene du har
                                            valgt.
                                        </p>
                                        <div data-bind="foreach: selectedResourcesWithFacilities">
                                            <div class="">
                                                <div class="border-bottom p-2" style="font-size: 1.25rem"
                                                     data-bind="text: resourceName"></div>
                                                <div data-bind="foreach: facilities">
                                                    <div class="border-bottom p-2"
                                                         style="padding-left: 1.5rem!important"
                                                         data-bind="text: name"></div>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="pe-btn pe-btn-primary text-grey-light"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close">Ok
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 mb-4">
                            <div class="font-weight-bold gap-3 d-flex align-items-center">
                                <h3 class="fa-solid fa-layer-group m-0 text-bold"></h3>
                                <h3 class="m-0 text-bold">
                                    <xsl:value-of
                                            select="php:function('lang', 'rent object')"/>
                                </h3>
                            </div>
                            <label class="mb-2 d-flex align-items-center" for="select-multiple">
                                <h4 class="m-0">
                                    <xsl:value-of
                                            select="php:function('lang', 'add_rent_object')"/>
                                </h4>    <!-- Button trigger modal -->
                                <button type="button"
                                        class="pe-btn pe-btn--transparent navbar__section__language-selector p-0"
                                        data-bs-toggle="modal" data-bs-target="#facilities" aria-label="Velg språk">
                                    <svg viewBox="0 0 48 48" class="font-size-h4" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h48v48h-48z" fill="none"/>
                                        <path d="M22 34h4v-12h-4v12zm2-30c-11.05 0-20 8.95-20 20s8.95 20 20 20 20-8.95 20-20-8.95-20-20-20zm0 36c-8.82 0-16-7.18-16-16s7.18-16 16-16 16 7.18 16 16-7.18 16-16 16zm-2-22h4v-4h-4v4z"/>
                                    </svg>
                                </button>
                            </label>
                            <div id="select-multiple-container">
                                <div data-bind="foreach: bookableResource"
                                     class="d-flex flex-row gap-1 flex-wrap pb-2 w-100">
                                    <div class="pill pill--secondary" data-bind="visible: $data.selected">
                                        <!--                            <div class="pill-label" data-bind="text: $data"></div>-->
                                        <!--                            <div class="pill-divider"></div>-->
                                        <div class="pill-label" data-bind="text: $data.name"></div>
                                        <button class="pill-icon" data-bind="click: $parent.removeRessource">
                                            <i class="pill-cross"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 resource-select">
                                    <select class="js-select-multiple-items " data-bind="foreach: bookableResource"
                                            id="select-multiple">
                                        <xsl:attribute name="data-placeholder">
                                            <xsl:value-of select="php:function('lang', 'add_rent_object')"/>
                                        </xsl:attribute>
                                        <option></option>
                                        <option data-bind="text: name,
                       value: id,
                       attr: {{ 'aria-selected': selected }}
                       "/>
                                    </select>
                                </div>
                            </div>
                            <div id="select-multiple-error" class="invalid-feedback"></div>
                        </div>
                        <div class=".selected-items-display"></div>


                        <!--                    <span data-bind="text: ko.toJSON(bookableresource)"></span>-->

                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group" style="display:none">
                                    <div class=" mb-4">
                                        <ul class="row py-2 d-flex g-2 list-unstyled"
                                            data-bind="foreach: bookableResource">
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
                        </div>


                        <div class="form-group mb-3" id="date-time-selection">

                            <div class="form-group">
                                <div class="font-weight-bold gap-3 d-flex align-items-center">
                                    <h3 class="fas fa-calendar-alt m-0 text-bold"></h3>
                                    <h3 class="m-0 text-bold">
                                        <xsl:value-of
                                                select="php:function('lang', 'rent period')"/>
                                    </h3>
                                </div>
                                <!-- Display Time Chosen -->
                                <div class="form-group mb-2 ">
                                    <span class="d-block span-label">
                                        <h4>
                                            <xsl:value-of select="php:function('lang', 'Chosen rent period')"/>
                                        </h4>
                                    </span>
                                    <div data-bind="foreach: date" class="d-flex flex-row gap-1 flex-wrap">
                                        <input class="datetime" required="true" name="from_[]" hidden="hidden"
                                               data-bind="value: from_"/>
                                        <input class="datetime" required="true" name="to_[]" hidden="hidden"
                                               data-bind="value: to_"/>
                                        <time-slot-pill
                                                params="date: $data, removeDate: () => $parent.removeDate($data), selectedResources: $parent.selectedResources, schedule: $parent.schedule"></time-slot-pill>

                                        <!--                                    <pre data-bind="text: ko.toJSON($data)"></pre>-->

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
                                                       id="standard-datepicker"
                                                       data-bind="textInput: bookingDate">
                                                    <xsl:attribute name="placeholder">
                                                        <xsl:value-of select="php:function('lang', 'add_date')"/>
                                                    </xsl:attribute>
                                                    <!--                                                    Velg dato-->

                                                </input>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <time-picker
                                                class="col-6 mb-4 d-flex flex-column align-items-center"
                                                params="selectedTime: bookingStartTime, placeholderText: '{php:function('lang', 'from')}'"></time-picker>

                                        <time-picker
                                                class="col-6 mb-4 d-flex flex-column align-items-center"
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
                                    </div>
                                    <div class="row">
                                        <div class="d-flex justify-content-start">
                                            <div>
                                                <button class="pe-btn pe-btn-secondary" data-bind="click: addDate" type="button">+
                                                    <xsl:value-of select="php:function('lang', 'add_rental_period')"/>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div id="date-time-selection-error" class="invalid-feedback"></div>
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
                            <div class="row gx-3">
                                <div class="col d-flex flex-column">
                                    <div class="font-weight-bold gap-3 d-flex align-items-center">
                                        <h3 class="fas fa-shapes m-0 text-bold"></h3>
                                        <h3 class="m-0 text-bold">
                                            <xsl:value-of select="php:function('lang', 'Articles')"/>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <article-table
                                    params="selectedResources: selectedResourcesOld, date: date"></article-table>
                            <!--                        <article-table-old-->
                            <!--                                params="selectedResources: selectedResourcesOld, date: date"></article-table-old>-->
                        </xsl:if>


                        <!-- END STEP 1-->
                    </div>
                    <div data-bind="visible: formStep() === 1">
                        <!-- START STEP 2-->

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
                            <div id="audienceDropdown-error" class="invalid-feedback"></div>
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
                            <div id="inputEventName-error" class="invalid-feedback"></div>
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
                            <div id="inputOrganizerName-error" class="invalid-feedback"></div>
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
                            <textarea id="field_description" style="resize: none;" class="w-100" rows="3"
                                      name="description"
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
                        <div class="form-group border-bottom border-2 border-top pt-4 pb-4 mt-4" id="participants-container">
                            <div class="row pt-2 pb-2">
                                <h2>
                                    <xsl:value-of select="php:function('lang', 'Estimated number of participants')"/>
                                </h2>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <!-- Headers for Male/Female -->
                                    <div class="row  mb-3">
                                        <div class="col-3">
                                            <span class="text-bold">
                                                <xsl:value-of select="php:function('lang', 'agegroup')"/>
                                            </span>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-bold">
                                                <xsl:value-of select="php:function('lang', 'number of')"/>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Data Binding Iteration -->
                                    <div data-bind="foreach: agegroupList">
                                        <div class="row mb-2">
                                            <span data-bind="text: id, visible: false"></span>
                                            <div class="col-3 d-flex align-items-center">
                                                <span data-bind="text: agegroupLabel"></span>
                                            </div>
                                            <div class="col-md-4 col-8">
                                                <input type="text" class="w-100 sm-input maleInput" data-bind=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="participants-container-error" class="invalid-feedback"></div>

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

                        <div class="form-group  pt-4 pb-4 mt-4">
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
                                            <div id="regulation_documents-error" class="invalid-feedback"></div>

                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                        </div>
                        <!-- END STEP 2 -->
                    </div>


                    <!--                    <hr class="mt-5 mb-5"></hr>-->
                    <!--                    &lt;!&ndash; Submit &ndash;&gt;-->
                    <!--                    <div id="submitContainer" class="form-group float-right text-center">-->
                    <!--                        <button id="submitBtn" class="btn btn-light" type="submit">-->
                    <!--                            <xsl:value-of select="php:function('lang', 'Next step')"/>-->
                    <!--                        </button>-->
                    <!--                        <div id="submit-error" style="display: none">-->
                    <!--                            <xsl:value-of select="php:function('lang', 'Please fill all fields')"/>-->
                    <!--                        </div>-->
                    <!--                    </div>-->


                    <div id="submitContainer" class="d-flex gap-4 justify-content-between">

                        <div>
                            <button id="backBTN"
                                    class=" pe-btn pe-btn-secondary align-items-center gap-2"
                                    data-bind="visible: formStep() !== 0, click: () => goPrev()">
                                <div class="text-bold d-flex align-items-center">
                                    <i class="fa-solid fa-arrow-left-long"></i>
                                </div>
                                <div class="text-bold">
                                    <xsl:value-of select="php:function('lang', 'Previous step')"/>
                                </div>

                            </button>
                        </div>
                        <div class="d-flex gap-4 justify-content-end">
                            <a class="pe-btn pe-btn-secondary  d-flex align-items-center gap-2"
                               style="width: fit-content" href="{application/frontpage_link}">
                                <div class="text-bold">
                                    <xsl:value-of select="php:function('lang', 'exit to homepage')"/>
                                </div>
                            </a>
<!--                            <button id="submitBtn"-->
<!--                                    class=" pe-btn pe-btn-primary  align-items-center gap-2"-->
<!--                                    type="submit" data-bind="visible: formStep() === 1">-->
<!--                                <div class="text-bold">-->
<!--                                    <xsl:value-of select="php:function('lang', 'Next step')"/>-->
<!--                                </div>-->
<!--                                <div class="text-bold d-flex align-items-center">-->
<!--                                    <i class="fa-solid fa-arrow-right-long"></i>-->
<!--                                </div>-->
<!--                            </button>-->
                            <button id="nextBTN"
                                    class=" pe-btn pe-btn-primary align-items-center gap-2"
                                    data-bind="click: () => goNext()">
                                <div class="text-bold">
                                    <xsl:value-of select="php:function('lang', 'Next step')"/>
                                </div>
                                <div class="text-bold d-flex align-items-center">
                                    <i class="fa-solid fa-arrow-right-long"></i>
                                </div>
                            </button>
                        </div>
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

        <!--        <pre data-bind="text: ko.toJSON(am, null, 2)"></pre>-->

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
