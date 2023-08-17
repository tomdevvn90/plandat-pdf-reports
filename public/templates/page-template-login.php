<?php
session_start();
get_header();

?>

<div class="templ-form-login">
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
            <?php if(!is_user_logged_in()){ ?>
            <div class="plandat-form-login">
                <h1><?php echo __('Login' , 'plandat-pdf-reports') ?></h1>

                <div class="__sub-text-login">
                  <?php echo __('Please sign in to continue.' , 'plandat-pdf-reports') ?>
                </div>

                <?php

                $err_codes = isset( $_SESSION["err_codes"] )? $_SESSION["err_codes"] : 0;
                if( $err_codes !== 0 ){
                    ?>
                    <div class="__error-login">
                        <?php echo plandat_display_error_message(  $err_codes ); ?>
                    </div>
                    <?php
                    session_destroy();
                }

                 ?>

                <?php
                $args = array(
                      'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                      'label_username' => __( 'Email Address' ),
                      'label_password' => __( 'Password' ),
                      'label_remember' => __( 'Stay logged in' ),
                      'label_log_in' => __( 'Login &#8594;' ),
                      'remember' => true
                  );
                  wp_login_form( $args );
                 ?>

                <div class="__bottom-text-form">
                    Don't have an account? <a href="/sign-up/">Sign up here</a>
                </div>

            </div>
          <?php }else{
            echo "<div style='text-align:center;'>Welcome to come back!</div>";
          } ?>
        </div>
  </div>
</div>

<?php

get_footer();

 ?>
