<?php

if(!is_user_logged_in()){
  $login_page  = home_url( '/login' );
  wp_redirect( $login_page ); // keep users on the same page
  exit;
}

get_header();
$current_user = wp_get_current_user();
$pdfs = get_report_current_user();
$report_id    = !empty($pdfs) ? $pdfs[0]->ID : '';
$address      = $report_id ? get_field('address' , $report_id) : '';
$report_files = $report_id ? get_field('report_files' , $report_id) : '';
$services     = get_field('services-pdf-reports','option');
$author_id = get_post_field( 'post_author', $report_id );
$info_user_report = get_user_by('id',$author_id);
$google_map_img = get_field('google_map_image',$report_id);

?>

<div class="pd-templ-account">
  <div class="container">
      <!-- <a href="/previous-searches/" class="btn-orange previous-searches">Previous Searches</a> -->
      <div class="layout-main-account">
          <div class="__map">
            <?php if ( $google_map_img ) {
            	?><img src="<?php echo $google_map_img; ?>" alt="<?php echo get_the_title($report_id) ?>"><?php
            }else{
              ?><img src="<?php echo PLANDAT_PDF_URL . 'public/images/map-2.png' ?>" alt=""><?php
            } ?>
          </div>
          <div class="__info-accout">
              <div class="user-info">
                <p>
                  <label for="user_name">User Name:</label>
                  <input type="text" value="<?php echo $info_user_report->display_name; ?>" disabled name="user_name" readonly>
                </p>
                <p>
                  <label for="user_address">Address:</label>
                  <input type="text" value="<?php echo $address; ?>" disabled name="user_address" readonly>
                </p>
                <p>
                  <label for="user_email">Email:</label>
                  <input type="text" value="<?php echo $info_user_report->user_email; ?>" disabled name="user_email" readonly>
                </p>
              </div>
              <div class="user-files">
                  <?php if($report_files){
                          foreach ($report_files as $key => $service) {
                              ?>
                              <form action="" method="post">
                                  <input type="hidden" name="pdf_report_template" value="<?php echo $service['service_name']; ?>">
                                  <input type="hidden" name="pdf_report_id" value="<?php echo $report_id; ?>">
                                  <p><button type="submit" class="btn-pd-report"><?php echo $service['service_name']; ?> <span>&#8594;</span></button></p>
                              </form>
                              <?php
                          }
                          ?>
                  <?php } ?>
                  <?php if($current_user->ID == $author_id): ?>
                    <form action="" method="post">
                        <input type="hidden" name="pdf_invoice" value="<?php echo $report_id; ?>">
                        <p class="p-invoice">
                          <button type="submit" class="btn-pd-report">Invoice <img src="<?php echo PLANDAT_PDF_URL . 'public/images/icons8-print-50.png' ?>" alt="Invoice" width="32" height="32"></button>
                        </p>
                    </form>

                  <?php endif; ?>
              </div>
              <?php if($current_user->ID == $author_id): ?>
                <a href="#form-share-pdf-user" class="btn-share-users">
                  <img src="<?php echo PLANDAT_PDF_URL . 'public/images/svg-share-shared-icon-hd-png-download.png' ?>" alt="Share" width="32" height="32">
                  Share this Report?
                </a>
              <?php endif; ?>
          </div>
      </div>
      <a href="?action=logout" class="btn-orange btn-sign-out">Sign Out</a>
  </div>
</div>

<div id="form-share-pdf-user" class="plantdat-popup mfp-hide">
    <form class="form-submit-share-user" action="#" method="post">
        <div class="text-share">
          Please enter the email address you want to share this report.
        </div>
        <div class="fields">
          <input type="text" name="email_user" value="" placeholder="Enter email user to share">
          <input type="hidden" name="report_id" value="<?php echo $report_id; ?>">
          <button type="submit" name="button">Send</button>
        </div>
        <div class="error-msg"></div>
    </form>
</div>

<?php

get_footer();

 ?>
