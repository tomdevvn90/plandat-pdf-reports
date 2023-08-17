<?php
// reference the Dompdf namespace
use Dompdf\Dompdf;
/**
 * @since      1.0.0
 * @package    Plandat_PDF_Reports
 * @subpackage Plandat_PDF_Reports/includes
 * @author     Online Optimisation <info@onlineoptimisation.com.au>
 */
class Plandat_PDF_Reports {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plandat_PDF_Reports_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'Plandat_PDF_Reports_VERSION' ) ) {
			$this->version = Plandat_PDF_Reports_VERSION;
		} else {
			$this->version = PLANDAT_PDF_VERSION;
		}
		$this->plugin_name = 'Plandat_PDF_Reports';

		$this->load_dependencies();
		$this->define_public_hooks();
		$this->define_public_hooks_admin();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plandat_PDF_Reports_Loader. Orchestrates the hooks of the plugin.
	 * - Plandat_PDF_Reports_i18n. Defines internationalization functionality.
	 * - Plandat_PDF_Reports_Admin. Defines all hooks for the admin area.
	 * - Plandat_PDF_Reports_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plandat-pdf-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plandat-pdf-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-plandat-pdf-public.php';

		/**
		 * Load payment Stripe
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-plandat-pdf-stripe.php';

    $this->loader = new Plandat_PDF_Loader();

	}

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Plandat_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale() {

    $plugin_i18n = new Plandat_i18n();

    $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

  }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Plandat_PDF_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_public, 'block_wp_admin' );
		$this->loader->add_filter( 'theme_page_templates', $plugin_public, 'load_templates', 10 , 4 );
		$this->loader->add_filter( 'page_template', $plugin_public, 'page_template');
		$this->loader->add_filter( 'show_admin_bar', $plugin_public, 'show_admin_bar');
		$this->loader->add_filter( 'template_include', $plugin_public, 'redirect_templates');
		$this->loader->add_filter( 'et_header_top', $plugin_public, 'add_user_top_right_header');
		$this->loader->add_action( 'admin_footer', $plugin_public, 'add_js_admin');

	}

	private function define_public_hooks_admin(){

		//Settings
		add_filter('use_block_editor_for_post_type', array($this,'plandat_disable_gutenberg') , 10, 2);
		add_action('acf/init', array($this,'plandat_acf_op_init'));
		//add_filter('acf/load_field/name=report_files', array($this, 'acf_load_report_files_field_choices'));

		//Save reports
		add_action('init', array($this,'plandat_save_reports'));
		add_action('init', array($this,'plandat_update_status_reports'));

		//User
		add_filter('manage_users_columns', array($this,'plandat_modify_user_table'), 9999);
		add_filter('manage_users_custom_column', array($this,'plandat_modify_user_table_row'), 10, 3 );

		//PDF Reports
		add_filter('manage_pdf-reports_posts_columns', array($this,'plandat_modify_pdf_reports_table'), 9999);
		add_action('manage_pdf-reports_posts_custom_column', array($this,'plandat_modify_pdf_reports_table_row'), 10, 2 );

		//Login page
		add_filter( 'login_errors', array($this,'plandat_error_handler'));
		add_filter( 'login_redirect', array($this,'plandat_login_default_page'));
		add_action( 'init' , array($this,'plandat_logout'));

		//Ajax Share report
		add_action( 'wp_ajax_share_report_user', array($this, 'share_report_user_init' ) );
		add_action( 'wp_ajax_nopriv_share_report_user', array($this, 'share_report_user_init' ) );

		//Ajax Share report
		add_action( 'wp_ajax_get_property_size', array($this, 'get_property_size_init' ) );
		add_action( 'wp_ajax_nopriv_get_property_size', array($this, 'get_property_size_init' ) );

		//Log send mail
		add_action( 'wp_ajax_send_mail_log_user_search', array($this, 'send_mail_log_user_search_init' ) );
		add_action( 'wp_ajax_nopriv_send_mail_log_user_search', array($this, 'send_mail_log_user_search_init' ) );

		//Invoice PDF template
		add_action( 'init' , array($this,'plandat_print_pdf_invoice_template'));

		//PlanDAT Shed NSW Report PDF template
		add_action( 'init' , array($this,'plandat_print_pdf_shed_nsw_template'));

		//PlanDAT Preview PDF
		add_action( 'init' , array($this,'plandat_print_pdf_preview_template'));

		//PlanDAT Test Report
		add_action( 'init' , array($this,'plandat_print_pdf_test_template'));

	}

	public function send_mail_log_user_search_init(){

		$key_search = $_POST['key_search'];
		$user_id = $_POST['user_id'];
		$user_current = get_userdata( $user_id );
		$settings = get_field('template_email_step_3','options');
		$Emailer = new UR_Emailer();
		$values = array_merge(
			array(
				'email' => $user_current->data->user_email,
				'phone' => get_user_meta($user_id,'user_registration_input_box_phone',true),
				'company' => get_user_meta($user_id,'user_registration_input_box_company',true),
				'key' => $key_search,
				'address' => $key_search,
				'log_message' => 'Not found address at step 3'

			),
			(array) $user_current->data
		);

		$subject = $Emailer->parse_smart_tags($settings['email_subject'],$values);
		$message = $Emailer->parse_smart_tags($settings['email_content'],$values);

		plandat_send_email_log_to_admin($subject,$message,$user_current->data->user_email);

		echo wp_json_encode(array(
			'result' => true
		));

		die;

	}

	public function get_property_size_init(){

		$property_id = $_POST['property_id'];
		$json = file_get_contents('https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/boundary?id='.$property_id.'&Type=property&outsr=4326');
		$obj5 = json_decode($json);

		require_once("greens-polygon/polygon.class.php");
		$polygon = new Polygon();
		$geo = [];
		foreach($obj5[0]->geometry->rings[0] as $ring) {
		$geo[] = $ring[1].','.$ring[0];

		//$polygon->setVertice(new Point($ring[0], $ring[1]));
		$polygon->setVertice(new Point($ring[1], $ring[0]));
		//$polygon->setVertice(new Point(($ring[1]*111.32), ($ring[0] * (40075 * cos($ring[1]*111.32) / 360))));
		}

		$property_size = round($polygon->area() * -10249622610,0);
		$zoom = 15;

		if($property_size < 50000) $zoom = 17;
		if($property_size >= 50000 && $property_size < 200000 ) $zoom = 16;
		if($property_size >= 200000 && $property_size < 600000) $zoom = 15;
		if($property_size >= 600000) $zoom = 14;

		echo wp_json_encode(array(
			'size' => $property_size,
			'geo' => implode('|', $geo),
			'zoom' => $zoom
		));

		die;

	}

	public function share_report_user_init(){

		$email_user = trim($_POST['email_user']);
		$report_id  = $_POST['report_id'];
		$list_users = get_field('share_users',$report_id);
		$error_mess = '';
		$status = 'success';

		//check email exist
		if(!email_exists($email_user)){
			$status = 'error';
			$error_mess = "<b>" . $email_user . "</b> is not exists.";
		}

		//check valid email
		if(!filter_var($email_user, FILTER_VALIDATE_EMAIL)){
			$status = 'error';
			$error_mess = "<b>" . $email_user . "</b> is not a valid email address.";
		}

		//check empty
		if(trim($email_user) == ''){
			$status = 'error';
			$error_mess = "Email is empty!";
		}

		//Add user to report
		$info_share_user = get_user_by('email',$email_user);
		$list_users = !empty($list_users) ? $list_users : array();
		$list_users = array_merge($list_users,array($info_share_user->ID));
		update_field('share_users',$list_users,$report_id);

		$result = array(
			'status' => $status,
			'error' => $error_mess
		);

		echo wp_json_encode($result);
		die;
	}

	public function plandat_logout(){

		if(isset($_GET['action']) && $_GET['action'] == 'logout'){
			wp_logout();
	    wp_redirect(home_url('/login'));
	    exit;
		}

	}

	public function plandat_error_handler(){
		session_start();
		$login_page  = home_url( '/login' );
    global $errors;
    $err_codes = $errors->get_error_codes(); // get WordPress built-in error codes
    $_SESSION["err_codes"] =  $err_codes;
    wp_redirect( $login_page ); // keep users on the same page
    exit;
	}

	public function plandat_login_default_page($redirect_url){
		return home_url('/report/');
	}

	public function acf_load_report_files_field_choices($field){
		// reset choices
    $field['choices'] = array();

    $services =  get_field('services-pdf-reports' , 'options');

		if(!empty($services)){
			foreach($services as $attr){
					$field['choices'][ $attr['service_name'] ] = $attr['service_name'];
			}
		}

		return $field;
	}

	public function plandat_modify_pdf_reports_table($column){
		unset($column['date']);
		$column['payment_status'] = 'Payment Status?';
		$column['address'] = 'Address';
		$column['lotlocationdp'] = 'Lot/Location/DP';
		$column['zone'] = 'Property Zone';
		$column['size'] = 'Property Size';
		$column['purchased'] = 'Purchased';
		$column['author'] = 'User';
		$column['date'] = 'Date';
		return $column;
	}

	public function plandat_modify_pdf_reports_table_row($column, $post_id){
		switch ($column) {
				case 'payment_status' :
						$color = 'green';
						$payment_status = get_field('payment_status',$post_id);
						if($payment_status == 'Faield') $color = 'red';
						if($payment_status == 'Cancel' || $payment_status == 'Pending') $color = '#797979';
						echo '<b style="color:'.$color.'; text-transform: uppercase;">' . $payment_status . '</b>';break;
        case 'address' :
						echo get_field('address',$post_id);break;
				case 'lotlocationdp' :
						echo get_field('lotlocationdp',$post_id);break;
				case 'zone' :
						echo get_field('property_zone',$post_id);break;
				case 'size' :
						echo get_field('property_size',$post_id);break;
				case 'purchased' :
						$report_files = get_field('report_files',$post_id);
						if(!empty($report_files) && is_array($report_files)):
							foreach ($report_files as $key => $file) {
								echo $file['service_name'];
								echo (($key + 1) < count($report_files)) ? ', ' : '';
							}
						endif;
						break;
        default:
    }
	}

	public function plandat_modify_user_table_row($val, $column_name, $user_id){
		switch ($column_name) {
        case 'pdf-report' :
						 $url = admin_url('edit.php?post_type=pdf-reports&author=' . $user_id);
						 $reports = get_posts(array( 'post_type' => 'pdf-reports','posts_per_page' => -1, 'author' => $user_id, 'post_status' => array('publish' , 'pending') ));
						 return '<a href="'.$url.'">' . (count($reports) > 1 ? count($reports) . ' reports' : count($reports) . ' report') . '</a>';
        default:
    }
    return $val;
	}

	public function plandat_modify_user_table($column){
		unset($column['ur_user_user_registered_source']);
		unset($column['ur_user_user_status']);
		unset($column['ur_user_user_registered_log']);
		//unset($column['posts']);
		unset($column['role']);
		$column['pdf-report'] = 'PDF Reports';
		$column['role'] = 'Role';
		$column['ur_user_user_status'] = 'Status';
		$column['ur_user_user_registered_log'] = 'Registered At';
    return $column;
	}

	public function plandat_disable_gutenberg($current_status, $post_type){
		// Use your post type key instead of 'product'
    if ($post_type === 'pdf-reports') return false;
    return $current_status;
	}

	public function plandat_acf_op_init(){
		// Check function exists.
    if( function_exists('acf_add_options_page') ) {

        // Register options page.
        $option_page = acf_add_options_page(array(
            'page_title'    => __('PlanDAT Settings'),
            'menu_title'    => __('PlanDAT Settings'),
            'menu_slug'     => 'plandat-settings',
            'redirect'      => false
        ));
    }
	}

	public function plandat_save_reports(){

		if(isset($_POST['_action_save_report_user']) && $_POST['_action_save_report_user']){

			$total_price = isset($_POST['total_price']) ? $_POST['total_price'] : 0;


			//get user id current
			$user_ID = get_current_user_id();
			$services = get_field('services-pdf-reports','option');
			$security_id = $this->generateToken();

			// Create report
			$my_report = array(
				'post_title'    => 'Report #' . time(),
				'post_status'   => 'pending',
				'post_author'   => $user_ID,
				'post_type' => 'pdf-reports'
			);

			// Insert the post into the database
			$post_id = wp_insert_post($my_report);
			$total_cost = 0;

			if(!is_wp_error($post_id)){
				//info project
				update_field('address' , $_POST['i-address'], $post_id);
				update_field('lotlocationdp' , $_POST['i-lot'], $post_id);
				update_field('property_zone' , $_POST['i-zone'], $post_id);
				update_field('property_size' , $_POST['i-size'], $post_id);

				//owner supplier
				update_field('owner_name' , $_POST['pd_owner_name'], $post_id);
				update_field('supplier' , $_POST['pd_supplier'], $post_id);
				update_field('supplier_contact' , $_POST['pd_supplier_contact'], $post_id);

				//owner project
				update_field('proposed_owner_name' , $_POST['pd_owner_name_2'], $post_id);
				update_field('proposed_structure_use' , $_POST['pd_proposed_use'], $post_id);

				//update type
				wp_set_post_terms( $post_id, array($_POST['pd_project']) , 'type-reports' );

				//update location
				wp_set_post_terms( $post_id, array($_POST['pd_state']) , 'location-reports' );

				//Set image featured
				update_field('google_map_image' , $_POST['google_map_img'], $post_id);

				//Council
				update_field('council' , $_POST['i-council'], $post_id);

				//Status
				update_field('payment_status' , 'Pending', $post_id);

				//Propery ID
				update_field('property_id' , $_POST['property_id'], $post_id);

				//Security ID
				update_field('security_id' , $security_id , $post_id);

				$report_files = [];
				foreach ($services as $key => $service) {
					 if(in_array($service['service_name'],$_POST['pd_order_confirm'])){
						 $service_price = $service['service_price'] ? $service['service_price'] : 0;
						 $total_cost = $total_cost + $service_price;
						 $report_files[] = $service;
					 }
				}

				//Files
				update_field('report_files' , $report_files , $post_id);

				if($total_cost > 0){
					//Get link payment
					$stripe = new Plandat_PDF_Stripe_Payment();
					$url_payment = $stripe->plandat_get_payment($total_cost , $post_id , $security_id);
					header('Location: ' . $url_payment);
					exit;
				}

			}else{
				//there was an error in the post insertion,
				echo $post_id->get_error_message(); die;
			}

		}

	}

	public function plandat_update_status_reports(){

		if(isset($_GET['payment']) && $_GET['payment'] && isset($_GET['pdf_id']) && $_GET['pdf_id']){

			$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : 0;
			$pdf_id 		= $_GET['pdf_id'];
			$payment 		= $_GET['payment'];
			$token_id 		= $_GET['token_id'];

			// Update status
			$my_pdf = array();
			$my_pdf['ID'] = $pdf_id;
			$status_report = get_field('payment_status' , $pdf_id);
			$security_id   = get_field('security_id' , $pdf_id);
			$is_secure = false;

			//Security
			if(isset($token_id) && trim($token_id) != '' && trim($token_id) == $security_id) $is_secure = true;

			if($status_report != 'Success' && $this->is_check_report_user($pdf_id) && $is_secure){
					if($session_id && $payment == 'true'){
							$stripe = new Plandat_PDF_Stripe_Payment();
							$infor_payment = $stripe->plandat_get_retrieve($session_id);
							if($infor_payment->status == 'complete'){
								// Update the post into the database
								$my_pdf['post_status'] = 'publish';
								wp_update_post( $my_pdf );
								update_field('payment_status' , 'Success' , $pdf_id);
							}else{
								update_field('payment_status' , 'Failed' , $pdf_id);
							}
							//Update meta field
							update_field('transaction_id' , $infor_payment->payment_intent , $pdf_id);
							update_field('session_id' , $session_id , $pdf_id);
					}else{
						update_field('payment_status' , 'Cancel' , $pdf_id);
					}
			}

		}
	}

	//Check report of user
	public function is_check_report_user($report_id){
			$curt_author_id = get_current_user_id();
			$post_author_id = get_post_field( 'post_author', $_GET['pdf_id'] );

			if($curt_author_id != $post_author_id) return false;
			return true;
	}

	public function generateToken($type = null) {
		$token_id = md5(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10) . time());
		return $token_id;
	}

	public function plandat_print_pdf_invoice_template(){
		if(isset($_POST['pdf_invoice']) && $_POST['pdf_invoice'] != ''){

			ob_start();
			include PLANDAT_PDF_DIR . 'public/templates/pdf-invoice-template.php';
			$html = ob_get_contents();
			ob_end_clean();

			$_dompdf_show_warnings = true; // record warnings generated by Dompdf
			$_dompdf_debug = false; // output frame details for every frame in the document
			$_DOMPDF_DEBUG_TYPES = [
			    'page-break' => false // record information about page break determination
			];

			$dompdf = new Dompdf([
			    "logOutputFile" => "log.html",
			    "debugPng" => false, // extra messaging
			    "debugKeepTemp" => false, // don't delete temp files
			    'debugCss' => false, // output Style parsing information and frame details for every frame in the document
			    'debugLayout' => false, // draw boxes around frames
			    'debugLayoutLines' => false, // line boxes
			    'debugLayoutBlocks' => false, // block frames
			    'debugLayoutInline' => false, // inline frames
			    'debugLayoutPaddingBox' => false, // padding box
			    "isRemoteEnabled" => true,
			    "chroot" => ["images"]
			]);

			  // instantiate and use the dompdf class
			  $dompdf->loadHtml($html);

			  // (Optional) Setup the paper size and orientation
			  $dompdf->setPaper('A4', 'portrait');

			  // Render the HTML as PDF
			  $dompdf->render();

			  // Output the generated PDF to Browser
			  $dompdf->stream("PlanDAT-Invoice-".$_GET['pdf_id'].".pdf");
		}
	}

	public function plandat_print_pdf_shed_nsw_template(){
		if(isset($_POST['pdf_report_template']) && $_POST['pdf_report_template'] != ''){
			$pdf_report 	= $_POST['pdf_report_template'];
			$pdf_nameFile = str_replace(' ','-',$pdf_report);
			ob_start();

			//PlanDAT Shed NSW Report
			if($pdf_report == 'PlanDAT Shed NSW Report'){
					include PLANDAT_PDF_DIR . 'public/reports/plandat-shed-nsw-report.php';
			}

			$html = ob_get_contents();
			ob_end_clean();

			$_dompdf_show_warnings = true; // record warnings generated by Dompdf
			$_dompdf_debug = false; // output frame details for every frame in the document
			$_DOMPDF_DEBUG_TYPES = [
			    'page-break' => false // record information about page break determination
			];

			$dompdf = new Dompdf([
			    "logOutputFile" => "log.html",
			    "debugPng" => false, // extra messaging
			    "debugKeepTemp" => false, // don't delete temp files
			    'debugCss' => false, // output Style parsing information and frame details for every frame in the document
			    'debugLayout' => false, // draw boxes around frames
			    'debugLayoutLines' => false, // line boxes
			    'debugLayoutBlocks' => false, // block frames
			    'debugLayoutInline' => false, // inline frames
			    'debugLayoutPaddingBox' => false, // padding box
			    "isRemoteEnabled" => true,
			    "chroot" => ["images"]
			]);

			  // instantiate and use the dompdf class
			  $dompdf->loadHtml($html);

			  // (Optional) Setup the paper size and orientation
			  $dompdf->setPaper('A4', 'portrait');

			  // Render the HTML as PDF
			  $dompdf->render();

			  // Output the generated PDF to Browser
			  $dompdf->stream( $pdf_nameFile . ".pdf");
		}
	}

	public function plandat_print_pdf_preview_template(){
			if(isset($_GET['action']) && $_GET['action'] == 'preview_report'){
				ob_start();
				?>
				<style media="screen">
					#page-container > .pf:not(:first-child){
						filter:blur(4px);
				    -o-filter:blur(4px);
				    -ms-filter:blur(4px);
				    -moz-filter:blur(4px);
				    -webkit-filter:blur(4px);
						 user-select: none;
					}
					body.loading::before{
							content: "";
							display: block;
					    background: url(<?php echo PLANDAT_PDF_URL . 'public/images/ajax-loading.gif' ?>) no-repeat center center;
					    position: fixed;
					    top: 0;
					    left: 0;
					    height: 100%;
					    width: 100%;
					    z-index: 9999999;
							background-color: #fff;
							opacity: 0.8;
							background-size: 50px;
					}
				</style>
				<script type="text/javascript">
					document.body.classList.add('loading');
					window.onload = function() {document.body.classList.remove('loading');};
				</script>
				<?php

				//This template conver by https://convertio.co/pdf-html/
				include PLANDAT_PDF_DIR . 'public/templates/pdf-report-template.php';

				$html = ob_get_contents();
				echo $html;die;
			}
	}

	public function plandat_print_pdf_test_template(){
		if(isset($_POST['test_report']) && $_POST['test_report'] == 'GO'){

			require_once("greens-polygon/polygon.class.php");
			$address 		 = $_POST['address'];
			$type_report = $_POST['type_report'];
			$outfiles = get_data_outfile();
			$council_guidelines = get_data_council_guidelines();
			$text_sub_type = '';
			if($type_report == 'Carport') $text_sub_type = 'PlanDat Carport Report';
			if($type_report == 'Deck') 		$text_sub_type = 'PlanDat Deck Report';
			if($type_report == 'House') 	$text_sub_type = 'PlanDat House Report';
			if($type_report == 'Pergola') $text_sub_type = 'PlanDat Pergola Report';
			if($type_report == 'Pool') 		$text_sub_type = 'PlanDat Pool Report';
			if($type_report == 'Shed') 		$text_sub_type = 'PlanDat Class 10a Shed Report';

			//Prop ID
			$property_id = $_POST['propId'];

			//Address
			$address = $_POST['address'];

			//Zone
			$zone_results = explode(',',$_POST['i-zone']);
			$zones = [];
			$check_zones = [];
			foreach ($zone_results as $zone){
				$zones[] = $zone;
				$check_zones[] = strtolower($zone);
				$check_zones[] = strtolower(str_replace(':','',$zone));
				$check_zones[] = strtolower('Zone ' . str_replace(':','',$zone));
			}

			//Council
			$council_name = $_POST['i-council'];

			//Lot
			$LotDescription = $_POST['i-lot'];

			//Size
			$prod_size = $_POST['i-size'];

			//1. Trim Council Name
			$trim_council_name = strtolower(trim(str_replace('COUNCIL','',$council_name)));

			//2. Find Council In Outfile
			$list_zones = [];
			foreach ($outfiles as $key => $row) {

				$Council = strtolower($row['Council']);
				$Zone		 = trim(strtolower($row['Zone']));

				//Check zone
				$is_check_zone = false;
				foreach ($check_zones as $c => $c_zone) {
					if(strpos($Zone, $c_zone) !== false){
						$is_check_zone = true;
					}
				}

				if(strpos($Council, $trim_council_name) !== false && $is_check_zone){
					$list_zones[] = $row;
				}

			}


			//3. get info Council Guidelines
			$list_guidelines = [];

			//COUNCIL
			$councils = explode(',',$council_name);
			$council_report = [];
			foreach ($councils as $c) {
			  $council_report[] = strtolower(trim($c));
			}

			//Zone
			$arr_zones = explode(',',$_POST['i-zone']);
			$tmp_zones = [];
			foreach ($arr_zones as $z) {
			  $arr_z = explode(':',$z);
			  $tmp_zones[] = trim($arr_z[0]);
			}

			foreach ($council_guidelines as $g) {
			  $g_council_name = strtolower(trim($g['Council Name']));
			  $g_size = trim($g['Size']);
			  if(in_array($g_council_name,$council_report) && in_array($g_size,$tmp_zones)){
			    $list_guidelines[] = $g;
			  }
			}


			//4. Get Image and Info
			$json = file_get_contents('https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/lot?propId='.$property_id);
			$obj4 = json_decode($json);

			$ring_highest = $ring_lowest = 0;
			$bbox = [];
			foreach( $obj4[0]->geometry->rings[0] as $ring_key => $ring_value){
			  if ($ring_highest == 0 || $ring_highest < $ring_value[0]){
			    $ring_highest = $ring_value[0];
			    $bbox[0] = $ring_value[0];
			    $bbox[1] = $ring_value[1];
			  }
			  if ($ring_lowest == 0 || $ring_lowest > $ring_value[0]){
			    $ring_lowest = $ring_value[0];
			    $bbox[2] = $ring_value[0];
			    $bbox[3] = $ring_value[1];
			  }
			}

			//echo implode(',', $bbox);

			$bbox[0] = $bbox[0]+($bbox[0]-$bbox[2]);
			$bbox[1] = $bbox[1]+($bbox[1]-$bbox[3]);
			$bbox[2] = $bbox[2]-($bbox[0]-$bbox[2]);
			$bbox[3] = $bbox[3]-($bbox[1]-$bbox[3]);


			$site_info = [];

			$json = file_get_contents('https://api.apps1.nsw.gov.au/planning/viewersf/V1/ePlanningApi/layerintersect?type=property&id='.$property_id.'&layers=epi');
			$obj2 = json_decode($json);

			foreach ($obj2 as $layer){
			  if ($layer->id == 1) { // biodiversity
			    $tmp = [];
			    $tmp['title'] = $layer->layerName;
			    $tmp['image'] = PLANDAT_PDF_URL . 'public/images/Biodiversity.png';
			    $tmp['map'] = 'https://api.apps1.nsw.gov.au/planning/arcgis/V1/rest/services/ePlanning/BiodiversityValuesMap/MapServer/export?bbox='.implode('%2C', $bbox).'&bboxSR=102100&imageSR=102100&size=1920%2C1039&dpi=96&format=png32&transparent=true&dynamicLayers=%5B%7B%22id%22%3A1%2C%22source%22%3A%7B%22mapLayerId%22%3A1%2C%22type%22%3A%22mapLayer%22%7D%2C%22drawingInfo%22%3A%7B%22showLabels%22%3Afalse%2C%22transparency%22%3A0%7D%7D%5D&f=image';
			    $tmp['info'] = '<b>What does it mean?: </b><br>
			      <p>The site is affected by Terrestrial Biodiversity, this is a NSW State Government initiative to protect sensitive lands. This mapping updates every 90-days. This can include sensitive trees and grasses on the site.</p>
			      <b>Implication on your build:</b><br>
			      <p>If you land is impacted by Terrestrial Biodiversity, you will likely need a Biodiversity Development Assessment Report (BDAR). These are prepared by an environmental consultant.</p>
			      <b>How to manage: </b><br>
			      <ul>
			        <li>Avoid proposing your construction within biodiversity mapped areas.</li>
			        <li>Propose the structure away from any sensitive trees, including the extent of their roots.</li>
			        <li>If you are proposing in these areas, a BDAR will be required, this wont necessarily prevent development but it is timely and costly.</li>
			      </ul>';
			    $site_info[] = $tmp;
			  }
			  else if ($layer->id == 229) { // bushfire
			    $tmp = [];
			    $tmp['title'] = $layer->layerName;
			    $tmp['image'] = PLANDAT_PDF_URL . 'public/images/Bushfire.png';
			    $tmp['map'] = 'https://api.apps1.nsw.gov.au/planning/arcgis/V1/rest/services/ePlanning/Planning_Portal_Hazard/MapServer/export?bbox='.implode('%2C', $bbox).'&bboxSR=102100&imageSR=102100&size=1920%2C1039&dpi=96&format=png32&transparent=true&dynamicLayers=%5B%7B%22id%22%3A229%2C%22source%22%3A%7B%22mapLayerId%22%3A229%2C%22type%22%3A%22mapLayer%22%7D%2C%22drawingInfo%22%3A%7B%22showLabels%22%3Afalse%2C%22transparency%22%3A0%7D%7D%5D&f=image';
			    $tmp['info'] = '<b>What does it mean?: </b><br>
			      <p>The site is effected by Bushfire zoning, which means a BAL (Bushfire Attack Level) will be determined. Additionally, you will need to adhere to fire rating requirements and specific setbacks to existing structures.</p>
			      <b>Implication on your build: </b><br>
			      <p>The proposed structure will need to be setback a minimum of 6m from the existing dwelling. A bushfire report may be required from a license Bushfire Consultant, or a bushfire self assessment may be requested: (https://www.rfs.nsw.gov.au/resources/publications/building-in-a-bush-fire-area/general/single-dwelling-application-kit)</p>
			      <b>How to manage: </b><br>
			      <ul>
			        <li>Build over 6m from the dwelling/existing structures on the site.</li>
			        <li>Investigate fire rated materials that may be requested by council or a certifier.</li>
			        <li>Investigate licenced bushfire consultants.</li>
			      </ul>';
			    $tmp_cats = [];
			    foreach ($layer->results as $result){
			      $tmp_title = str_replace("Vegetation Category  ", "", $result->title);

			      if (strlen($tmp_title) < strlen($result->title)){
			        $tmp_cats[$tmp_title] = 0;
			      }
			    }
			    if($tmp_cats){
			      ksort($tmp_cats);
			      $tmp['title'] = $tmp['title'].' - Category '.implode(', ',array_keys($tmp_cats));
			    }
			    $site_info[] = $tmp;
			  }

			}

			include PLANDAT_PDF_DIR . 'public/reports/plandat-test-report.php';

			die;

		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plandat_PDF_Reports_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
