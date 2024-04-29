<div class="booking-cart" id="applications-cart-content"
        data-bind="visible: applicationCartItems().length > 0 && visible">
        <div class="booking-cart-title" data-bind="click: toggleCartExpansion">
            <span class="font-weight-bold">{cart_header} </span><span
                    data-bind="text: '('+applicationCartItems().length+')'"></span>
            <i class="booking-cart-icon float-right mr-2"
               data-bind="css: { 'far fa-window-minimize': cartElementExpanded(), 'fas fa-plus': !cartElementExpanded() }"></i>
        </div>
        <div class="booking-cart-items" data-bind="visible: cartElementExpanded">
            <div data-bind="foreach: applicationCartItems">
                <div class="booking-cart-item">
                    <div class="row">
                        <div class="col-5" data-bind="text: building_name"></div>
                        <div class="col-5 d-inline" data-bind="foreach: resources"><span class="mr-3"
                                                                                         data-bind="text: name"></span>
                        </div>
                        <div class="col-2 float-right"><span data-bind="click: $parent.deleteItem"
                                                             class="far fa-trash-alt mr-2"></span></div>
                    </div>
                    <div class="row" data-bind="foreach: dates">
                        <div class="col-5" data-bind="text: date"></div>
                        <div class="col-6" data-bind="text: 'kl. ' + periode"></div>
                    </div>
                </div>
            </div>
            <div class="m-2">
                <button onclick="window.location.href = phpGWLink('bookingfrontend/', {menuaction:'bookingfrontend.uiapplication.add_contact' }, false)"
                        class="btn btn-light m-2">
                    {cart_complete_application}
                </button>
                <button onclick="window.location.href = phpGWLink('bookingfrontend/', {}, false)"
                        class="btn btn-light m-2">
                    {cart_add_application}
                </button>
            </div>
        </div>
</div>
<footer class="footer">
    <div class="container py-4 md:px-4 footer-border-top mt-4">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <a target="_blank" rel="noopener noreferrer" href="{footer_logo_url}"><img class="footer-site-logo"
                                                                                           src="{footer_logo_img}"
                                                                                           alt="Logo"></a>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h4 class="text-body mb-1">{contact}</h4>
                <ul class="text-small list-unstyled mb-0">
                    <li><a target="_blank" rel="noopener noreferrer" href="mailto:{support_email}">{support_email}</a>
                    </li>
                    <li><a target="_blank" rel="noopener noreferrer"
                           href="https://github.com/PorticoEstate/-Aktiv-Kommune-feil-forslag/issues">Feilmelding (om
                            systemet)</a></li>
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h4 class="text-body mb-1">Aktiv kommune</h4>
                <ul class="text-small list-unstyled mb-0">
                    <li><a target="_blank" rel="noopener noreferrer"
                           href="https://www.aktiv-kommune.no/">{textaboutmunicipality}</a></li>
                    <li><a target="_blank" rel="noopener noreferrer"
                           href="https://www.aktiv-kommune.no/manual/">{manual}</a></li>
                    <li><a target="_blank" rel="noopener noreferrer" href="{footer_privacy_link}">{privacy}</a></li>
                    {url_uustatus}
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h4 class="text-body mb-1">{sign_in}</h4>
                <ul class="text-small list-unstyled mb-0">
                    {user_info_view}
                    <li><i class="fas fa-sign-in-alt me-1"></i><a href="{login_url}">{login_text}</a></li>
                    {org_info_view}
                    <li><i class="fas fa-sign-in-alt me-1"></i><a target="_blank" rel="noopener noreferrer"
                                                                  href="{executiveofficer_url}">{executiveofficer}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</div>
<script>
    var footerlang = {"Do you want to delete application?": "{cart_confirm_delete}"};
</script>
{javascript_end}
</body>
</html>
