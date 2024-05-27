<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <xsl:variable name="lang_details">
        <xsl:value-of select="php:function('lang', 'details')"/>
    </xsl:variable>
    <xsl:variable name="lang_name">
        <xsl:value-of select="php:function('lang', 'name')"/>
    </xsl:variable>
    <xsl:variable name="lang_unit">
        <xsl:value-of select="php:function('lang', 'unit')"/>
    </xsl:variable>
    <xsl:variable name="lang_quantity">
        <xsl:value-of select="php:function('lang', 'quantity')"/>
    </xsl:variable>
    <xsl:variable name="lang_amount">
        <xsl:value-of select="php:function('lang', 'amount')"/>
    </xsl:variable>
    <xsl:variable name="lang_tax">
        <xsl:value-of select="php:function('lang', 'tax')"/>
    </xsl:variable>
    <xsl:variable name="lang_sum">
        <xsl:value-of select="php:function('lang', 'Sum')"/>
    </xsl:variable>

    <style>
        .modal-dialog,
        .modal-content {
        /* 98% of window height */
        height: 98%;
        }

        .modal-body {
        /* 100% = dialog height, 120px = header + footer */
        max-height: calc(100vh - 210px);
        overflow-y: auto;
        }

        .select2-selection__rendered {
        line-height: 33px !important;
        }

        .select2-container .select2-selection--single {
        height: 36px !important;
        }

        .select2-selection__arrow {
        height: 35px !important;
        }
    </style>
    <div class="container new-application-page my-container-top-fix  mx-3" id="new-application-partialtwo"
         data-bind="visible: isFormVisible">
        <div class="row mb-2r mx-2">
            <div class="col-md-2">
                <a class="link-text link-text-primary">
                    <xsl:attribute name="href">
                        <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/', '')"/>
                    </xsl:attribute>
                    <i class="fa-solid fa-arrow-left"></i>
                    <xsl:value-of select="php:function('lang', 'home_page')"/>
                </a>
            </div>

        </div>
        <form action="" method="POST" id='application_form' name="form" class="needs-validation" novalidate="">
            <div class="row mb-5">
                <div class="col-md-12" data-bind="visible: !applicationSuccess()">
                    <h1 class="font-weight-bold">
                        <xsl:value-of select="php:function('lang', 'summary')"/>
                    </h1>
                    <p>
                        <xsl:value-of select="config/application_contact"/>
                    </p>
                    <hr class="divider divider-primary my-3"></hr>
                    <div class="mb-4">
                        <xsl:call-template name="msgbox"/>
                    </div>
                    <h2 class=" mb-4">
                        <xsl:value-of select="php:function('lang', 'your_applications')"/>
                    </h2>
                    <p class="validationMessage" data-bind="visible: applicationCartItems().length == 0">
                        <xsl:value-of select="php:function('lang', 'applicationcart empty')"/>
                    </p>
                    <div data-bind="visible: applicationCartItems().length != 0">
                        <div data-bind="foreach: applicationCartItems" class="mb-2r">
                            <div class="article-table-wrapper pb-0 mb-2r">
                                <div class="article-table-header">
                                    <div class="resource-name d-flex gap-2 align-items-center">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <h3 class="p-0 m-0" data-bind="text: building_name"></h3>
                                    </div>
                                    <div class="resource-price">
                                    </div>
                                    <div class="resource-expand" data-bind="click: $parent.deleteItem">
                                        <button class="btn btn-subtle" type="button">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="category-table">

                                    <div class="category-header category-article-row d-flex flex-wrap"
                                         data-bind="foreach: resources">

                                        <div class="pill pill">
                                            <i class="fa-solid fa-layer-group"></i>
                                            <div class="pill-label" data-bind="text: $data.name"></div>
                                        </div>

                                    </div>

                                    <div class="category-articles">
                                        <div class="category-article-row d-flex  flex-wrap" data-bind="foreach: dates">
                                            <time-slot-pill params="date: $data"></time-slot-pill>
                                        </div>
                                    </div>
                                    <div class="collapsible-part">
                                        <table class='table' data-bind="foreach: orders">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <xsl:value-of select="$lang_name"/>
                                                    </th>
                                                    <th>
                                                        <xsl:value-of select="$lang_unit"/>
                                                    </th>
                                                    <th>
                                                        <xsl:value-of select="$lang_amount"/>
                                                    </th>
                                                    <th>
                                                        <xsl:value-of select="$lang_tax"/>
                                                    </th>
                                                    <th>
                                                        <xsl:value-of select="$lang_quantity"/>
                                                    </th>
                                                    <th>
                                                        <xsl:value-of select="$lang_sum"/>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody data-bind="foreach: lines">
                                                <tr>
                                                    <td data-bind="text: name, style: {{'padding-left': parent_mapping_id === 0 ? '0.5rem': '1rem'}}"></td>
                                                    <td data-bind="text: lang[unit]"></td>
                                                    <td class="text-right" data-bind="text: amount.toFixed(2)"></td>
                                                    <td class="text-right" data-bind="text: tax.toFixed(2)"></td>
                                                    <td class="text-right" data-bind="text: quantity"></td>
                                                    <td class="text-right"
                                                        data-bind="text: (tax + amount).toFixed(2)"></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <xsl:value-of select="$lang_sum"/>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-right" data-bind="text: sum.toFixed(2)"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div id="total_sum_block" class="applications p-4 mb-2" style="display:none;">
                            <table class='table'>
                                <tr>
                                    <th>Total sum</th>
                                    <th class="text-right d-flex justify-content-end" id="total_sum"></th>
                                </tr>
                            </table>
                        </div>

                        <div class="form-group d-flex flex-column mb-2r">
                            <label>
                                <h3 class="mb-2r"><xsl:value-of select="php:function('lang', 'billing_information_contact_person')"/>*
                                </h3>
                            </label>
                            <input type="text" id="customer_identifier_type_hidden_field" hidden="hidden"
                                   value="{application/customer_identifier_type}"/>
                            <div class="mx-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="customer_identifier_type"
                                           id="privateRadio" data-bind="checked: typeApplicationRadio" value="ssn"
                                           required="true">
                                    </input>
                                    <label class="form-check-label" for="privateRadio">
                                        <xsl:value-of select="php:function('lang', 'Private event')"/>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="customer_identifier_type"
                                           id="orgRadio" data-bind="checked: typeApplicationRadio"
                                           value="organization_number" required="true"/>
                                    <label class="form-check-label" for="orgRadio">
                                        <xsl:value-of select="php:function('lang', 'organization')"/>
                                    </label>
                                </div>
                            </div>

                            <div class="invalid-feedback row" data-bind="visible: !typeApplicationSelected()">
                                Vennligst oppgi gyldig kundetype
                            </div>
                            <p data-bind="ifnot: typeApplicationSelected, visible: typeApplicationValidationMessage"
                               class="isSelected validationMessage">
                                <xsl:value-of select="php:function('lang', 'choose a')"/>
                            </p>
                        </div>

                        <h2 class="mt-0 mb-2r">
                            <xsl:value-of select="php:function('lang', 'billing_information')"/>
                        </h2>
                        <div class="form-group row mt-3">

                            <div class="row">
                                <div class="col-md-7 col-12">
                                    <!-- Organization Number -->
                                    <!--xsl:if test="count(delegate_data) > 0"-->
                                    <div class="form-group"
                                         data-bind="visible: typeApplicationRadio() === 'organization_number'">
                                        <select id="customer_organization_number" class=""
                                                name="customer_organization_number"
                                                data-bind="attr: {{ required: typeApplicationRadio() === 'organization_number' }}">
                                            <option></option>
                                            <xsl:for-each select="delegate_data">
                                                <option id="customer_organization_number_{id}"
                                                        value="{id}_{organization_number}">
                                                    <xsl:value-of select="organization_number"/>
                                                    [
                                                    <xsl:value-of select="name"/>
                                                    ]
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                        <div class="invalid-feedback">
                                            Vennligst velg en organisasjon.
                                        </div>
                                        <label class="mt-2">
                                            <a id="add_new_value" href="#"
                                               data-bind="click: () => addNewOrganization()">
                                                <img src="{add_img}" width="23"/>
                                                <xsl:text></xsl:text>
                                                <xsl:value-of select="php:function('lang', 'new organization')"/>
                                            </a>
                                        </label>
                                    </div>

                                    <!--/xsl:if-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 col-12">

                                    <!--xsl:if test="count(delegate_data)=0"-->
                                    <div class="form-group"
                                         data-bind="visible: typeApplicationRadio() === 'organization_number'">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'organization number')"/>*
                                        </label>
                                        <input name="customer_organization_number_fallback"
                                               value="{application/customer_organization_number}" type="text"
                                               class="form-control"
                                               data-bind="value: customerOrganizationNumberFallback, attr: {{ required: typeApplicationRadio() === 'organization_number', readonly: isOrganizationNumberReadonly }}"
                                               minlength="9" maxlength="9" pattern="[0-9]+">
                                        </input>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gyldig organisasjonsnummer.
                                        </div>
                                    </div>
                                    <div class="form-group"
                                         data-bind="visible: typeApplicationRadio() === 'organization_number'">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'organization')"/>*
                                        </label>
                                        <input name="customer_organization_name"
                                               value="{application/customer_organization_name}" type="text"
                                               class="form-control"
                                               data-bind="value: customerOrganizationName, attr: {{ required: typeApplicationRadio() === 'organization_number', readonly: isOrganizationNameReadonly }}"
                                               maxlength="150"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi organisasjonsnavn.
                                        </div>
                                    </div>
                                    <!--/xsl:if-->


                                    <!-- Customer Personal Number -->
                                    <div class="form-group" data-bind="visible: typeApplicationRadio() === 'ssn'">
                                        <xsl:if test="string-length(application/customer_ssn)=0">
                                            <label>
                                                <xsl:value-of select="php:function('lang', 'Ssn')"/>
                                                <xsl:text>*</xsl:text>
                                            </label>
                                        </xsl:if>
                                        <input class="form-control" name="customer_ssn"
                                               value="{application/customer_ssn}"
                                               required="true">
                                            <xsl:choose>
                                                <xsl:when test="application/customer_ssn != ''">
                                                    <xsl:attribute name="type">
                                                        <xsl:text>hidden</xsl:text>
                                                    </xsl:attribute>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:attribute name="type">
                                                        <xsl:text>text</xsl:text>
                                                    </xsl:attribute>
                                                    <xsl:attribute name="minlength">
                                                        <xsl:text>11</xsl:text>
                                                    </xsl:attribute>
                                                    <xsl:attribute name="maxlength">
                                                        <xsl:text>11</xsl:text>
                                                    </xsl:attribute>
                                                    <xsl:attribute name="pattern">
                                                        <xsl:text>[0-9]+</xsl:text>
                                                    </xsl:attribute>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </input>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gyldig personnummer.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-5 col-12">

                                    <!-- Contact Name -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'contact_name')"/>*
                                        </label>
                                        <input id="contactName" type="text" class="form-control" name="contact_name"
                                               value="{application/contact_name}" required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi navn.
                                        </div>
                                    </div>

                                    <!-- Street Name -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'responsible_street')"/>*
                                        </label>
                                        <input type="text" class="form-control" id="field_responsible_street"
                                               name="responsible_street" value="{application/responsible_street}"
                                               required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gatenavn.
                                        </div>
                                    </div>
                                    <!-- Zip Code -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'responsible_zip_code')"/>*
                                        </label>
                                        <input type="text" minlength="4" maxlength="4" class="form-control"
                                               id="field_responsible_zip_code" name="responsible_zip_code"
                                               value="{application/responsible_zip_code}" required="true">
                                            <xsl:attribute name="pattern">
                                                <xsl:text>[0-9]+</xsl:text>
                                            </xsl:attribute>
                                        </input>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gyldig postnummer.
                                        </div>
                                    </div>
                                    <!-- City -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'responsible_city')"/>*
                                        </label>
                                        <input type="text" class="form-control" id="field_responsible_city"
                                               name="responsible_city" value="{application/responsible_city}"
                                               required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi poststed.
                                        </div>
                                    </div>
                                    <!-- Email -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'contact_email')"/>*
                                        </label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                                               value="{application/contact_email}" required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gyldig e-post.
                                        </div>
                                    </div>
                                    <!-- Confirm Email -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'Confirm e-mail address')"/>*
                                        </label>
                                        <input type="email" class="form-control" id="contact_email2"
                                               name="contact_email2"
                                               value="{application/contact_email2}" required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst bekreft e-posten din.
                                        </div>
                                    </div>
                                    <!-- Phone -->
                                    <div class="form-group">
                                        <label>
                                            <xsl:value-of select="php:function('lang', 'contact_phone')"/>*
                                        </label>
                                        <input type="number" class="form-control" name="contact_phone"
                                               value="{application/contact_phone}" required="true"/>
                                        <div class="invalid-feedback">
                                            Vennligst oppgi gyldig telefonnummer.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!--						<hr class="mt-5"></hr>-->
                        <div class="row  d-flex justify-content-end">
                            <div class="btn-group col-2" id="btnSubmitGroup">
                                <button
                                        class=" pe-btn pe-btn-primary  align-items-center gap-2"
                                        type="button" id="btnSubmit">
                                    <div class="text-bold">
                                        <xsl:choose>
                                            <xsl:when test="count(payment_methods) > 0">
                                                Fakturering
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:value-of select="php:function('lang', 'submit_application')"/>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </div>
                                    <div class="text-bold d-flex align-items-center">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                </button>
                                <div id="external_payment_method">
                                    <xsl:if test="count(payment_methods) > 0">
                                        <xsl:for-each select="payment_methods">
                                            <img src="{logo}" class="ml-5" OnClick="initiate_payment('{method}');">
                                            </img>
                                        </xsl:for-each>
                                    </xsl:if>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
        <div class="push"></div>
        <!-- MODAL INSPECT EQUIPMENT START -->
        <div class="modal fade" id="new_organization">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 id="inspection_title" class="modal-title">
                            <xsl:value-of select="php:function('lang', 'new organization')"/>
                        </h4>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div style="width: 100%">
                            <iframe id="iframeorganization" src="" frameborder="0" scrolling="yes" marginheight="0"
                                    marginwidth="0">
                                Wait for it...
                            </iframe>
                        </div>
                        <br/>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button"
                                class="pe-btn pe-btn-colour-secondary d-flex gap-3 align-items-center pe-btn--small"
                                data-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                            <xsl:value-of select="php:function('lang', 'Cancel')"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL INSPECT EQIPMENT END -->
    </div>
    <script>
        var initialAcceptAllTerms = true;
        var initialSelection = [];
        var lang =
        <xsl:value-of
                select="php:function('js_lang', 'Do you want to delete application?', 'Send', 'each', 'kg', 'm', 'm2', 'hour', 'day')"/>
        ;
        var payment_order_id = '<xsl:value-of select="payment_order_id"/>';
        var selected_payment_method = '<xsl:value-of select="selected_payment_method"/>';

        $('#new_organization').on('shown.bs.modal', function (e)
        {
        var src_organization = phpGWLink('bookingfrontend/', {menuaction: 'bookingfrontend.uiorganization.add',
        nonavbar: true} );
        $("#iframeorganization").attr("src", src_organization);
        });

        $('#new_organization').on('hidden.bs.modal', function (e)
        {
        // alert on insufficient rights
        if(!i_have_already_told_you)
        {
        location.reload();
        }
        });
    </script>
</xsl:template>
