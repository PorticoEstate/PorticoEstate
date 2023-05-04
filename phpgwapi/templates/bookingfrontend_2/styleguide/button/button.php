<section class="container py-5">
  <?php 
    $buttonTypes = array(
      'primary', 'secondary', 'disabled'
    );

    $buttonModifiers = array(
      'normal', 'small', 'large', 'circle', 'transparent'
    );

    foreach ($buttonModifiers as $buttonModifier) {
      echo '<div class="row mb-3">';

      foreach ($buttonTypes as $buttonType) {

        $typeClass = ' pe-btn-'.$buttonType;
        $modifierClass = ' pe-btn--'.$buttonModifier;
        $contentType = '<span>pe-btn '.$typeClass.'</span>';
        $contentModifier = '<span>'.$modifierClass.'</span>';
        
        if($buttonModifier == 'normal') {
          $modifierClass = '';
          $contentModifier = '';
        }

        if($buttonModifier == 'transparent') {
          $typeClass = '';
        }

        if($buttonType == 'disabled') {
          $typeClass = ' pe-btn-primary';
          $contentType = 'pe-btn';
        }

        echo '<div class="col-6 col-sm-4 d-flex flex-column align-items-center mb-4">
                <button type="button" class="pe-btn '.$typeClass.$modifierClass.'" '.(($buttonType == 'disabled') ? 'disabled' : '').'>
                '.(($buttonModifier == 'circle' || $buttonModifier == 'transparent') ? '<span class="sr-only">Søk</span><span class="fas fa-search" title="Søk"></span>' : 'Knapp').'</button>
                <div class="d-flex flex-column mt-2 text-center">
                  '. $contentType.$contentModifier .'
                </div>
              </div>';
      }
      echo '</div>';
    }
  ?>
</section>