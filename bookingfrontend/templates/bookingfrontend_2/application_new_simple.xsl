<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div class="container new-application-page" id="new-application-page">
        <form action="{add_action}" method="POST" id='application_form' enctype='multipart/form-data'
              name="form" novalidate="true" class="needs-validationm">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <!-- Heading with building name -->
                    <div class="row gx-3">
                        <div class="col d-flex flex-column">
                            <div class="font-weight-bold gap-3 d-flex align-items-center">
                                <h1>
                                    Ny søknad
                                </h1>
                            </div>
                        </div>
                    </div>


                    <div class="row gx-3  mb-4">
                        <div class="col d-flex flex-column">
                            <div class="font-weight-bold gap-3 d-flex mb-1">
                                <h2 class="m-0 fa-solid fa-layer-group line-height-h1"></h2>
                                <div>
                                    <h2 class="m-0" data-bind="text: selectedResource()?.name">
                                    </h2>
                                    <div>
                                        <xsl:value-of select="building/street"/>,
                                        <xsl:value-of select="building/zip_code"/>
                                    </div>
                                    <div>
                                        <xsl:value-of select="building/part_of_town"/>
                                    </div>
                                    <a class="link-text link-text-secondary">
                                        <xsl:attribute name="href">
                                            <xsl:value-of select="building/link"/>
                                        </xsl:attribute>
                                        <i class="fa-solid fa-location-dot"></i>
                                        <xsl:value-of
                                                select="building/name"/>
                                        <!--<i class="fa-solid fa-arrow-right"></i>-->
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Select Time and Date Section -->
                    <div class="form-group mb-3">

                        <div class="form-group">
                            <div class="font-weight-bold gap-3 d-flex align-items-center">
                                <h3 class="fas fa-calendar-alt m-0 text-bold"></h3>
                                <h3 class="m-0 text-bold">
                                    Leieperiode
                                </h3>
                            </div>
                            <!-- Display Time Chosen -->
                            <div class="form-group mb-2 ">
                                <span class="font-weight-bold d-block span-label">
                                    <xsl:value-of select="php:function('lang', 'Chosen rent period')"/>
                                </span>

                                <!--                                <span class="selectedItems" id="selected_period"></span>-->
                                <input class="datetime" id="from_" type="hidden" required="true" name="from_[]"/>
                                <input class="datetime" id="to_" type="hidden" required="true" name="to_[]"/>
                                <div data-bind="foreach: date" class="d-flex flex-row gap-1 flex-wrap">
                                    <input class="datetime" required="true" name="from_[]" hidden="hidden"
                                           data-bind="value: from_"/>
                                    <input class="datetime" required="true" name="to_[]" hidden="hidden"
                                           data-bind="value: to_"/>
                                    <time-slot-pill params="date: $data"></time-slot-pill>

                                    <!--                                    <pre data-bind="text: ko.toJSON($data)"></pre>-->

                                </div>
                                <!--                                    <pre data-bind="text: ko.toJSON($data)"></pre>-->

                                <!--                     -->
                            </div>

                            <div class="form-group">
                                <div id="time_select" class="row" style="display: none">
                                    <!-- Date Pick -->
                                    <div class="form-group col-lg-5 col-sm-12 col-12">
                                        <span id="lang_date" class="mb-2" style="font-weight: bold; color: #cc3300">
                                            <xsl:value-of select="php:function('lang', 'Date')"/>
                                        </span>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input id="start_date" type="text" onkeydown="return false"
                                                   class="bookingDate form-control datepicker-btn">
                                                <xsl:attribute name="placeholder">
                                                    <xsl:value-of select="php:function('lang', 'Date')"/>
                                                </xsl:attribute>
                                            </input>
                                        </div>
                                    </div>
                                    <!-- From Time Pick -->
                                    <div class="form-group col-lg-3 col-sm-6 col-6">
                                        <span id="lang_checkin" class="mb-2" style="font-weight: bold; color: #cc3300">
                                            Innsjekk
                                        </span>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-clock"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="bookingStartTime" onkeydown="return false"
                                                   disabled="disabled" class="form-control mr-2">
                                                <xsl:attribute name="placeholder">
                                                    <xsl:value-of select="php:function('lang', 'from')"/>
                                                </xsl:attribute>
                                            </input>
                                        </div>
                                    </div>
                                    <!-- To Time Pick -->
                                    <div class="form-group col-lg-3 col-sm-6 col-6">
                                        <span id="lang_checkout" class="mb-2" style="font-weight: bold; color: #cc3300">
                                            Utsjekk
                                        </span>

                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-clock"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="bookingEndTime" onkeydown="return false"
                                                   disabled="disabled" class="form-control">
                                                <xsl:attribute name="placeholder">
                                                    <xsl:value-of select="php:function('lang', 'to')"/>
                                                </xsl:attribute>
                                            </input>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <xsl:if test="config/activate_application_articles !=''">
                        <div class="mb-3">
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
                                            Artikkler
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <article-table
                                    params="selectedResources: selectedResources, date: date"></article-table>
                            <!--                        <article-table-old-->
                            <!--                                params="selectedResources: selectedResourcesOld, date: date"></article-table-old>-->
                        </div>
                    </xsl:if>


                    <!-- Message Box and Hidden Input -->
                    <div class="mb-2">
                        <xsl:call-template name="msgbox"/>
                    </div>
                    <input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId"/>
                    <input name="formstage" value="partial2" hidden="hidden"/>

                    <!--                    &lt;!&ndash; Resource Selection Section &ndash;&gt;-->
                    <!--                    <div class="col-sm-12 mb-4">-->
                    <!--                        &lt;!&ndash; Add resource selection logic here &ndash;&gt;-->
                    <!--                    </div>-->
                    <div id="resource_list" class="form-group">
                        <label>
                            <xsl:value-of select="php:function('lang', 'resources')"/>
                        </label>
                        <select id="resource_id" name="resources[]" class="form-control text-left w-100 custom-select"
                                required="true">
                            <xsl:attribute name="title">
                                <xsl:value-of select="php:function('lang', 'Choose resource')"/>
                            </xsl:attribute>
                            <xsl:if test="count(resource_list/options) > 1 ">
                                <option value="">
                                    <xsl:value-of select="php:function('lang', 'No rent object chosen')"/>
                                </option>
                            </xsl:if>
                            <xsl:apply-templates select="resource_list/options"/>
                        </select>
                    </div>
                    <div class="col-12 mt-4" id="item-description" data-bind="text: ">
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h3 class="">
                                Beskrivelse
                            </h3>
                        </div>

                        <collapsable-text params="{{ content: resourceDescription }}">

                        </collapsable-text>
                    </div>
                    <div class="row mb-4">
                        <light-box params="images: imageArray"></light-box>
                    </div>
                    <div class="row  mb-4">
                        <div class="col-sm-12">
                            <h3 class="">
                                <xsl:value-of select="php:function('lang', 'Facilities')"/>
                            </h3>
                        </div>
                        <div class="col-sm-12 d-flex flex-row flex-wrap"
                             data-bind="foreach: selectedResource()?.facilities_list">
                            <div class="col-12 col-sm-6 col-md-3" data-bind="text: name">

                            </div>
                        </div>

                    </div>
                    <!-- Target Audience Section-->
                    <input id="inputTargetAudience" required="true" type="hidden" name="audience[]"/>

                    <!-- Estimated Number of Participants -->
                    <div data-bind="foreach: agegroupList">
                        <input type="hidden" class="form-input sm-input maleInput" data-bind=""/>
                        <input type="hidden" class="form-input sm-input femaleInput" data-bind=""/>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-group pt-4 pb-4 mt-4">
                        <div class="row pt-2 pb-2">
                            <h2>
                                <xsl:value-of select="php:function('lang', 'legal condition')"/>
                            </h2>
                        </div>
                        <div class="row">
                            <fieldset>
                                <div class="row">
                                    <div class="col-12 mb-4" data-bind="foreach: termAcceptDocs">
                                        <div class="checkbox col-12 mb-4">
                                            <label class="choice d-inline d-flex gap-2 align-items-center">
                                                <input id="termsInput" type="checkbox"
                                                       data-bind="checked: checkedStatus"/>
                                                <!-- Placeholder for label text, populated by data binding -->
                                                <span class="choice__check" data-bind=""></span>
                                                <a class="d-inline termAcceptDocsUrl" target="_blank" data-bind=""></a>
                                                <i class="fas fa-external-link-alt"></i>
                                            </label>

                                        </div>
                                        <span class="validationMessage" style="display: none">
                                            <xsl:value-of select="config/application_terms2"/>
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>


                    <!-- Submit -->
                    <!--                    <div id="submitContainer" class="form-group float-right text-center">-->
                    <!--                        <button id="submitBtn" class="btn btn-light" type="submit">-->
                    <!--                            <xsl:value-of select="php:function('lang', 'Next step')"/>-->
                    <!--                        </button>-->
                    <!--                        <div id="submit-error" style="display: none">Vennligst fyll inn alle feltene!</div>-->
                    <!--                    </div>-->
                    <!-- Submit Button -->

                    <div id="submitContainer" class="d-flex gap-4 justify-content-end">
                        <a class="link-text link-text-primary pe-btn pe-btn-secondary  d-flex align-items-center gap-2"
                           style="width: fit-content" href="{application/frontpage_link}">
                            <div class="text-normal">
                                <xsl:value-of select="php:function('lang', 'exit to homepage')"/>
                            </div>
                        </a>
                        <button id="submitBtn"
                                class=" pe-btn pe-btn-primary pe-btn--large d-flex align-items-center gap-2"
                                type="submit">
                            <div class="text-normal">
                                <xsl:value-of select="php:function('lang', 'Next step')"/>
                            </div>
                            <div class="text-normal d-flex align-items-center">
                                <i class="fa-solid fa-arrow-right-long"></i>
                            </div>
                        </button>
                        <div id="submit-error" style="display: none">
                            <xsl:value-of select="php:function('lang', 'Please fill all fields')"/>
                        </div>
                    </div>


                </div>
            </div>
        </form>

        <!-- Additional Script -->
        <script>
            var date_format = '<xsl:value-of
                select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/>';
            var initialAcceptAllTerms = false;
            var initialSelection =<xsl:value-of select="application/resources_json"/>;
            var initialAudience =<xsl:value-of select="application/audience_json"/>;
            var initialDates =<xsl:value-of select="application/dates_json"/>;
            var initialAgegroups =<xsl:value-of select="application/agegroups_json"/>;
            var initialAcceptedDocs =<xsl:value-of select="application/accepted_documents_json"/>;
            var errorAcceptedDocs = '<xsl:value-of select="config/application_terms2"/>';
            var cache_refresh_token = "<xsl:value-of
                select="php:function('get_phpgw_info', 'server|cache_refresh_token')"/>";
            var lang =
            <xsl:value-of
                    select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.', 'article', 'Select', 'price', 'unit', 'quantity', 'Selected', 'Delete', 'Sum', 'unit cost')"/>
            ;
        </script>

        <div class="push"></div>
    </div>
</xsl:template>


<xsl:template match="options">
<option value="{id}">
    <xsl:if test="selected != 0">
        <xsl:attribute name="selected" value="selected"/>
    </xsl:if>
    <xsl:value-of disable-output-escaping="yes" select="name"/>
</option>
</xsl:template>

