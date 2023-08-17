<?php

function plandat_display_error_message( $err_code ){
    // Invalid username.
    if ( in_array( 'invalid_username', $err_code ) ) {
        $error = '<strong>ERROR</strong>: Invalid username.';
    }
    // Incorrect password.
    if ( in_array( 'incorrect_password', $err_code ) ) {
        $error = '<strong>ERROR</strong>: The password you entered is incorrect.';
    }

    // Incorrect password.
    if ( in_array( 'invalid_email', $err_code ) ) {
        $error = '<strong>ERROR</strong>: The email you entered is incorrect or not exists.';
    }

    // Empty username.
    if ( in_array( 'empty_username', $err_code ) ) {
        $error = '<strong>ERROR</strong>: The username field is empty.';
    }
    // Empty password.
    if ( in_array( 'empty_password', $err_code ) ) {
        $error = '<strong>ERROR</strong>: The password field is empty.';
    }
    // Empty username and empty password.
    if( in_array( 'empty_username', $err_code )  &&  in_array( 'empty_password', $err_code )){
        $error = '<strong>ERROR</strong>: The username and password are empty.';
    }
    return $error;
}

function is_user_in_role($user, $roles ) {
    // Set user_id to null;
    $user_obj = null;

    // Check if the $user is integer.
    if ( is_int( $user ) ) {
        $user_obj = get_user_by( 'id', $user );
    }

    // Check if the $user is object.
    if ( $user instanceof WP_User) {
        $user_obj = $user;
    }

    // Bail if the $user_id is not set.
    if ( null === $user_obj) {
        return false;
    }

    // Check if the user belons to the role.
    if ( is_string( $roles ) ) {
        return in_array( $roles, (array) $user_obj->roles );
    }

    // Check if the user belongs to the roles.
    if ( is_array( $roles ) ) {
        $user_belong_to = true;
        foreach( $roles as $role ) {
            if ( ! in_array( $role, (array) $user_obj->roles ) ) {
                $user_belong_to = false;
            }
        }
        return $user_belong_to;
    }

    // Return false if nothing works.
    return false;
}

function get_report_current_user(){
  $current_user = wp_get_current_user();
  $pdfs = [];

  if(!empty($current_user)){
    // WP_Query arguments
    $args = array(
      'post_type'              => array( 'pdf-reports' ),
      'post_status'            => array( 'publish' ),
      'author'                 => $current_user->ID,
      'posts_per_page'         => 1,
      'order'                  => 'DESC',
      'orderby'                => 'date',
    );

    if(isset($_GET['pdf_id'])){
      $args['p'] = $_GET['pdf_id'];
    }

    // The Query
    $pdfs = get_posts( $args );

    //get report share
    if(empty($pdfs)){
      // WP_Query arguments
      $args2 = array(
        'post_type'              => array( 'pdf-reports' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => 1,
        'meta_query' => array(
          array(
      			'key'     => 'share_users',
      			'value'   => '"'.$current_user->ID.'"',
      			'compare' => 'LIKE'
      		)
        )
      );
      // The Query
      $pdfs = get_posts( $args2 );
    }

  }
  return $pdfs;
}

function get_report_share_users(){

  $current_user = wp_get_current_user();
  $pdfs = [];

  if(!empty($current_user)){

    // WP_Query arguments
    $args = array(
      'post_type'              => array( 'pdf-reports' ),
      'post_status'            => array( 'publish' ),
      'posts_per_page'         => -1,
      'meta_query' => array(
        array(
          'key'     => 'share_users',
          'value'   => '"'.$current_user->ID.'"',
          'compare' => 'LIKE'
        )
      )
    );
    // The Query
    $pdfs = get_posts( $args );

  }
  return $pdfs;
}

function get_data_outfile(){

    $file_arr = get_field('upload_file_outfile','options');

    $file_path = get_attached_file( $file_arr['ID'] );

  // Open file
    $file = fopen($file_path, 'r');

    // Headers
    $headers = fgetcsv($file);

    // Rows
    $data = [];
    while (($row = fgetcsv($file)) !== false)
    {
        $item = [];
        foreach ($row as $key => $value){
            $name = trim($headers[$key]);
            if($name == '') continue;
            $item[$name] = $value ?: '';
        }

        $data[] = $item;
    }

    // Close file
    fclose($file);

    return $data;

}

function get_data_council_guidelines(){

    $file_arr = get_field('upload_file_council_guidelines','options');

    $file_path = get_attached_file( $file_arr['ID'] );

  // Open file
    $file = fopen($file_path, 'r');

    // Headers
    $headers = fgetcsv($file);

    // Rows
    $data = [];
    while (($row = fgetcsv($file)) !== false)
    {
        $item = [];
        foreach ($row as $key => $value){
            $name = trim($headers[$key]);
            if($name == '') continue;
            $item[$name] = $value ?: '';
        }

        $data[] = $item;
    }

    // Close file
    fclose($file);

    return $data;

}

//Send mail log to admin
function plandat_send_email_log_to_admin($subject = '', $message = '', $email){
    if($subject != '' && $message != ''){
        $email_admin = get_field('to_email','options');
        $header  = "Reply-To: ".$email." \r\n";
        $header .= 'Content-Type: text/html; charset=UTF-8';
        wp_mail( $email_admin, $subject , $message, $header);
    }
}

//Function log data report
function plandat_log_data_report($key_miss = array(),$user_id = '',$file_name = '', $address = '' , $title = ''){

  if($file_name != '' && !empty($key_miss) && $user_id != '' && $address != ''){
    $user_current = wp_get_current_user();
    $settings = get_field('template_email_page_6','options');
    $Emailer = new UR_Emailer();
    $values = array_merge(
      array(
        'email' => $user_current->data->user_email,
        'phone' => get_user_meta($user_id,'user_registration_input_box_phone',true),
        'company' => get_user_meta($user_id,'user_registration_input_box_company',true),
        'data_missing' => implode(',',$key_miss),
        'log_message' => 'The Report File missing is "'.$file_name.'"',
        'address' => $address,
        'file_missing' => $file_name,
        'title' => $title
      ),
      (array)$user_current->data
    );
    $subject = $Emailer->parse_smart_tags($settings['email_subject'],$values);
    $message = $Emailer->parse_smart_tags($settings['email_content'],$values);

    plandat_send_email_log_to_admin($subject,$message,$user_current->data->user_email);
  }

}



 ?>
