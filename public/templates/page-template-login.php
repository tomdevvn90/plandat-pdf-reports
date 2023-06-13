<?php

get_header();

?>

<div class="templ-form-login">
  <div class="container">
        <div class="plandat-content-form">
            <div class="plandat-big-logo">
              <a href="<?php echo home_url(); ?>"><img src="<?php echo PLANDAT_PDF_URL . 'public/images/Untitled-design-2023-03-07T125648.334.png' ?>" alt=""></a>
            </div>
            <div class="plandat-form-login">
                <h1>Login</h1>
                <div class="__sub-text-login">
                  Please sign in to continue.
                </div>
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
        </div>
  </div>
</div>

<?php

get_footer();

 ?>
