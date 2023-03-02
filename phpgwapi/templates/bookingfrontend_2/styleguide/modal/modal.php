<section class="container py-5">
  <!-- Button trigger modal -->
  <button type="button" class="pe-btn pe-btn--transparent navbar__section__language-selector" data-bs-toggle="modal" data-bs-target="#selectLanguage" aria-label="Velg språk">
    <img src="gfx/norway.png" alt="Norsk flagg" class="">
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
                <img src="gfx/norway.png" alt="Norsk flagg" class=""> Norsk
                <span class="choice__radio"></span>
              </label>
              <label class="choice mb-5">
                <input type="radio" name="select_language" value="english" />
                <img src="gfx/united-kingdom.png" alt="Engelsk flagg" class=""> English
                <span class="choice__radio"></span>
              </label>
              <button type="button" class="pe-btn pe-btn-primary w-auto">Lagre</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>