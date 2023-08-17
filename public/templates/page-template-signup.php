<?php

get_header();

?>

<div class="templ-form-signup">
  <div class="container">
        <div class="plandat-content-form">
            <div class="plandat-big-logo">
              <a href="<?php echo home_url(); ?>">
                <?php if( get_field( 'plandat_logo','option' ) ){
                    ?><img src="<?php echo get_field('plandat_logo','option'); ?>" alt=""><?php
                }else{
                  ?><img src="<?php echo PLANDAT_PDF_URL . 'public/images/Untitled-design-2023-03-07T125648.334.png' ?>" alt=""><?php
                } ?>
              </a>
            </div>
            <div class="plandat-form-signup">
                <h1><?php echo __('Sign Up' , 'plandat-pdf-reports') ?></h1>
                <div class="__sub-text-signup">
                  <?php echo __('Please sign up to continue.' , 'plandat-pdf-reports') ?>
                </div>

                <?php echo do_shortcode(get_field('shortcode_register_form')) ?>

                <div class="__bottom-text-form">
                    Have an account? <a href="/login/">Login here</a>
                </div>

            </div>
        </div>
  </div>
</div>

<?php

get_footer();

 ?>
