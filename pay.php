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
	TODO: todas las funciones getText __ cambiar a
	De __( 'PAGOS' )
	A __( 'Pays', 'redsys_direct_pay' )
	y crear archivo de traducciones
*/




function custom_entries() {
  register_post_type( 'payment',
    array(
      'labels' => array(
        'name'                => __( 'PAGOS' ),
        'singular_name'       => __( 'PAGO' ),
        'menu_name'           => __( 'PAGOS' ),
        'parent_item_colon'   => __( 'PAGO padre:' ),
        'all_items'           => __( 'Todos los Pagos' ),
        'view_item'           => __( 'Ver pago' ),
        'add_new_item'        => __( 'Añadir nuevo Pago' ),
        'add_new'             => __( 'Nuevo Pago' ),
        'edit_item'           => __( 'Editar Pago' ),
        'update_item'         => __( 'Actualizar Pago' ),
        'search_items'        => __( 'Buscar Pagos' ),
        'not_found'           => __( 'No se han encontrado pagos' ),
        'not_found_in_trash'  => __( 'No hay pagos en la papelera' )
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

add_filter( 'manage_edit-pays_columns', 'my_edit_pays_columns' ) ;

function my_edit_pays_columns( $columns ) {

	$columns = array(
		'cb'=> __( 'Seleccionar' ),
		'title' => __( 'Title' ),
		'email' => __( 'Email' ),
		'importe' => __( 'Importe' ),
        'autor' =>  __( 'Autor' ),
 		'date' => __( 'Date' )
	);

	return $columns;
}

add_action( 'manage_pays_posts_custom_column', 'my_manage_pays_columns', 10, 2 );

function my_manage_pays_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'duration' column. */
		case 'email' :

			/* Get the post meta. */
			$email = get_post_meta( $post_id, 'author_email_info', true );

			/* If no duration is found, output a default message. */
			if ( empty( $email ) )
				echo __( 'Unknown' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $email;

			break;

		/* If displaying the 'genre' column. */
		case 'importe' :

			/* Get the post meta. */
			$importe = get_post_meta( $post_id, 'author_importe_info', true );

			/* If no duration is found, output a default message. */
			if ( empty( $importe ) )
				echo __( 'Unknown' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $importe;

			break;

		case 'autor' :

			/* Get the post meta. */
		
			/* If no duration is found, output a default message. */
			if ( empty( $post->post_author ) )
				echo __( 'Unknown' );

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
	?>  

       
      
		<form id="formulariopago" name="frm" action="<?php echo $tpvurl; ?>" method="POST">

			<input class="required" type="text" id="thepost_name" name="post_name" size="75" value="<?php strip_tags(stripslashes($_POST['post_name'])) ?>" placeholder="Name" /></br>
			<input class="required" type="text" id="thesubmitters_email" name="submitters_email" size="75" value="<?php strip_tags(stripslashes($_POST['submitters_email'])) ?>" placeholder="Email" /></br>
			
			<textarea class="required" id="comentario" name="comentarios"rows="4" cols="50" placeholder="Coments" value="<?php $comentarios ?>" ></textarea></br>
			<input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
			<input type="hidden" id="Ds_MerchantParameters" name="Ds_MerchantParameters" value=""/>
			<input type="hidden" id="Ds_Signature" name="Ds_Signature" value=""/>
			<input class="required" type="text" id="Ds_Merchant_Amount" name="Ds_Merchant_Amount" value="" placeholder="Import" /></br>

			<button style="float:left;" class="btn btn-primary" type="button" onclick="javascript:doFormFinal()" /> Send</button>

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

// Do Pages

register_activation_hook( __FILE__, 'insert_page_ok' );
register_activation_hook( __FILE__, 'insert_page_ko' );
register_activation_hook( __FILE__, 'insert_page_pay' );

function insert_page_ok(){
    // Create post object
    $page_ok = array(
      'post_title'    => 'Pago OK',
      'post_content'  => '[redsys_direct_pay_page_ko]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    // Insert the post into the database
    wp_insert_post( $page_ok, '' );
}

function insert_page_ko(){
    // Create post object
    $page_ko = array(
      'post_title'    => 'Pago KO',
      'post_content'  => '[redsys_direct_pay_page_ko]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    wp_insert_post( $page_ko, '' );
}
function insert_page_pay(){
    // Create post object
    $direct_pay = array(
      'post_title'    => 'Pago directo',
      'post_content'  => '[redsys_direct_pay_page]',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    wp_insert_post( $direct_pay, '' );
}

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
		__( 'Clave SHA-256C', 'redsys_direct_pay' ), 
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
		__( 'Nombre del comercio', 'redsys_direct_pay' ), 
		'redsys_direct_text_commerce_name_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_ok', 
		__( 'Página pago OK', 'redsys_direct_pay' ), 
		'redsys_direct_select_pay_ok_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_ko', 
		__( 'Página pago KO', 'redsys_direct_pay' ), 
		'redsys_direct_select_pay_ko_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);
	add_settings_field( 
		'redsys_direct_select_pay_page', 
		__( 'Página de pago', 'redsys_direct_pay' ), 
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
		echo esc_attr( __( 'Select page' ) );
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
		echo esc_attr( __( 'Select page' ) );
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
		echo esc_attr( __( 'Select page' ) );
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
}

?>

