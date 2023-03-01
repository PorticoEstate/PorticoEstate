<section class="container py-5">
  <div class="row">
    <p class="mb-2 text-bold">Filter</p>
    <div class="col mb-4">
      <label class="filter me-4 mb-4">
        <input type="checkbox" name="filter1"/>
        <span class="filter__check">Filtervalg 1 </span>
      </label>
      <label class="filter mb-4">
        <input type="checkbox" name="filter2" checked/>
        <span class="filter__check">Filtervalgt 2</span>
      </label>
    </div>
  </div>
  <div class="row">
    <p class="mb-2 text-bold">Datovisning</p>
    <div class="col-12 mb-4">
      <fieldset>
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
    <p class="mb-2 text-bold">Filtergruppe</p>
    <div class="col-12 d-flex align-items-start">
      <fieldset>
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