<script>
  $(document).ready(function() {

    //Datepicker
    $( ".js-basic-datepicker" ).datepicker({
      dateFormat: "d.m.yy",
      changeMonth: true,
      changeYear: true
    });

  });
</script>

<section class="container py-5">
  <div class="row">
    <div class="col-md-6 mb-4 d-flex flex-column align-items-start">
      <label class="mb-2 text-bold" for="standard-datepicker">Datovelger</label>
      <input type="text" class="js-basic-datepicker" placeholder="Velg dato" id="standard-datepicker">
    </div>
  </div>
</section>