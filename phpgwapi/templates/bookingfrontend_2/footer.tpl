<footer class="footer">
    <div class="container py-4 md:px-4 footer-border-top mt-4">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <a target="_blank" rel="noopener noreferrer" href="{footer_logo_url}"><img class="footer-site-logo"
                                                                                           src="{footer_logo_img}"
                                                                                           alt="Logo"></a>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h3 class="text-body mb-1">{contact}</h3>
                <ul class="text-small list-unstyled mb-0 footer-list">
                    <li><a target="_blank" class="link-text link-text-secondary normal" rel="noopener noreferrer"
                           href="mailto:{support_email}"><span>{support_email}</span></a>
                    </li>
                    <li><a target="_blank" class="link-text link-text-secondary normal" rel="noopener noreferrer"
                           href="https://github.com/PorticoEstate/-Aktiv-Kommune-feil-forslag/issues"><span>Feilmelding (om
                                systemet)</span></a></li>
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h3 class="text-body mb-1">Aktiv kommune</h3>
                <ul class="text-small list-unstyled mb-0 footer-list">
                    <li><a target="_blank" class="link-text link-text-secondary normal" rel="noopener noreferrer"
                           href="https://www.aktiv-kommune.no/">{textaboutmunicipality}</a></li>
                    <li><a target="_blank" class="link-text link-text-secondary normal" rel="noopener noreferrer"
                           href="https://www.aktiv-kommune.no/manual/">{manual}</a></li>
                    <li><a target="_blank" class="link-text link-text-secondary normal" rel="noopener noreferrer"
                           href="{footer_privacy_link}">{privacy}</a></li>
                    {url_uustatus}
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-md-3 d-flex flex-column mb-4">
                <h3 class="text-body mb-1">{sign_in}</h3>
                <ul class="text-small list-unstyled mb-0 footer-list">
                    {user_info_view}
                    <li><a class="link-text link-text-secondary normal" href="{login_url}"><i
                                    class="fas fa-sign-in-alt"></i>{login_text}</a></li>
                    {org_info_view}
                    <li><a class="link-text link-text-secondary normal" target="_blank" rel="noopener noreferrer"
                           href="{executiveofficer_url}"><i class="fas fa-sign-in-alt"></i>{executiveofficer}</a>
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
