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
    <div class="col-md-6 mb-4">
      <p class="mb-2 text-bold">Standard visning</p>
      <input type="text" class="js-basic-datepicker" placeholder="Velg dato">
    </div>
  </div>
</section>