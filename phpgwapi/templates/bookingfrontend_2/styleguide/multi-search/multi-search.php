<script>
$(document).ready(function () {

    //Datepicker
    $('.js-datepicker').datepicker();
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
  <div class="multisearch w-100">
    <div class="multisearch__inner multisearch__inner--no-button w-100">
      <div class="row flex-column flex-md-row mb-lg-4">
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0">
          <div class="multisearch__inner__item">
            <label for="search-event-text">Søk</label>
            <input id="search-event--text" type="text" placeholder="Søk">
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_area_large">Område</label>
            <select class="js-select-multisearch" id="id_label_area_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="id_label_location_large">Lokale</label>
            <select class="js-select-multisearch" id="id_label_location_large">
              <option value="">Velg</option>
              <option value="Stavanger kommune">Stavanger kommune</option>
              <option value="Bergen kommune">Bergen kommune</option>
            </select>
          </div>
        </div>
        <div class="col col-md-6 col-lg-3 mb-3 mb-lg-0 multisearch__inner--border">
          <div class="multisearch__inner__item">
            <label for="label_datepicker_large">Dato</label>
            <input type="text" id="label_datepicker_large" class="js-datepicker" placeholder="Velg">
          </div>
        </div>
      </div>
      <div class="row flex-column flex-md-row">
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
</section>