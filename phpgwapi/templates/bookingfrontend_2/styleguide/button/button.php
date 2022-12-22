
<div class="d-flex justify-content-between">
  <a href="#home">Home</a> <span>Button</span>
</div>
<div class="container">
  <div class="row border-top border-2 py-5">
    <?php 

      $buttonTypes = array(
        'primary', 'secondary',
      );

      $buttonModifiers = array(
        'small', 'large'
      );

      foreach ($buttonTypes as $buttonType) {
        echo '<div class="row mb-3">
                <div class="col d-flex flex-column align-items-center mb-2">
                  <button type="button" class="btn btn-'.$buttonType.' mb-2">Button</button>
                    <div class="d-flex flex-column ps-3 ">
                      <span>btn btn-'.$buttonType.'</span>
                    </div>
                </div>';

                foreach ($buttonModifiers as $buttonModifier) {
                  echo '<div class="col d-flex flex-column align-items-center mb-2">
                          <button type="button" class="btn btn-'.$buttonType.' btn--'.$buttonModifier.' mb-2">Button</button>
                          <div class="d-flex flex-column ps-3 ">
                            <span>btn btn-'.$buttonType.'</span>
                            <span>btn--'.$buttonModifier.'</span>
                          </div>
                        </div>';
                }

        echo '<div class="col d-flex flex-column align-items-center mb-2">
                <button type="button" class="btn btn-primary mb-2" disabled>Button</button>
                <span>Disabled</span>
              </div>
            </div>';
      }

    ?>
  </div>
</div>