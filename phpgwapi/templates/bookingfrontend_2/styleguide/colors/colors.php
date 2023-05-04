<section class="container py-5">
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
    function lumdiff($hex){

      $background = "#FFFFFF";

      list($R1, $G1, $B1) = sscanf($hex, "#%02x%02x%02x");
      list($R2, $G2, $B2) = sscanf($background, "#%02x%02x%02x");

      $L1 = 0.2126 * pow($R1/255, 2.2) +
            0.7152 * pow($G1/255, 2.2) +
            0.0722 * pow($B1/255, 2.2);

      $L2 = 0.2126 * pow($R2/255, 2.2) +
            0.7152 * pow($G2/255, 2.2) +
            0.0722 * pow($B2/255, 2.2);

      if($L1 > $L2){
          return ($L1+0.05) / ($L2+0.05);
      }else{
          return ($L2+0.05) / ($L1+0.05);
      }
    }

    $colors = array(
      'purple' => '#793C8A',
      'blue' => '#28358B',
      'black' => '#000000',
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
        echo '<div class="col-6 col-md-4 col-lg-3 d-flex align-items-center mb-3">
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

    echo '<div class="col-6 col-md-4 col-lg-3 d-flex flex-column mb-3">
            <span class="text-lg text-primary p-1">text-primary</span>
            <span class="px-1">text-primary</span>
          </div>';
    echo '<div class="col-6 col-md-4 col-lg-3 d-flex flex-column mb-3">
            <span class="text-lg text-secondary p-1">text-secondary</span>
            <span class="px-1">text-secondary</span>
          </div>';

    foreach ( $colors as $color => $hexColor) {

      $backgroundColor = '';
      if(lumdiff($hexColor) < 5 ) {
        $backgroundColor = 'bg-black-80';
      } else {
      }

      echo '<div class="col-6 col-md-4 col-lg-3 d-flex flex-column mb-3">
              <span class="text-lg text-'.$color.' '.$backgroundColor.' p-1 rounded-mini">text-'.$color.'</span>
              <span class="px-1">text-'.$color.'</span>
            </div>';
    }
    echo '</div>';


    echo '<p class="mb-4"><span class="text-bold">BAKGRUNNSFARGE</span> - for gjennomsiktig bakgrunnsfarge bruk prefix "bgo" istedenfor "bg"</p>';
    foreach ( $colors as $color => $hexColor) {
      echo '<div class="row mb-4">';
      echo '<p class="col-12 mb-2 text-bold">bg-'.$color.'</p>';
        echo '<div class="col d-flex flex-column px-4 mb-4">
                <div class="colorbox bg-'.$color.'"></div>
              </div>';
        if( !in_array($color, $onlyMainColor)) {
          foreach ( $opacities as $opacity ) {
            echo '<div class="col d-flex flex-column align-items-center text-center px-4 mb-4">
                  <div class="colorbox bg-'.$color.'-'.$opacity.'"></div>
                  <span class="text-center text-nowrap">*-'.$opacity.'</span>
                </div>';
          }
        };
      echo '</div>';
      if( !in_array($color, $onlyMainColor)) {
        echo '<div class="row mb-4">';
        echo '<p class="col-12 mb-2 text-bold">bgo-'.$color.'</p>';
          echo '<div class="col d-flex flex-column align-items-center px-4 mb-4">
                  <div class="colorbox bgo-'.$color.'"></div>
                </div>';
          
            foreach ( $opacities as $opacity ) {
              echo '<div class="col d-flex flex-column align-items-center text-center px-4 mb-4">
                    <div class="colorbox bgo-'.$color.'-'.$opacity.'"></div>
                    <span class="text-center text-nowrap">*-'.$opacity.'</span>
                  </div>';
            }
      
        echo '</div>';
      };
    };
  ?>
</section>