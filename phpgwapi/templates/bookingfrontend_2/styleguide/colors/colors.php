<div class="container">
  <div class="row g-0 border-top border-2 py-5">
    <div class="row">
        <div class="d-flex align-items-center text-center mb-2">
          <div class="colorbox bg-purple"></div>
          <span class="text-center ps-3">Primary color (purple)</span>
        </div>
        <div class="d-flex align-items-center text-center mb-5">
          <div class="colorbox bg-blue"></div>
          <span class="text-center ps-3">Secondary color (blue)</span>
        </div>
    </div>
    
    <?php
      $colors = array(
        'purple' => '#793C8A',
        'blue' => '#28358B',
        'black' => '#E6E3DB',
        'beige-dark' => '#E6E3DB',
        'beige' => '#F7F5F0',
        'grey' => '#D6D6D6',
        'grey-light' => '#F5F5F5',
        'white' => '#FFFFFF',
        'green-light' => '#B0FF94',
        'red-light' => '#FF9494',
        'yellow-light' => '#FFEE94',
      );

      $onlyMainColor = array('white', 'green-light', 'red-light','yellow-light');

      $opacities = array(
        '80', '64', '56', '40', '24', '16', '8', '4'
      );

      
      echo '<div class="row mb-5">';
        foreach ( $colors as $color => $hexColor) {
          echo '<div class="col-3 d-flex align-items-center mb-3">
                  <div class="colorbox bg-'.$color.'"></div>
                  <div class="d-flex flex-column ps-3 ">
                    <span>'.$hexColor.'</span>
                    <span>'.$color.'</span>
                  </div>
                </div>';
        }
      echo '</div>';
      
      echo '<p class="mb-4 text-bold">TEKSTFARGE</p>';
      echo '<div class="row mb-5">';
      foreach ( $colors as $color => $hexColor) {
        echo '<div class="col-3 d-flex flex-column mb-3">
                <span class="text-lg text-'.$color.'">text-'.$color.'</span>
                <span class="">text-'.$color.'</span>
              </div>';
      }
      echo '</div>';


      echo '<p class="mb-4"><span class="text-bold">BAKGRUNNSFARGE</span> - for gjennomsiktig bakgrunnsfarge bruk prefix "bgo" istedenfor "bg"</p>';
      foreach ( $colors as $color => $hexColor) {
        echo '<div class="row">';
          echo '<div class="col-2 d-flex flex-column align-items-center pe-4 mb-4">
                  <div class="colorbox bg-'.$color.'"></div>
                  <span class="text-center">bg-'.$color.'</span>
                </div>';
          if( !in_array($color, $onlyMainColor)) {
            foreach ( $opacities as $opacity ) {
              echo '<div class="col d-flex flex-column align-items-center text-center px-4">
                    <div class="colorbox bg-'.$color.'-'.$opacity.'"></div>
                    <span class="text-center">*-'.$opacity.'</span>
                  </div>';
            }
          };
        echo '</div>';
        if( !in_array($color, $onlyMainColor)) {
          echo '<div class="row">';
            echo '<div class="col-2 d-flex flex-column align-items-center pe-4 mb-4">
                    <div class="colorbox bgo-'.$color.'"></div>
                    <span class="text-center">bgo-'.$color.'</span>
                  </div>';
            
              foreach ( $opacities as $opacity ) {
                echo '<div class="col d-flex flex-column align-items-center text-center px-4">
                      <div class="colorbox bgo-'.$color.'-'.$opacity.'"></div>
                      <span class="text-center">*-'.$opacity.'</span>
                    </div>';
              }
        
          echo '</div>';
        };
      };
    ?>
  </div>
</div>