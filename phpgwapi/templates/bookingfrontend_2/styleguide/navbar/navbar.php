<?php
    $site_url = "/portico/bookingfrontend";
    $headlogopath = "gfx";
    $about = $site_url;
    $faq = $site_url;
    $login_url = $site_url;

    $nav = <<<HTML
<div class="py-5">
  <nav class="navbar mb-5">
    <a href="{$site_url}" class="navbar__logo">
      <img src="{$headlogopath}/logo_aktiv_kommune_horizontal.png" alt="Aktiv kommune logo" class="navbar__logo__img">
      <img src="{$headlogopath}/logo_aktiv_kommune.png" alt="Aktiv kommune logo" class="navbar__logo__img--desktop">
    </a>
    <div class="d-flex d-lg-none">
      <button class="pe-btn nav-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft" aria-label="Åpne hovedmeny">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
    <div class="navbar__section navbar__section--right d-none d-lg-flex">
      <!-- Button trigger modal -->
      <button type="button" class="pe-btn pe-btn--transparent navbar__section__language-selector" data-bs-toggle="modal" data-bs-target="#selectLanguage" aria-label="Velg språk">
        <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class="">
        <span class="fas fa-chevron-down icon" aria-hidden="true"></span>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="selectLanguage" tabindex="-1" aria-labelledby="selectLanguage" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header border-0">
              <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center pt-0 pb-4">
              <div>
                <h3>Velg språk</h3>
                <p>Hvilket språk ønsker du?</p>
                <form class="d-flex flex-column">
                  <label class="choice mb-3">
                    <input type="radio" name="select_language" value="norwegian" checked />
                    <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class=""> Norsk
                    <span class="choice__radio"></span>
                  </label>
                  <label class="choice mb-5">
                    <input type="radio" name="select_language" value="english" />
                    <img src="{$headlogopath}/united-kingdom.png" alt="Engelsk flagg" class=""> English
                    <span class="choice__radio"></span>
                  </label>
                  <button type="button" class="pe-btn pe-btn-primary w-auto">Lagre</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ul class="list-unstyled navbar__section__links">
        <li><a href="{$about}">Hva er Aktiv kommune?</a></li>
        <li><a href="{$faq}">FAQ</a></li>
      </ul>
      <button type="button" class="pe-btn pe-btn-primary py-3">Logg inn</button>
    </div>
  </nav>
  <nav class="navbar mb-5">
    <div class="navbar__section d-none d-lg-flex">
      <button class="pe-btn nav-toggler me-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft" aria-label="Åpne hovedmeny">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <ul class="list-unstyled navbar__section__links">
        <li><a href="{$about}">Hva er Aktiv kommune?</a></li>
        <li><a href="{$faq}">FAQ</a></li>
      </ul>
    </div>
    <a href="/" class="navbar__logo">
      <img src="{$headlogopath}/logo_aktiv_kommune_horizontal.png" alt="Aktiv kommune logo" class="navbar__logo__img">
      <img src="{$headlogopath}/logo_aktiv_kommune.png" alt="Aktiv kommune logo" class="navbar__logo__img--desktop">
    </a>
    <button class="pe-btn nav-toggler d-flex d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasLeft" aria-controls="offcanvasLeft" aria-label="Åpne hovedmeny">
        <span></span>
        <span></span>
        <span></span>
      </button>
    <div class="navbar__section navbar__section--right d-none d-lg-flex">
      <!-- Button trigger modal -->
      <button type="button" class="pe-btn pe-btn--transparent navbar__section__language-selector" data-bs-toggle="modal" data-bs-target="#selectLanguage" aria-label="Velg språk">
        <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class="">
        <span class="fas fa-chevron-down icon" aria-hidden="true"></span>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="selectLanguage" tabindex="-1" aria-labelledby="selectLanguage" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header border-0">
              <button type="button" class="btn-close text-grey-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex justify-content-center pt-0 pb-4">
              <div>
                <h3>Velg språk</h3>
                <p>Hvilket språk ønsker du?</p>
                <form class="d-flex flex-column">
                  <label class="choice mb-3">
                    <input type="radio" name="select_language" value="norwegian" checked />
                    <img src="{$headlogopath}/norway.png" alt="Norsk flagg" class=""> Norsk
                    <span class="choice__radio"></span>
                  </label>
                  <label class="choice mb-5">
                    <input type="radio" name="select_language" value="english" />
                    <img src="{$headlogopath}/united-kingdom.png" alt="Engelsk flagg" class=""> English
                    <span class="choice__radio"></span>
                  </label>
                  <button type="button" class="pe-btn pe-btn-primary w-auto">Lagre</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal end -->

      <!-- Messages -->
      <button type="button" class="pe-btn pe-btn--transparent text-xl me-2" aria-label="Les meldinger">
        <span class="far fa-envelope" title="Les meldinger"></span>
      </button>
      
      <!-- Notifications -->
      <button type="button" class="pe-btn pe-btn--transparent text-xl me-4" aria-label="Les meldinger">
        <span class="far fa-bell" title="Les varsler"></i>
      </button>

      <!-- User menu -->
      <div class="js-dropdown menu position-relative">
          <button class="js-dropdown-toggler pe-btn menu__toggler" type="button" aria-expanded="false">
            <span>Hans Hansen</span>
            <span class="fas fa-play icon" aria-hidden="true"></span>
          </button>
          <div class="js-dropdown-content menu__content menu__content--navbar">
            <ul class="list-unstyled">
              <li><a href="">Mine innstillinger</a></li>
              <li><a href="">Brukerstøtte</a></li>
              <li><a href="">Gi delegat tilgang</a></li>
              <li><a href="">Kommende arrangmenter</a></li>
              <li><a href="">Tidligere bookinger</a></li>
              <li><a href="">Logg ut</a></li>
            </ul>
          </div>
        </div>
    </div>
  </nav>
</div>
HTML;

    echo $nav;