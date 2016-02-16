function doFormFinal(url) {

	var postname = jQuery('input#thepost_name').val();
	var submitters_email = jQuery('input#thesubmitters_email').val();
	var amount = jQuery('input#importe').val();  
	var comentario = jQuery('input#comentario').val();

	jQuery('#formulariopago').validate({
		rules: {
			post_name : {
			required : true},
			submitters_email : {
			required: true,
			email: true},
			Ds_Merchant_Amount:{
			required : true,
			number: true},
			comentarios:{
			required : true}
		},
		messages: { 
			post_name: {
			required : 'Insert your name'},
			submitters_email: {
			required: 'Insert your Email',
			email : 'This email is invalid'
			},
			Ds_Merchant_Amount :{
			required: 'Please insert a import',
			number: 'Please insert a valid import'},
			comentarios: {
			required: 'Please insert a comment'}
		}
	});

	var isvalidate=jQuery("#formulariopago").valid();
	//console.log('isvalidate: '+isvalidate);
	
	if (isvalidate===true) {
		var posting = jQuery.ajax({
		    type: 'POST',
		    url: url,
		    data: jQuery( '#formulariopago' ).serialize(),
		    dataType: 'json',
		    success: function( data ) {		
				var idPOST=data['idPOST'];  
				var signature=data['signature'];
				var params=data['params'];
				//alert('idPOST: ' + idPOST + ' - signature: ' +  signature + ' params: ' + params);
				//here signature
				jQuery('#Ds_MerchantParameters').val(params);
				jQuery('#Ds_Signature').val(signature);
				//let's go!
				jQuery('#formulariopago').submit();
			}								     
	  	});
	}
	
}
