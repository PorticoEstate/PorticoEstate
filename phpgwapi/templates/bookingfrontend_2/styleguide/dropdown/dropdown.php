<script>
  $(document).ready(function () {

    $('.js-select-basic').select2({ 
      theme: 'select-v2',
      width: '100%', 
    })

    /* Select 2 */
    $(".js-select").select2({
      width: '100%',
      templateResult: formatImageState,
    });
  
    /* Image dropdown */
    function formatImageState (state) {
      if (!state.id) {
        return state.text;
      }

      var optionImage = (state.element.value && state.element.value.length > 0) ? '<span style="width: 2rem"><img src="gfx/' + state.element.value.toLowerCase() + '.png" class="" style="max-width: 1.75rem; max-height:1.5rem;" /></span>' : '' ;
      var state = $(
        '<span class="d-flex align-items-center">' + optionImage + state.text + '</span>'
      );

      return state;
    };

    $(".js-select-image").select2({
      theme: 'select-v2',
      width: '100%',
      templateResult: formatImageState,
      templateSelection: formatImageState,
      placeholder: 'Velg kommune med bilde',
    });
  
    /* Multiselect dropdown */
    $('.js-select-multiple-items').select2({
      theme: 'select-v2',
      width: '100%',
      placeholder: 'Velg en eller flere kommuner',
      closeOnSelect: false
    });
  
    /* Dropdown f.ex. price */
    var prices = [
      {
        "id": 1,
        "text": "Privatperson",
        "price": "123 kr. per dag",
      },
      {
        "id": 2,
        "text": "Barn u/18år",
        "price": "Gratis",
      },
      {
        "id": 3,
        "text": "Idrett",
        "price": "89 kr. per dag",
      },
      {
        "id": 4,
        "text": "Organisasjon",
        "price": "89 kr. per dag",
      },
    ];

    $('.js-select-price').select2({
        theme: 'select-v2',
        width: '100%',
        data: prices,
        placeholder: 'Velg pris',
        allowClear: true,
        templateSelection: function(data){
            return $('<p class="d-flex m-0 justify-content-between align-items-center flex-auto pe-2">')
            .html('<span>' + data.text + '</span><span class="text-end"> ' + ((data.price) ? data.price : '') + ' </span>');
        },
        templateResult: function(data){
            return $('<p class="d-flex m-0 justify-content-between align-items-center ">')
            .html('<span>' + data.text + '</span><span class="text-end"> ' + ((data.price) ? data.price : '') + ' </span>');
        },
    });
  });
</script>

<section class="container py-5">
  <div class="row">
    <div class="col-sm-6 mb-4">
      <label class="mb-2 text-bold" for="select-basic">Standard</label>
      <select class="js-select-basic" id="select-basic">
        <option value="">Velg kommune</option>
        <option value="Stavanger kommune">Stavanger kommune</option>
        <option value="Bergen kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <label class="mb-2 text-bold" for="select-image">Med bilde</label>
      <select class="js-select-image" id="select-image">
        <option value="">Velg kommune</option>
        <option value="stavanger-kommune">Stavanger kommune</option>
        <option value="bergen-kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <label class="mb-2 text-bold" for="select-multiple">Flere valg</label>
      <select class="js-select-multiple-items" multiple="multiple" id="select-multiple">
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
    <div class="col-sm-6 mb-4">
      <label class="mb-2 text-bold" for="select-price">Pris</label>
      <select class="js-select-price" id="select-price">
        <option value="">Velg kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold" for="select-info">Informasjon</p>
      <div class="js-dropdown dropdown" id="select-info">
        <button class="js-dropdown-toggler dropdown__toggler" type="button" aria-expanded="false">
          Informasjon
        </button>
        <div class="js-dropdown-content dropdown__content">
          <p>
            Som innbygger kan du søke om leie av idrettsanlegg, baner, lokaler, byrom og utstyr
            <ul>
              <li>finne treningstid og arrangement</li>
              <li>finne lag og foreninger</li>
              <li>via «Min side» få</li>
              <li>oversikt over og eventuelt endre kontaktinformasjon</li>
              <li>se status for, og eventuelt endre dine søknader</li>
              <li>se eventuelle fakturaer tilknyttet ulike leieforhold</li>
            </ul>
          </p> 
        </div>
      </div>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Meny - slide</p>
      <div class="js-slidedown submenu">
        <button class="js-slidedown-toggler submenu__toggler" type="button" aria-expanded="false">
          Administrasjon
        </button>
        <div class="js-slidedown-content submenu__content">
          <ul class="list-unstyled mb-0">
            <li><a href="">Rapport</a></li>
            <li><a href="">Kundeliste</a></li>
            <li><a href="">Tjenester</a></li>
            <li><a href="">Send epost</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-12 mb-4">
      <p class="mb-2 text-bold">Informasjonskort stor</p>
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler slidedown__toggler--large" type="button" aria-expanded="false">
          <span>
          Sandnes idrettspark
        </button>
        <div class="js-slidedown-content slidedown__content">
          <p>
            Som innbygger kan du søke om leie av idrettsanlegg, baner, lokaler, byrom og utstyr
            <ul>
              <li>finne treningstid og arrangement</li>
              <li>finne lag og foreninger</li>
              <li>via «Min side» få</li>
              <li>oversikt over og eventuelt endre kontaktinformasjon</li>
              <li>se status for, og eventuelt endre dine søknader</li>
              <li>se eventuelle fakturaer tilknyttet ulike leieforhold</li>
            </ul>
          </p>
        </div>
      </div>
    </div>
    <div class="col-12 mb-4">
      <p class="mb-2 text-bold">Informasjonskort</p>
      <?php 
        $toggerInfo = ['Møterom 2', 'Ledig i valgt tid'];
      ?>
      <div class="js-slidedown slidedown">
        <button class="js-slidedown-toggler slidedown__toggler" type="button" aria-expanded="false">
          <span>Sandnes idrettspark</span>
          <span class="slidedown__toggler__info">
            <?php
              echo implode('<span class="slidedown__toggler__info__separator"><i class="fa-solid fa-circle"></i></span>', $toggerInfo);
            ?>
          </span>
        </button>
        <div class="js-slidedown-content slidedown__content">
          <p>
            Som innbygger kan du søke om leie av idrettsanlegg, baner, lokaler, byrom og utstyr
            <ul>
              <li>finne treningstid og arrangement</li>
              <li>finne lag og foreninger</li>
              <li>via «Min side» få</li>
              <li>oversikt over og eventuelt endre kontaktinformasjon</li>
              <li>se status for, og eventuelt endre dine søknader</li>
              <li>se eventuelle fakturaer tilknyttet ulike leieforhold</li>
            </ul>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>