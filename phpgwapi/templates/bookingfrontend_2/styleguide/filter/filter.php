<section class="container py-5">
  <p class="mb-2 text-bold">Filter</p>
  <div class="row">
    <div class="col mb-5">
      <label class="filter me-4 mb-2">
        <input type="checkbox" name="filter1"/>
        <span class="filter__check">Filtervalg 1 </span>
      </label>
      <label class="filter mb-2">
        <input type="checkbox" name="filter2" checked/>
        <span class="filter__check">Filtervalgt 2</span>
      </label>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-5">
      <fieldset>
        <legend class="mb-2 text-bold text-body">Datovisning</legend>
        <label class="filter">
          <input type="radio" name="filter" value="day" checked/>
            <span class="filter__radio">Dag</span>
        </label>
        <label class="filter">
          <input type="radio" name="filter" value="week"/>
            <span class="filter__radio">Uke</span>
        </label>
        <label class="filter">
          <input type="radio" name="filter" value="moth"/>
            <span class="filter__radio">Måned</span>
        </label>
      </fieldset>
    </div>
  </div>
  <div class="row d-flex flex-column ">
    <div class="col-12 d-flex align-items-start">
      <fieldset>
        <legend class="mb-2 text-bold text-body">Filtergruppe</legend>
        <div class="filter-group">
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
    </div>
  </div>
</section>