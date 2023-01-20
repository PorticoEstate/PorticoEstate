<div class="container">
  <div class="row border-top border-2 py-5">
    <?php 

      $statusTypes = array(
        'success' => 'Tidspunkt ledig', 
        'error' => 'Tidspunkt ikke ledig', 
        'warning' => 'Delvis ledig',
      );
      
      echo '<div class="row mb-3">';
        foreach ($statusTypes as $key => $statusLabel) {      
          echo '<div class="col d-flex flex-column align-items-center mb-2">
                  <div class="status status--'.$key.'">
                    '.$statusLabel.'
                  </div>
                  <div class="d-flex flex-column ps-3 ">
                    <span>status status--'.$key.'</span>
                  </div>
                </div>';
        }
      echo '</div>';

      echo '<div class="row mb-3">';
        foreach ($statusTypes as $key => $statusLabel) {      
          echo '<div class="col d-flex flex-column align-items-center mb-2">
                  <div class="status status--'.$key.' w-100">
                    <i class="fas fa-info-circle"></i>
                    '.$statusLabel.'
                  </div>
                  <div class="d-flex flex-column ps-3 ">
                    <span>status status--'.$key.' w-100</span>
                  </div>
                </div>';
        }
      echo '</div>';
    ?>
  </div>
</div>