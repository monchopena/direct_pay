<?php

/*

*/


include_once('../../../wp-config.php');
include_once('../../../wp-includes/post.php');
//require(dirname(__FILE__) . '/wp-load.php'); 

// necesitamos la librería
require_once ('apiRedsys.php');


$options = get_option( 'redsys_direct_settings' );

/*
$options['redsys_direct_select_environment'];
$options['redsys_direct_text_key_sha_256c'];
$options['redsys_direct_text_commerce_fuc'];
$options['redsys_direct_text_terminal'];
$options['redsys_direct_text_commerce_name'];
$options['redsys_direct_select_pay_ok'];
$options['redsys_direct_select_pay_ko'];
$options['redsys_direct_select_pay_page'];
*/

//only for tests
//use tail -f /var/log/syslog
$debug_mode=0;

//a simple function for logs
function doDebug($debug_mode, $msg) {
	if ($debug_mode==1) {
		//syslog(LOG_DEBUG, $msg);
		error_log($msg);
	}
}

//open log
if ($debug_mode==1) {
	openlog('pay-me-log', 0, LOG_LOCAL0);
}

//init array
if ($debug_mode==1) {
	$result=array(
				'idPOST' => 0,
				'signature' => '',
				'params' => '',
				'kc' => '',
				'fuc' => '',
				'DS_MERCHANT_AMOUNT' => 0,
				'DS_MERCHANT_ORDER' => 0,
				'DS_MERCHANT_MERCHANTCODE' => '',
				'DS_MERCHANT_CURRENCY' => '',
				'DS_MERCHANT_TRANSACTIONTYPE' => '',
				'DS_MERCHANT_TERMINAL' => '',
				'DS_MERCHANT_MERCHANTURL' => '',
				'DS_MERCHANT_URLOK' => '',
				'DS_MERCHANT_URLKO' =>''
			 );
} else {

$result=array(
				'idPOST' => 0,
				'signature' => '',
				'params' => ''
			 );
}

//
doDebug($debug_mode, '$_POST[\'post_name\']: ' . $_POST['post_name']);
doDebug($debug_mode, '$_POST[\'submitters_email\']: ' . $_POST['submitters_email']);
doDebug($debug_mode, '$_POST[\'Ds_Merchant_Amount\']: ' . $_POST['Ds_Merchant_Amount']);
doDebug($debug_mode, '$_POST[\'comentarios\']: ' . $_POST['comentarios']);
//doDebug($debug_mode, '$_POST[\'Ds_Merchant_Order\']: ' . $_POST['Ds_Merchant_Order']);

//we have all?
if (isset($_POST['post_name']) && $_POST['post_name']!='' &&
	isset($_POST['submitters_email']) && $_POST['submitters_email']!='' &&
	isset($_POST['Ds_Merchant_Amount']) && $_POST['Ds_Merchant_Amount']!='' &&
	isset($_POST['comentarios']) && $_POST['comentarios']!=''
	/* 
	&&
	isset($_POST['Ds_Merchant_Order']) && $_POST['Ds_Merchant_Order']!=''
	*/
	) {
	
	$post_name = $_POST['post_name'];
	$invitado_email= $_POST['submitters_email'];
	$Importe = $_POST['Ds_Merchant_Amount'];
	$Importe = $Importe * 100;
	$comentario= $_POST['comentarios'];
	$pedidonum= $_POST['Ds_Merchant_Order'];
	
	$post_pendiente = array(
	  'post_type'=> 'payment',
	  'post_title'    => $post_name,
	  'post_content'  => $comentario,
	  'post_status'   => 'Pending',
	  'post_author'   => 1,
	);

	doDebug($debug_mode, '$post_pendiente[\'post_type\']: ' . $post_pendiente['post_type']);
	doDebug($debug_mode, '$post_pendiente[\'post_title\']: ' . $post_pendiente['post_title']);
	doDebug($debug_mode, '$post_pendiente[\'post_content\']: ' . $post_pendiente['post_content']);
	doDebug($debug_mode, '$post_pendiente[\'post_status\']: ' . $post_pendiente['post_status']);
	doDebug($debug_mode, '$post_pendiente[\'post_author\']: ' . $post_pendiente['post_author']);

	$wp_post_id = wp_insert_post($post_pendiente);

	$newImporte=$Importe/100;

	if ( $wp_post_id > 0) {
	      // Add our custom fields	   
	      add_post_meta($wp_post_id, 'redsys_direct_email', strip_tags(stripslashes($invitado_email)));
	      add_post_meta($wp_post_id, 'redsys_direct_import', strip_tags(stripslashes($newImporte)));
	}
	
		
	$miObj = new RedsysAPI;
	
	// Valores de entrada
	$fuc=$options['redsys_direct_text_commerce_fuc'];
	//$fuc="qwertyasdf0123456789";
	$terminal=$options['redsys_direct_text_terminal'];
	$moneda="978";
	//$moneda="000";
	$trans="0";
	//$url=// ??
	$url="";
	
	/*
		
		1.- Realice al menos una operación Autorizada. Utilice esta tarjeta de prueba:

		Número de tarjeta	4548 8120 4940 0004
		Caducidad	12/20
		Código CVV2	123
		Código CIP	123456
		2.- Realice al menos una operación Denegada. Utilice esta tarjeta de prueba:
		
		Número de tarjeta	1111111111111117
		Caducidad	12/20
		
	*/

	$urlOK = get_permalink($options['redsys_direct_select_pay_ok']);
	$urlKO = get_permalink($options['redsys_direct_select_pay_ko']);
    
  
	$my_order=strval($wp_post_id)+1000;
	
	// Se Rellenan los campos
	$miObj->setParameter("DS_MERCHANT_AMOUNT",$Importe); //EL IMPORTE
	$miObj->setParameter("DS_MERCHANT_ORDER", $my_order);
	$miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
	$miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
	$miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
	$miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
	$miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
	$miObj->setParameter("DS_MERCHANT_URLOK",$urlOK);		
	$miObj->setParameter("DS_MERCHANT_URLKO",$urlKO);
	
	//Datos de configuración
	$version="HMAC_SHA256_V1";
		
	$kc = $options['redsys_direct_text_key_sha_256c'];
	$request = "";
	$params = $miObj->createMerchantParameters();
	$signature = $miObj->createMerchantSignature($kc);	
	
	doDebug($debug_mode, '$signature: ' . $signature);
	
	
	if ($debug_mode==1) {
		$result=array(
					'idPOST' => $wp_post_id,
					'signature' => $signature,
					'params' => $params, 
					'kc' => $kc,
					'fuc' => $fuc,
 					'DS_MERCHANT_AMOUNT' => $Importe,
					'DS_MERCHANT_ORDER' => $my_order,
					'DS_MERCHANT_MERCHANTCODE' => $fuc,
					'DS_MERCHANT_CURRENCY' => $moneda,
					'DS_MERCHANT_TRANSACTIONTYPE' => $trans,
					'DS_MERCHANT_TERMINAL' => $terminal,
					'DS_MERCHANT_MERCHANTURL' => $url,
					'DS_MERCHANT_URLOK' => $urlOK,
					'DS_MERCHANT_URLKO' => $urlKO
				 );
	} else {
	
		
		$result=array(
					'idPOST' => $wp_post_id,
					'signature' => $signature,
					'params' => $params
	
				 );
	
	}			 
	if ( $wp_post_id > 0) {
	      // Add our custom fields	   
	      add_post_meta($wp_post_id, 'redsys_direct_signature', strip_tags(stripslashes($signature)));
	}
	
	 
} else {
	doDebug($debug_mode, 'Where is the data?');
}

//close log file
if ($debug_mode==1) {
	closelog();
}

echo json_encode($result);
?>
