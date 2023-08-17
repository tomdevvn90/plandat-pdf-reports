<?php

if(!is_user_logged_in()){
  $login_page  = home_url( '/login' );
  wp_redirect( $login_page ); // keep users on the same page
  exit;
}
get_header();
?>
<style media="screen">
  @media only screen and (max-width: 600px) {
    html{
      height: 100%;
    }
  }
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<div class="templ-form-report">
  <div class="container">

    <form id="regForm" method="post" action="/account/">
      <!-- One "tab" for each step in the form: -->
      <div class="tab">
        <p>
          <label for="pd_owner_name">Owner Name:</label>
          <input placeholder="" oninput="this.className = ''" name="pd_owner_name">
        </p>
        <p>
          <label for="pd_supplier">Supplier:</label>
          <input placeholder="" oninput="this.className = ''" name="pd_supplier">
        </p>

        <p>
          <label for="pd_supplier_contact">Supplier Contact</label>
          <input placeholder="" oninput="this.className = ''" name="pd_supplier_contact">
        </p>
      </div>

      <div class="tab">
          <h2>What do you want to build?</h2>
          <div class="project-options">
              <?php
              $types = get_terms( array(
                'taxonomy'   => 'type-reports',
                'hide_empty' => false,
              ) );
              foreach ($types as $type) {
                $thumbnail = get_field('image_featured', 'type-reports_' . $type->term_id);

                ?>
                <div class="item-project" style="background-image:url('<?php echo $thumbnail; ?>')">
                    <label for="pd_project_<?php echo $type->term_id; ?>"><?php echo $type->name; ?></label>
                    <input type="radio" name="pd_project" id="pd_project_<?php echo $type->term_id; ?>" value="<?php echo $type->term_id; ?>" data-slug="<?php echo $type->name; ?>">
                </div>
                <?php
              }
              ?>
          </div>
      </div>

      <div class="tab">
           <a href="javascript:;" class="pd-back-step"><span class="dashicons dashicons-arrow-left-alt"></span></a>
          <h2>Which State or Territory are you planning the project in?</h2>
          <div class="state-options">
              <?php
              $locations = get_terms( array(
                'taxonomy'   => 'location-reports',
                'hide_empty' => false,
              ) );
              foreach ($locations as $location) {
                ?>
                <div class="item-state">
                    <input type="radio" name="pd_state" id="pd_state_<?php echo $location->term_id; ?>" value="<?php echo $location->term_id; ?>" data-name="<?php echo $location->name; ?>">
                    <label for="pd_state_<?php echo $location->term_id; ?>"><?php echo $location->name; ?></label>
                </div>
                <?php
              }
              ?>
          </div>
      </div>

      <div class="tab search-build-report">
          <a href="javascript:;" class="pd-back-step"><span class="dashicons dashicons-arrow-left-alt"></span></a>
          <h2>Where is the site build?</h2>
          <div class="pd-search-project">
              <div class="form-search">
                <input
                  id="pac-input"
                  class="pd-controls"
                  type="text"
                  placeholder="Search address ..."
                />
              </div>
              <div class="result-search">
                  <div class="__infor-search">
                      <div class="add-1">
                          <label for="">Address</label>
                          <input type="text" name="i-address" value="" readonly>
                      </div>
                      <div class="add-1">
                          <label for="">Lot/Section/DP</label>
                          <input type="text" name="i-lot" value="" readonly>
                      </div>
                      <div class="add-1" style="display:none;">
                          <label for="">Council</label>
                          <input type="text" name="i-council" value="" readonly>
                      </div>
                      <div class="add-1">
                          <label for="">Property Zone</label>
                          <input type="text" name="i-zone" value="" readonly>
                      </div>
                      <div class="add-1">
                          <label for="">Property Size</label>
                          <input type="text" name="i-size" value="" readonly>
                      </div>
                  </div>
                  <div class="__map"></div>
              </div>
          </div>
          <div class="txt-confirm">
              I confirm that the property <span class="txt-property">...</span> is correct and I wish for the report to be ordered.
          </div>
          <div class="btn-confirm">
            <div class="item-btn-confirm">
                <input type="radio" name="pd_btn_confirm" id="pd_btn_confirm_1" value="1">
                <label for="pd_btn_confirm_1">Yes</label>
            </div>
            <div class="item-btn-confirm">
                <input type="radio" name="pd_btn_confirm" id="pd_btn_confirm_2" value="0">
                <label for="pd_btn_confirm_2">No</label>
            </div>
          </div>
      </div>

      <div class="tab">
        <a href="javascript:;" class="pd-back-step"><span class="dashicons dashicons-arrow-left-alt"></span></a>
        <p>
          <label for="pd_owner_name_2">Owner Name:</label>
          <input placeholder="" oninput="this.className = ''" name="pd_owner_name_2">
        </p>
        <p>
          <label for="pd_proposed_use">Proposed Structure Use:</label>
          <input placeholder="" oninput="this.className = ''" name="pd_proposed_use">
        </p>

        <div class="txt-confirm">
            I confirm that the property <span class="txt-property">...</span>  is correct and I wish for the report to be ordered.
        </div>
        <div class="btn-confirm">
          <div class="item-btn-confirm">
              <input type="radio" name="pd_btn_confirm_1" id="pd_btn_confirm_3" value="1">
              <label for="pd_btn_confirm_3">Yes</label>
          </div>
        </div>

      </div>

      <div class="tab">
        <a href="javascript:;" class="pd-back-step"><span class="dashicons dashicons-arrow-left-alt"></span></a>
        <h2>Report Preview</h2>
        <div class="content-report-preview">
          <div class="load-data-content">
            <iframe src="<?php echo home_url('/?action=preview_report&pro_id=&type=') ?>" class="preview_report" width="100%" height="400"></iframe>
          </div>
        </div>
      </div>

      <div class="tab">
        <a href="javascript:;" class="pd-back-step"><span class="dashicons dashicons-arrow-left-alt"></span></a>
        <h2>Order Confirmation</h2>
        <?php $services = get_field('services-pdf-reports','option'); ?>
        <div class="order-confirmation">
            <div class="table-order">
                <?php if(!empty($services)): ?>
                <table>
                   <thead>
                      <th>Item</th>
                      <th style="width: 80px;">Price</th>
                      <th style="width: 80px;">Confirm</th>
                   </thead>
                   <tbody>
                      <?php
                       foreach ($services as $key => $service) {
                          ?>
                          <tr>
                            <td><?php echo $service['service_name'] ?></td>
                            <td>$<?php echo number_format($service['service_price'],2,',',' ') ?></td>
                            <td>
                              <div class="cb-order-confirm">
                                  <input type="checkbox" name="pd_order_confirm[]" data-price="<?php echo $service['service_price'] ?>" id="pd_order_confirm_<?php echo $key; ?>" value="<?php echo $service['service_name']; ?>">
                                  <label for="pd_order_confirm_<?php echo $key; ?>"></label>
                              </div>
                            </td>
                          </tr>
                          <?php
                       }
                       ?>
                      <tr>
                        <td> <strong>Total Cost</strong> </td>
                        <td colspan=2> <strong class="total-cost">$0.00</strong> </td>
                      </tr>
                   </tbody>
                </table>
              <?php else: ?>
                          <?php echo "Not found services!" ?>
            <?php endif; ?>
            </div>
        </div>
      </div>

      <div id="pd-form-actions" class="form-actions">
          <input type="hidden" name="_action_save_report_user" value="1">
          <input type="hidden" name="total_price" value="0">
          <input type="hidden" name="property_id" value="">
          <input type="hidden" name="google_map_img" value="">
          <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
          <button type="button" id="nextBtn" class="continue-btn">Continue &#8594;</button>
      </div>

    </form>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<?php

get_footer();

 ?>
