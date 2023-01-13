<script>
  $(document).ready(function() {

    /* Select 2 */
    $("#js-select").select2({
      width: '100%',
      templateResult: formatImageState,
    });

    /* Basic dropdown */
    $selectBasic = $('#js-select-basic').select2({
      width: '100%'
    });

    $selectBasic.data('select2').$container.addClass('select-v2');
    $selectBasic.data('select2').$dropdown.addClass('select-v2');

    /* Image dropdown */
    function formatImageState (state) {
      if (!state.id) {
        return state.text;
      }

      var optionImage = (state.element.value && state.element.value.length > 0) ? '<img src="gfx/' + state.element.value.toLowerCase() + '.png" class="" style="width: 2rem; margin-right: 0.5rem" />' : '' ;

      var state = $(
        '<span>' + optionImage + state.text + '</span>'
      );

      return state;
    };

    $selectImage = $("#js-select-image").select2({
      width: '100%',
      templateResult: formatImageState,
    });

    $selectImage.data('select2').$container.addClass('select-v2');
    $selectImage.data('select2').$dropdown.addClass('select-v2');

    /* Multiselect */
    $selectMultiple = $('#js-select-multiple').select2({
      width: '100%'
    });

    $selectMultiple.data('select2').$container.addClass('select-v2');
    $selectMultiple.data('select2').$dropdown.addClass('select-v2');

  });
</script>

<div class="container">
  <div class="row border-top border-2 py-5">
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Standard</p>
      <select id="js-select-basic">
        <option value="" selected="selected">Velg kommune</option>
        <option value="Stavanger kommune">Stavanger kommune</option>
        <option value="Bergen kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Med bilde</p>
      <select id="js-select-image">
        <option value="" selected="selected">Velg kommune</option>
        <option value="stavanger-kommune">Stavanger kommune</option>
        <option value="bergen-kommune">Bergen kommune</option>
      </select>
    </div>
    <div class="col-sm-6 mb-4">
      <p class="mb-2 text-bold">Flere valg</p>
      <select id="js-select-multiple" multiple="multiple">
        <option value="stavanger-kommune">Stavanger kommune</option>
        <option value="bergen-kommune">Bergen kommune</option>
      </select>
    </div>
  </div>
</div>

