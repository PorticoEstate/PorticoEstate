<script>
  $(document).ready(function() {

    /* Select 2 */
    $("#js-select").select2({
      width: '100%',
      templateResult: formatImageState,
    });

    /* Basic dropdown */
    $selectBasic = $('#js-select-basic').select2({
      theme: 'select-v2',
      width: '100%',
      placeholder: 'Velg kommune',
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

    $selectImage = $("#js-select-image").select2({
      theme: 'select-v2',
      width: '100%',
      templateResult: formatImageState,
      templateSelection: formatImageState,
      placeholder: 'Velg kommune med bilde',
    });

    /* Multiselect */
    $selectMultiple = $('#js-select-multiple').select2({
      theme: 'select-v2',
      width: '100%',
      placeholder: 'Velg en eller flere kommuner',
      closeOnSelect: false
    });

    /* Price example */
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

    $selectPrice = $('#js-select-price').select2({
      theme: 'select-v2',
      width: '100%',
      data: prices,
      placeholder: 'Velg pris',
      allowClear: true,
      templateSelection: function(data){
        return $('<p class="d-flex m-0 justify-content-between flex-auto pe-2">')
        .html('<span>' + data.text + '</span><span class="text-end"> ' + ((data.price) ? data.price : '') + ' </span>');
      },
      templateResult: function(data){
        return $('<p class="d-flex m-0 justify-content-between">')
        .html('<span>' + data.text + '</span><span class="text-end"> ' + ((data.price) ? data.price : '') + ' </span>');
      },
    });

    // Text
    $(document).on('click', function(event) {
      var container = $(".dropdown");

      //check if the clicked area is dropdown or not
      if (container.has(event.target).length === 0) {
        container.removeClass('dropdown--open');
        $('.dropdown-toggler').attr("aria-expanded","false");
        $('.dropdown-content').addClass('hidden');
      }
    })

    $(".dropdown").each(function(){

      var $toggler = $(this).find(".dropdown-toggler");
      var $dropDown = $(this).find(".dropdown-content");

      $(this).on("click", function(){
        $dropDown.toggleClass("hidden");

        if($dropDown.hasClass('hidden')) {
          $toggler.attr("aria-expanded","false");
          $(this).removeClass('dropdown--open');
        } else {
          $toggler.attr("aria-expanded","true");
          $(this).addClass('dropdown--open');
        }

      });

    });

    $(".slidedown").each(function(){

      var $toggler = $(this).find(".slidedown-toggler");
      var $dropDown = $(this).find(".slidedown-content");

      $(this).on("click", function(){
        $dropDown.slideToggle('fast', function() {
          if($dropDown.is(':visible')) {
            $toggler.attr("aria-expanded","true");
            $('.slidedown').addClass('slidedown--open');
          } else {
            $toggler.attr("aria-expanded","false");
            $('.slidedown').removeClass('slidedown--open');
          }
        });

        
      });
    });
  });
</script>

<div class="container">
  <div class="row border-top border-2 py-5">
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Standard</p>
      <select id="js-select-basic">
        <option value="">Velg kommune</option>
        <option value="Stavanger kommune">Stavanger kommune</option>
        <option value="Bergen kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Med bilde</p>
      <select id="js-select-image">
        <option value="">Velg kommune</option>
        <option value="stavanger-kommune">Stavanger kommune</option>
        <option value="bergen-kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Flere valg</p>
      <select id="js-select-multiple" multiple="multiple">
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
      <p class="mb-2 text-bold">Pris</p>
      <select id="js-select-price">
        <option value="">Velg kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Informasjon</p>
      <div class="dropdown">
        <button class="dropdown-toggler" type="button" aria-expanded="false">
          Informasjon
        </button>
        <div class="dropdown-content hidden">
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
      <div class="slidedown">
        <button class="slidedown-toggler" type="button" aria-expanded="false">
          Sandnes idrettspark
        </button>
        <div class="slidedown-content" style="display: none">
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
</div>

