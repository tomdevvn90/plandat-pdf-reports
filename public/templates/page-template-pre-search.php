<?php

if(!is_user_logged_in()){
  $login_page  = home_url( '/login' );
  wp_redirect( $login_page ); // keep users on the same page
  exit;
}

get_header();
$current_user = wp_get_current_user();
$pdfs = [];

if(!empty($current_user)){

  // WP_Query arguments
  $args = array(
    'post_type'              => array( 'pdf-reports' ),
    'post_status'            => array( 'publish' ),
    'author'                 => $current_user->ID,
    'posts_per_page'         => -1,
    'order'                  => 'DESC',
    'orderby'                => 'date',
  );
  // The Query
  $pdfs = get_posts( $args );

  $pdf_shares = get_report_share_users();
  foreach ($pdf_shares as $pdf_share) {
    $pdfs[] = $pdf_share;
  }

}

$report_id    = !empty($pdfs) ? $pdfs[0]->ID : '';
$address      = $report_id ? get_field('address' , $report_id) : '';
$report_files = $report_id ? get_field('report_files' , $report_id) : '';
$services     = get_field('services-pdf-reports','option');
$phone        = get_user_meta($current_user->ID , 'user_registration_input_box_phone' , true);
$company      = get_user_meta($current_user->ID , 'user_registration_input_box_company' , true);
?>

<div class="pd-templ-presearch">
  <div class="container">
      <a href="/report/" class="pd-back-step-acc">New report</a>
      <div class="layout-main-presearch">
          <div class="list-address-search">
            <?php foreach ($pdfs as $key => $pdf) {
              $address_pdf = get_field('address' , $pdf->ID);
              ?>
              <p>
                <a href="/account/?pdf_id=<?php echo $pdf->ID; ?>" class="btn-pd-report"><?php echo $address_pdf; ?> <span>&#8594;</span> </a>
              </p>
              <?php
            } ?>
          </div>
          <div class="info-address">
            <div class="user-info">
              <p>
                <label for="user_name">First Name:</label>
                <input type="text" value="<?php echo $current_user->first_name; ?>" name="user_name" readonly disabled>
              </p>
              <p>
                <label for="user_name">Last Name:</label>
                <input type="text" value="<?php echo $current_user->last_name; ?>" name="user_name" readonly disabled>
              </p>
              <p>
                <label for="user_email">Phone:</label>
                <input type="text" value="<?php echo $phone; ?>" name="user_email" readonly disabled>
              </p>
              <p>
                <label for="user_email">Company:</label>
                <input type="text" value="<?php echo $company; ?>" name="user_email" readonly disabled>
              </p>
              <!-- <p>
                <label for="user_address">Address:</label>
                <input type="text" value="<?php // echo $address; ?>" name="user_address" readonly disabled>
              </p> -->
              <p>
                <label for="user_email">Email:</label>
                <input type="text" value="<?php echo $current_user->user_email; ?>" name="user_email" readonly disabled>
              </p>
            </div>
          </div>
      </div>
      <div class="pre-logout">
        <a href="?action=logout" class="btn-orange btn-sign-out">Sign Out</a>
      </div>
  </div>
</div>

<?php

get_footer();

 ?>
