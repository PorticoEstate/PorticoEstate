<section class="container py-5">
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
  <div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <input type="text" class="w-100" value="Tekstfelt"/>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex flex-column align-items-center">
      <label class="input-icon w-100">
        <i class="fas fa-calendar-alt"></i>
        <input type="text" value="Tekstfelt med ikon"/>
      </label>
      <div class="d-flex flex-column mt-2 text-center">
        <span>input-icon</span>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex flex-column align-items-center">
      <label class="input-icon input-icon--action w-100">
        <input type="text" value="Tekstfelt med knapp"/>
        <button type="button" aria-label="Mer informasjon">
          <i class="fas fa-info-circle"></i>
        </button> 
      </label>
      <div class="d-flex flex-column mt-2 text-center">
        <span>input-icon</span>
        <span>input-icon--action</span>
      </div>
    </div>
  </div>
</section>