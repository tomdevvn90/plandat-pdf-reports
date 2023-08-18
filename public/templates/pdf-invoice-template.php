
<?php
$report_id = $_POST['pdf_invoice'];
$address      = $report_id ? get_field('address' , $report_id) : '';
$report_files = $report_id ? get_field('report_files' , $report_id) : '';
$property_zone = $report_id ? get_field('property_zone' , $report_id) : '';
$property_size = $report_id ? get_field('property_size' , $report_id) : '';
$lotlocationdp = $report_id ? get_field('lotlocationdp' , $report_id) : '';
$council = $report_id ? get_field('council' , $report_id) : '';
$author_id = get_post_field( 'post_author', $report_id );
$info_user_report = get_user_by('id',$author_id);
$google_map_img = get_field('google_map_image',$report_id);
$discount = get_field('discount', 'user_' . $author_id);
$total = 0;
$company = get_user_meta($author_id,'user_registration_input_box_company',true);
$phone   = get_user_meta($author_id,'user_registration_input_box_phone',true);
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <title>PlanDat</title>
  <style>
    table, th, td {
      border: 1px solid black;
    }
    th {
      background-color: #f2f2f2;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      text-align: left;
      padding: 8px;
    }

    .invoice-total{
      margin-top: 10px;
      text-align: right;
      clear: both;
    }

    .item-total{
      max-width: 300px;
      clear: both;
      margin-left: auto;
    }

    .__label{
      font-weight: bold;
      float: left;
      width: 40%;
      text-align: right;
      padding-right: 20px;
    }

    .__value{
      float: left;
      width: 60%;
      text-align: left;
      padding-left: 15px;
    }

    .item-total.last-item .__label,.item-total.last-item .__value{
      font-size: 20px;
    }

    /*tr:nth-child(even) {background-color: #f2f2f2;}*/
    </style>
</head>

<body>

<div style="background-color:#157aff;text-align:center;"><img style="width:300px;" src="<?php echo PLANDAT_PDF_URL . 'public/images/logo-white.png' ?>"></div>

<br>
  <div style="float:right;">Created: <?php echo date("d/m/Y"); ?> <br> ABN: 85 619 394 204</div>
  <b><u>TAX INVOICE #<?php echo $report_id; ?></u></b><br>
<br>
<h3>ORDER INFORMATION</h3>
<b>Search Address:</b> <?php echo $address; ?><br>
<b>Zone:</b> <?php echo $property_zone; ?><br>
<b>Council:</b> <?php echo $council; ?><br>
<b>Lot/Section/Plan no:</b> <?php echo $lotlocationdp; ?><br>
<b>Property Size:</b> <?php echo $property_size ?> <br>
<!-- <b>Google Map Image:</b> <a href="<?php //echo $google_map_img; ?>">Click here</a> <br> -->
<br>
<div class="order-confirmation">
    <div class="table-order">
        <table>
           <thead>
              <th>Item</th>
              <th>Price</th>
           </thead>
           <tbody>
              <?php
               foreach ($report_files as $key => $service) {
                 $item_total = $service['service_price'] ? $service['service_price'] : 0;
                 $total = $total + $item_total;
                  ?>
                  <tr>
                    <td><?php echo $service['service_name'] ?></td>
                    <td>$<?php echo number_format($service['service_price'],2,'.',' ') ?></td>
                  </tr>
                  <?php
               }
               ?>
           </tbody>
        </table>
        <div class="invoice-total">
          <div class="item-total">
             <div class="__label">
               Subtotal:
             </div>
             <div class="__value">
               <?php echo '$'.number_format($total,2,'.',' '); ?>
             </div>
          </div>
          <div class="item-total">
             <div class="__label">
               Discount (<?php echo $discount; ?>%):
             </div>
             <div class="__value">
               <?php $dis_price = ($total*$discount)/100; ?>
                <?php echo '$'.number_format($dis_price,2,'.',' '); ?>
             </div>
          </div>
					<div class="item-total">
             <div class="__label">
               Tax 10% (GST):
             </div>
             <div class="__value">
               <?php $tax = ($total - $dis_price)/11; ?>
                <?php echo '$'.number_format($tax,2,'.',' '); ?>
             </div>
          </div>
          <div class="item-total last-item">
             <div class="__label">
               Total:
             </div>
             <div class="__value">
               <?php echo '$'.number_format(($total - $dis_price),2,'.',' '); ?>
             </div>
          </div>
        </div>
    </div>

</div>
<br>
<h3>BILLING INFORMATION</h3>
<b>Full Name:</b> <?php echo $info_user_report->first_name ?> <?php echo $info_user_report->last_name ?><br>
<?php if($company): ?>
<b>Company:</b> <?php echo $company; ?><br>
<?php endif; ?>
<?php if($phone): ?>
<b>Phone:</b> <?php echo $phone; ?><br>
<?php endif; ?>
<br>
<h3>PAYMENT INFORMATION</h3>
<?php
$transaction_id = get_field('transaction_id',$report_id);
if($transaction_id){
  $stripe = new Plandat_PDF_Stripe_Payment();
  $infor_payment  = $stripe->plandat_get_payment_intents_retrieve($transaction_id);
  $payment_method  = $stripe->plandat_get_payment_retrieve($infor_payment->payment_method);
  ?>
  Credit Card<br>
  Card Type: <?php echo $payment_method->card->brand; ?><br>
  •••• •••• •••• <?php echo $payment_method->card->last4; ?><br>
  <?php
}
?>
</body></html>
