<?php
/*
Plugin Name: Redsys Pago Directo
Description: ¡Pago directo y sencillo con RedSys!
Version: 1.0
Author: Roi Facal y Moncho Pena
Author URI: https://codigo.co.uk
*/

/*
	
	TODO
	Nota: donde ponga "TODO", antes de subirlo hay que arreglarlo (¡No olvidarse de borrar todos estos comentarios!)
	
*/

/*
	Translations
*/

add_action('plugins_loaded', 'wan_load_textdomain');
function wan_load_textdomain() {
	load_plugin_textdomain( 'redsys_direct_pay', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

function custom_entries() {
  register_post_type( 'payment',
    array(
      'labels' => array(
        'name'                => __( 'Pays', 'redsys_direct_pay'),
        'singular_name'       => __( 'Pay', 'redsys_direct_pay' ),
        'menu_name'           => __( 'Pays', 'redsys_direct_pay' ),
        'all_items'           => __( 'All pays', 'redsys_direct_pay' ),
        'view_item'           => __( 'View pay', 'redsys_direct_pay' ),
        'add_new_item'        => __( 'Add new pay', 'redsys_direct_pay' ),
        'add_new'             => __( 'New Pay', 'redsys_direct_pay' ),
        'edit_item'           => __( 'Edit pay', 'redsys_direct_pay' ),
        'update_item'         => __( 'Update Pay', 'redsys_direct_pay' ),
        'search_items'        => __( 'search Pay', 'redsys_direct_pay' ),
        'not_found'           => __( 'Not found pays', 'redsys_direct_pay' ),
        'not_found_in_trash'  => __( 'No payments in bin', 'redsys_direct_pay' )
      ),
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'rewrite'         => array( 'slug' => 'pagos' ),
    'query_var' => true,
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor','author','subscriber', 'thumbnail','excerpt','comments')
    
    )
  );
}

add_action( 'init', 'custom_entries' );

/*
	SHOW in Backend
*/

add_filter( 'manage_edit-payment_columns', 'my_edit_payment_columns' ) ;

function my_edit_payment_columns( $columns ) {

	$columns = array(
		'cb'=> __( 'Select', 'redsys_direct_pay' ),
		'title' => __( 'Title', 'redsys_direct_pay' ),
		'email' => __( 'Email', 'redsys_direct_pay' ),
		'import' => __( 'Amount', 'redsys_direct_pay' ),
        'author' =>  __( 'Author', 'redsys_direct_pay' ),
        'signature' =>  __( 'Signature', 'redsys_direct_pay' ),
 		'date' => __( 'Date', 'redsys_direct_pay' )
	);

	return $columns;
}

add_action( 'manage_payment_posts_custom_column', 'my_manage_payment_columns', 10, 2 );

function my_manage_payment_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		case 'email' :

			/* Get the post meta. */
			$email = get_post_meta( $post_id, 'redsys_direct_email', true );

			/* If no duration is found, output a default message. */
			if ( empty( $email ) )
				echo __( 'Unknown', 'redsys_direct_pay' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $email;

			break;

		case 'import' :

			/* Get the post meta. */
			
			$import = get_post_meta( $post_id, 'redsys_direct_import', true );

			/* If no duration is found, output a default message. */
			if ( empty( $import) )
				echo __( 'Unknown', 'redsys_direct_pay' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $import;

			break;
			
		case 'signature':
		
			/* Get the post meta. */
			$signature = get_post_meta( $post_id, 'redsys_direct_signature', true );

			/* If no duration is found, output a default message. */
			if ( empty( $signature ) )
				echo __( 'Unknown', 'redsys_direct_pay' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $signature;

			break;
		

		case 'author' :

			/* Get the post meta. */
		
			/* If no duration is found, output a default message. */
			if ( empty( $post->post_author ) )
				echo __( 'Unknown', 'redsys_direct_pay' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				$user_id=$post->post_author;
				echo get_the_author_meta( 'display_name', $user_id );
			break;
		
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

/*
	Adding JS
*/

function my_scripts_method() {
    wp_enqueue_script( 'jquery_validate', plugins_url( '/js/jquery.validate.min.js' , __FILE__ ), array(  ) );
    wp_enqueue_script( 'redsys_direct_pay', plugins_url( '/js/redsys_direct_pay.js' , __FILE__ ), array( ) );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

/*
		
*/

function build_redsys_direct_pay_page() {
		

	   $version="HMAC_SHA256_V1";
	   
	   $options = get_option( 'redsys_direct_settings' );

	   
	   if ($options['redsys_direct_select_environment']==2) {
		   $tpvurl="https://sis-t.redsys.es:25443/sis/realizarPago";
	   } else {
		   $tpvurl="https://sis.redsys.es/sis/realizarPago";
	   }
	   //
	   
       $descripcion_producto_tpv='Payment';
       $texto_submit= 'Payment';
       echo '<div class="invitados_texto_form"><p>Pagar</p></div><br>
'; 
 		/*
	 		BEGIN shortcode redsys_direct_pay_page
	 	*/
	 	
	 	 $url = plugins_url() . '/redsys_direct_pay/pay_ajax.php';
	 	 
	 	 $test_mode=1;
	 	 
	 	 $post_name='';
	 	 $submitters_email='';
	 	 $comentarios='';
	 	 $Ds_Merchant_Amount='';
	 	 
	 	 if ($test_mode==0) {
		 	 $post_name='Xoan Rodriguez';
		 	 $submitters_email='xoan@rodriguez.com';
		 	 $comentarios='No comments';
		 	 $Ds_Merchant_Amount=221;
	 	 }
	 	 
	 	 
	?>  
      
		<form id="formulariopago" name="frm" action="<?php echo $tpvurl; ?>" method="POST">

			<input class="required" type="text" id="thepost_name" name="post_name" size="75" value="<?php echo strip_tags(stripslashes($post_name)) ?>" placeholder="Name" /></br>
			<input class="required" type="text" id="thesubmitters_email" name="submitters_email" size="75" value="<?php echo strip_tags(stripslashes($submitters_email)) ?>" placeholder="Email" /></br>
			
			<textarea class="required" id="comentario" name="comentarios"rows="4" cols="50" placeholder="Coments"><?php echo strip_tags(stripslashes($comentarios)) ?></textarea></br>
			<input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
			<input type="hidden" id="Ds_MerchantParameters" name="Ds_MerchantParameters" value=""/>
			<input type="hidden" id="Ds_Signature" name="Ds_Signature" value=""/>
			<input class="required" type="text" id="Ds_Merchant_Amount" name="Ds_Merchant_Amount" value="<?php echo strip_tags(stripslashes($Ds_Merchant_Amount)) ?>" placeholder="Import" /></br>

			<button style="float:left;" class="btn btn-primary" type="button" onclick="javascript:doFormFinal('<?echo $url ?>')" /> Send</button>

		</form>

	
	<?php	
		
}		

add_shortcode('redsys_direct_pay_page', 'build_redsys_direct_pay_page');

function build_redsys_direct_pay_page_ko() {

 		/*
	 		BEGIN shortcode redsys_direct_pay_page_ko
	 	*/
	 	
	 	$options = get_option( 'redsys_direct_settings' );
	 	
	 	require_once ('apiRedsys.php');
	 	
	 	$miObj = new RedsysAPI;

	    $kc = $options['redsys_direct_text_key_sha_256c'];
		$params=$_GET['Ds_MerchantParameters'];
		$new_signature = $miObj->createMerchantSignatureNotif($kc, $params);	
		$signature=$_GET['Ds_Signature'];
		
		$test_signature=0;
		if ($new_signature == $signature) {
			$test_signature=1;
		}
		
		$myOrder=$miObj->getParameter('Ds_Order');
		$myOrder=$myOrder-1000;
		
		$post_pendiente = array(
		  'ID'           => $myOrder,
		  'post_type'=> 'payment',
		  'post_status'   => 'Draft',
		);
		$post_id = wp_update_post($post_pendiente);
	 	
	?>  

       <?php
	     if  ($test_signature == 0) {
	    ?>
	    	<p>¡Fallo en la firma!</p>
	   <?php
	     } 
	   ?>
      
	   <p>El Pago con número <?php echo $myOrder; ?> ha fallado por favor inténtelo otra vez</p>
	   <p><a href="<?php echo get_permalink($options['redsys_direct_select_pay_page']); ?>">Volver</a></p>
	
	<?php	
		
}		

add_shortcode('redsys_direct_pay_page_ko', 'build_redsys_direct_pay_page_ko');


function build_redsys_direct_pay_page_ok() {

 		/*
	 		BEGIN shortcode redsys_direct_pay_page_ko
	 	*/
	 	
	 	$options = get_option( 'redsys_direct_settings' );
	 	
	 	require_once ('apiRedsys.php');
	 	
	 	$miObj = new RedsysAPI;

	    $kc = $options['redsys_direct_text_key_sha_256c'];
		$params=$_GET['Ds_MerchantParameters'];
		$new_signature = $miObj->createMerchantSignatureNotif($kc, $params);	
		$signature=$_GET['Ds_Signature'];
		
		$test_signature=0;
		if ($new_signature == $signature) {
			$test_signature=1;
		}
		
		$myOrder=$miObj->getParameter('Ds_Order');
		$myOrder=$myOrder-1000;
		
		$post_pendiente = array(
		  'ID'           => $myOrder,
		  'post_type'=> 'payment',
		  'post_status'   => 'Publish',
		);
		$post_id = wp_update_post($post_pendiente);
	 	
	?>  

       <?php
	     if  ($test_signature == 0) {
	    ?>
	    	<p>¡Fallo en la firma!</p>
	   <?php
	     } 
	   ?>
      
	   <p>El Pago con número <?php echo $myOrder; ?> ha sido realizado correctamente</p>

	
	<?php	
		
}		

add_shortcode('redsys_direct_pay_page_ok', 'build_redsys_direct_pay_page_ok');


/*  
	TODO: ¿Esto es necesario?
	Quitar barra de admin
*/

add_filter( 'show_admin_bar', '__return_false' );



/*
	SETTINGS Backend
*/

add_action( 'admin_menu', 'redsys_direct_add_admin_menu' );
add_action( 'admin_init', 'redsys_direct_settings_init' );
function redsys_direct_add_admin_menu(  ) { 
	add_menu_page( 'Redsys Pago Directo', 'Redsys Pago Directo', 'manage_options', 'redsys_direct', 'redsys_direct_options_page' );
}
function redsys_direct_settings_init(  ) { 
	register_setting( 'pluginPage', 'redsys_direct_settings' );
	add_settings_section(
		'redsys_direct_pluginPage_section', 
		__( 'Settings', 'redsys_direct_pay' ), 
		'redsys_direct_settings_section_callback', 
		'pluginPage'
	);
	add_settings_field( 
		'redsys_direct_select_environment', 
		__( 'Entorno', 'redsys_direct_pay' ), 
		'redsys_direct_select_environment_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_text_key_sha_256c', 
		__( 'Key SHA-256C', 'redsys_direct_pay' ), 
		'redsys_direct_text_key_sha_256c_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_text_commerce_fuc', 
		__( 'Cod Comercio FUC', 'redsys_direct_pay' ), 
		'redsys_direct_text_commerce_fuc_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_text_terminal', 
		__( 'Terminal', 'redsys_direct_pay' ), 
		'redsys_direct_text_terminal_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_text_commerce_name', 
		__( 'Commerce name', 'redsys_direct_pay' ), 
		'redsys_direct_text_commerce_name_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_ok', 
		__( 'OK Page', 'redsys_direct_pay' ), 
		'redsys_direct_select_pay_ok_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_ko', 
		__( 'KO Page', 'redsys_direct_pay' ), 
		'redsys_direct_select_pay_ko_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_page', 
		__( 'Pay Page', 'redsys_direct_pay' ), 
		'redsys_direct_select_pay_page_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
}
function redsys_direct_select_environment_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_environment]'>
		<option value='1' <?php selected( $options['redsys_direct_select_environment'], 1 ); ?>>Real</option>
		<option value='2' <?php selected( $options['redsys_direct_select_environment'], 2 ); ?>>Pruebas</option>
	</select>

<?php
}
function redsys_direct_text_key_sha_256c_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_key_sha_256c]' value='<?php echo $options['redsys_direct_text_key_sha_256c']; ?>'>
	<?php
}
function redsys_direct_text_commerce_fuc_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_commerce_fuc]' value='<?php echo $options['redsys_direct_text_commerce_fuc']; ?>'>
	<?php
}
function redsys_direct_text_terminal_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_terminal]' value='<?php echo $options['redsys_direct_text_terminal']; ?>'>
	<?php
}
function redsys_direct_text_commerce_name_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_commerce_name]' value='<?php echo $options['redsys_direct_text_commerce_name']; ?>'>
	<?php
}
function redsys_direct_select_pay_ok_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );

	$temp_option_id=$options['redsys_direct_select_pay_ok'];
	$temp_option_value='';
	
	if ($temp_option_id>0) {
		$temp_option_value=get_the_title($temp_option_id);
	}
	
?>

<?php

/*
	TODO: en estas funciones se tendría que sacar fuera pages, es decir siempre son las mismas
	también habría que pensar en sacar la variable $options.
*/
	
?>

<select name='redsys_direct_settings[redsys_direct_select_pay_ok]'> 
<option value="<?php echo $temp_option_id; ?>">

<?php

	if ($temp_option_value!='') {
		echo $temp_option_value;
	} else {
		echo esc_attr( __( 'Select page', 'redsys_direct_pay' ) );
	}

?>

</option> 
<?php 
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		$option = '<option value="' . $page->ID . '">';
		$option .= $page->post_title;
		$option .= '</option>';
	echo $option;
}
?>
</select>

<?php
}
function redsys_direct_select_pay_ko_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );

	
	$temp_option_id=$options['redsys_direct_select_pay_ko'];
	$temp_option_value='';
	if ($temp_option_id>0) {
		$temp_option_value=get_the_title($temp_option_id);
	}

?>

<select name='redsys_direct_settings[redsys_direct_select_pay_ko]'> 

	<option value="<?php echo $temp_option_id; ?>">
	
	<?php
	
	if ($temp_option_value!='') {
		echo $temp_option_value;
	} else {
		echo esc_attr( __( 'Select page', 'redsys_direct_pay' ) );
	}
	?>
	
	</option> 
<?php 
	$pages = get_pages(); 
	foreach ( $pages as $page ) {
		$option = '<option value="' .$page->ID . '">';
		$option .= $page->post_title;
		$option .= '</option>';
		echo $option;
	}
?>
</select>

<?php
}

function redsys_direct_select_pay_page_render(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	
	
    $temp_option_id=$options['redsys_direct_select_pay_page'];
	$temp_option_value='';
	if ($temp_option_id>0) {
		$temp_option_value=get_the_title($temp_option_id);
	}
	?>
	
<select name='redsys_direct_settings[redsys_direct_select_pay_page]'> 
	<option value="<?php echo $temp_option_id; ?>">
	
	<?php
	
	if ($temp_option_value!='') {
		echo $temp_option_value;
	} else {
		echo esc_attr( __( 'Select page', 'redsys_direct_pay' ) );
	}

	?>
	
	</option> 

<?php
$pages = get_pages();
	foreach ( $pages as $page ) {
		$option = '<option value="' . $page->ID . '">';
		$option .= $page->post_title;
		$option .= '</option>';
		echo $option;
	}
?>
</select>

<?php
}
function redsys_direct_settings_section_callback(  ) { 
	echo __( 'Plugin de Pago Directo para Redsys', 'redsys_direct_pay' );
}
function redsys_direct_options_page(  ) { 
	$options = get_option( 'redsys_direct_settings' );
	$url_pay = get_permalink($options['redsys_direct_select_pay_page']);
	
	?>
	<form action='options.php' method='post'>
		
		<h2>Redsys Pago Directo</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	
	<?php
		if ($url_pay!='') {	
	?>
		<p><a href="<?echo $url_pay;?>">Go to Pay page</a></p>
	
	<?php
		}	
	?>
	
	<?php
}


// Do Pages

register_activation_hook( __FILE__, 'insert_pages' );

function insert_pages(){
	
	$page_ok = array(
      'post_title'    => 'Pago OK',
      'post_content'  => '[redsys_direct_pay_page_ok]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    // Insert the post into the database
    $id_page_ok=wp_insert_post( $page_ok, '' );
    
    $page_ko = array(
      'post_title'    => 'Pago KO',
      'post_content'  => '[redsys_direct_pay_page_ko]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    $id_page_ko=wp_insert_post( $page_ko, '' );
    
    $direct_pay = array(
      'post_title'    => 'Pago directo',
      'post_content'  => '[redsys_direct_pay_page]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    $id_page_pay=wp_insert_post( $direct_pay, '' );  
    
    $array_of_options = array(
    'redsys_direct_select_pay_ok' => $id_page_ok,
    'redsys_direct_select_pay_ko' => $id_page_ko,
    'redsys_direct_select_pay_page' => $id_page_pay
	);
	
	update_option( 'redsys_direct_settings', $array_of_options );
	
}

?>
