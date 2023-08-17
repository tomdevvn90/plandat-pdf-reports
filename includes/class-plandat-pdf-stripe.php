<?php

/**
 * Stripe Payment
 *
 */

class Plandat_PDF_Stripe_Payment {

  protected $secret_key = 'sk_test_51NUmotLSVw6XKfizhqqJN69pCiOCRYNfkU7GM7nGyjLiEVMp338GFIxlqQ8IROjazpvEhWveQWH1tPWRbVxDdrgX000LdAhAD4';
  protected $settings_plandat;
  public static $discount = 0;
  public static $stripe;

	public function __construct() {


      if (  class_exists( 'ACF' ) ) {

        //get key
        $this->settings_plandat = get_fields('options');
        $status_stripe = isset($this->settings_plandat['status_stripe']) ? $this->settings_plandat['status_stripe'] : 0;
        if($status_stripe && $this->settings_plandat['secret_key_live']){
          $this->secret_key = $this->settings_plandat['secret_key_live'];
        }elseif($this->settings_plandat['secret_key_test_mode']){
          $this->secret_key = $this->settings_plandat['secret_key_test_mode'];
        }
      }

      //connect stripe
      $this->stripe = new \Stripe\StripeClient($this->secret_key);

	}

  public function get_discount(){

    $discount = $this->discount;

    //get discount
    if(is_user_logged_in()){
      $user_id = get_current_user_id();
      $user_discount = get_field('discount', 'user_' . $user_id );
      $discount = $user_discount ? $user_discount : $discount;
    }

    return $discount;

  }

  public function plandat_get_payment($price,$pdf_id,$security_id){

      \Stripe\Stripe::setApiKey($this->secret_key);

       $price = $this->stripe->prices->create([
          'unit_amount' => $price*100,
          'currency' => 'usd',
          'product' => $this->settings_plandat['product_id'],
       ]);

      $site_url = home_url();
      $discount = $this->get_discount();

      if($discount){

        $coupon = $this->stripe->coupons->create([
          'percent_off' => $discount,
          'duration' => 'once',
        ]);

        $checkout_session = \Stripe\Checkout\Session::create([
          'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => $price->id,
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'discounts' => [[
            'coupon' => $coupon->id,
          ]],
          'success_url' => $site_url . '/account?payment=true&pdf_id='.$pdf_id . '&token_id='.$security_id.'&session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' =>  $site_url . '/account?payment=false&pdf_id=' . $pdf_id,
        ]);

      }else{

        $checkout_session = \Stripe\Checkout\Session::create([
          'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => $price->id,
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => $site_url . '/account?payment=true&pdf_id=' .$pdf_id . '&token_id='.$security_id.'&session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' =>  $site_url . '/account?payment=false&pdf_id=' . $pdf_id,
        ]);

      }

      return $checkout_session->url;
  }

  public function plandat_get_retrieve($session_id){
    return $this->stripe->checkout->sessions->retrieve($session_id);
  }

  public function plandat_get_payment_intents_retrieve($pi){
    return $this->stripe->paymentIntents->retrieve($pi,[]);
  }

  public function plandat_get_payment_retrieve($pm){
    return $this->stripe->paymentMethods->retrieve($pm,[]);
  }

}
