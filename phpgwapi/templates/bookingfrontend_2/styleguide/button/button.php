<div class="container border-top border-2 py-5">
  <?php 
    $buttonTypes = array(
      'primary', 'secondary', 'disabled'
    );

    $buttonModifiers = array(
      'normal', 'small', 'large', 'circle'
    );

    foreach ($buttonModifiers as $buttonModifier) {
      echo '<div class="row mb-3">';

      foreach ($buttonTypes as $buttonType) {

        $typeClass = ' btn-'.$buttonType;
        $modifierClass = ' btn--'.$buttonModifier;
        $contentType = '<span>btn '.$typeClass.'</span>';
        $contentModifier = '<span>'.$modifierClass.'</span>';
        
        if($buttonModifier == 'normal') {
          $modifierClass = '';
          $contentModifier = '';
        }

        if($buttonType == 'disabled') {
          $typeClass = ' btn-primary';
          $contentType = 'btn';
        }

        echo '<div class="col-6 col-sm-4 d-flex flex-column align-items-center mb-4">
                <button type="button" class="btn '.$typeClass.$modifierClass.'"
                '.(($buttonType == 'disabled') ? 'disabled' : '').'>'.(($buttonModifier == 'circle') ? '<i class="fas fa-search"></i>' : 'Button').'</button>
                <div class="d-flex flex-column mt-2 text-center">
                  '. $contentType.$contentModifier .'
                </div>
              </div>';
      }
      echo '</div>';
    }
  ?>
</div>