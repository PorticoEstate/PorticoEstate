
<div class="d-flex justify-content-between">
  <a href="#home">Home</a> <span>Icons</span>
</div>
<div class="container">
  <div class="row border-top border-2 py-5">
    <div class="row">
      <?php 
        $icons = array(
          'add_circle',
          'cancel',
          'date_range',
          'edit',
          'event_busy',
          'info',
          'mail',
          'notifications_active',
          'restart_alt',
          'schedule',
          'settings'
        );

        foreach ($icons as $icon) {
          echo '<div class="col-2 d-flex flex-column align-items-center mb-3">
                  <img src="icons/'.$icon.'.svg" alt="" >
                  <span>'.$icon.'.svg</span>
                </div>
                ';
        }
      ?>
    </div>
  </div>
</div>