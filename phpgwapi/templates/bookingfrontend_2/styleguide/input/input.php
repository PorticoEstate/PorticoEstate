<section class="container py-5">
  <fieldset>
    <legend class="mb-2 text-bold text-body">Radioknapp</legend>
    <div class="row mb-4">
      <div class="col-sm-4 mb-4">
        <label class="choice">
          <input type="radio" name="hall" value="hall1"/>
            Svømmehall
            <span class="choice__radio"></span>
        </label>
      </div>
      <div class="col-sm-4 mb-4">
        <label class="choice">
          <input type="radio" name="hall" value="hall2" checked/>
            Idrettshall
            <span class="choice__radio"></span>
        </label>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend class="mb-2 text-bold text-body">Sjekkboks</legend>
    <div class="row mb-5">
      <div class="col-sm-4 mb-4">
        <label class="choice">
          <input type="checkbox" name="multiHall"/>
            Svømmehall
            <span class="choice__check"></span>
        </label>
      </div>
      <div class="col-sm-4 mb-4">
        <label class="choice">
          <input type="checkbox" name="multiHall" checked/>
            Idrettshall
            <span class="choice__check"></span>
        </label>
      </div>
    </div>
  </fieldset>
  <div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <label for="input-text-standard" class="mb-2 text-bold">Standard tekstfelt</label>
      <input type="text" class="w-100" value="Tekstfelt" id="input-text-standard" />
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex flex-column align-items-center">
      <span class="mb-2 text-bold align-self-start" id="input-text-icon">Tekstfelt med ikon</span>
      <label class="input-icon w-100" aria-labelledby="input-text-icon">
        <span class="fas fa-calendar-alt icon" aria-hidden="true"></span>
        <input type="text" value="Tekstfelt"/>
      </label>
      <div class="d-flex flex-column mt-2 text-center">
        <span>input-icon</span>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex flex-column align-items-center">
      <span class="mb-2 text-bold align-self-start" id="input-text-button">Tekstfelt med knapp</span>
      <label class="input-icon input-icon--action w-100" aria-labelledby="input-text-button">
        <input type="text" value="Tekstfelt"/>
        <button type="button" aria-label="Mer informasjon">
          <span class="fas fa-info-circle icon" title="Les mer informasjon"></span>
        </button> 
      </label>
      <div class="d-flex flex-column mt-2 text-center">
        <span>input-icon</span>
        <span>input-icon--action</span>
      </div>
    </div>
  </div>
</section>