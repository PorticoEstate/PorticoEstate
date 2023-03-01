<script>
$(document).ready(function () {

    //Datepicker
    $('.js-datepicker').datepicker();

    $('.js-select-multiple').select2({
        theme: 'select-v2 select-v2--main-search',
        width: '100%',
    });
});

</script>

<section class="container py-5">
  <div class="multisearch w-100 mb-5">
    <div class="multisearch__inner w-100">
      <div class="row flex-column flex-md-row">
        <div class="col mb-3 mb-md-0">
          <div class="multisearch__inner__item">
            <label for="id_label_area">Område</label>
            <select class="js-select-multisearch" id="id_label_area">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col mb-3 mb-md-0">
          <div class="multisearch__inner__item multisearch__inner__item--border">
            <label for="id_label_location">Lokale</label>
            <select class="js-select-multisearch" id="id_label_location">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col mb-3 mb-md-0">
          <div class="multisearch__inner__item multisearch__inner__item--border">
            <label for="label_datepicker">Dato</label>
            <input type="text" id="label_datepicker" class="js-datepicker" placeholder="Velg">
          </div>
        </div>
      </div>
      <div class="w-100">
        <button type="button" class="pe-btn pe-btn-primary pe-btn--large w-100 mb-2 mt-md-3 d-md-none">Søk</button>
        <button type="button" class="pe-btn pe-btn-primary pe-btn--circle d-none d-md-flex multisearch__inner__icon-button"><i class="fas fa-search"></i></button>
      </div>
    </div>
  </div>
  <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <fieldset>
      <div class="filter-group align-self-start mb-4 mb-md-0">
        <label class="filter-group__item">
          <input type="radio" name="type_group" value="booking" checked>
          <span class="filter-group__item__radio">Leie</span>
        </label>
        <label class="filter-group__item">
          <input type="radio" name="type_group" value="event">
          <span class="filter-group__item__radio">Arrangement</span>
        </label>
        <label class="filter-group__item">
          <input type="radio" name="type_group" value="organization">
          <span class="filter-group__item__radio">Organisasjon</span>
        </label>
      </div>
    </fieldset>
    <button type="button" class="pe-btn pe-btn-secondary align-self-end d-none d-md-flex">
      Nullstill søk
      <i class="fas fa-undo ms-2"></i>
    </button>
  </div>
  <div class="multisearch w-100">
    
    <div class="multisearch__inner multisearch__inner--no-button w-100">
      <div class="row flex-column flex-md-row">
        <div class="col col-md-6 col-lg-3 mb-3">
          <div class="multisearch__inner__item">
            <label for="search-event-text">Søk</label>
            <input id="search-event-text" type="text" placeholder="Søk">
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_area_large">Område</label>
            <select class="js-select-multisearch" id="id_label_area_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_location_large">Lokale</label>
            <select id="id_label_location_large" class="js-select-multiple" multiple="multiple">
              <option value="Alver">Alver</option>
              <option value="Bergen">Bergen</option>
              <option value="Bærum">Bærum</option>
              <option value="Drammen">Drammen</option>
              <option value="Klepp">Klepp</option>
              <option value="Kristiansand">Kristiansand</option>
              <option value="Larvik">Larvik</option>
              <option value="Sandnes">Sandnes</option>
              <option value="Sola">Sola</option>
              <option value="Stavanger">Stavanger</option>
              <option value="Suldal">Suldal</option>
              <option value="Sunnfjord">Sunnfjord</option>
              <option value="Time">Time</option>
              <option value="Øygarden">Øygarden</option>
              <option value="Ålesund">Ålesund</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="label_datepicker_large">Dato</label>
            <input type="text" id="label_datepicker_large" class="js-datepicker" placeholder="Velg">
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0">
          <div class="multisearch__inner__item ">
            <label for="id_label_activity_large">Aktivitet</label>
            <select class="js-select-multisearch" id="id_label_activity_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_category_large">Ressurskategori</label>
            <select class="js-select-multisearch" id="id_label_category_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_res_large">Ressurser</label>
            <select class="js-select-multisearch" id="id_label_res_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_fas_large">Fasiliteter</label>
            <select class="js-select-multisearch" id="id_label_fas_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 d-flex justify-content-end my-4 mb-md-0">
        <label class="choice text-purple text-bolder">
          <input type="checkbox" name="available">
            Vis kun tilgjengelige
            <span class="choice__check"></span>
        </label>
      </div>
  <div class="d-flex d-md-none mt-3 justify-content-end">
    <button type="button" class="pe-btn pe-btn-secondary align-self-end">
      Nullstill søk
      <i class="fas fa-undo ms-2"></i>
    </button>
  </div>
</section>