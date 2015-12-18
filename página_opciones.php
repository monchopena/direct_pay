<?php

    // Crear página pago OK
register_activation_hook( __FILE__, 'insert_page_ok' );
register_activation_hook( __FILE__, 'insert_page_ko' );
register_activation_hook( __FILE__, 'insert_page_pay' );

function insert_page_ok(){
    // Create post object
    $page_ok = array(
      'post_title'    => 'Pago OK',
      'post_content'  => '<b>Contenido de la Pago OK</b>',
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
      'post_content'  => '<b>Contenido de la Pago KO</b>',
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
      'post_content'  => 'aquí irá el shortcode',
      'post_status'   => 'publish',
      'post_author'   => get_current_user_id(),
      'post_type'     => 'page',
    );
    wp_insert_post( $direct_pay, '' );
}
    // Insert the post into the database



/* Página de opciones del plugin*/



add_action( 'admin_menu', 'redsys_direct_add_admin_menu' );
add_action( 'admin_init', 'redsys_direct_settings_init' );


function redsys_direct_add_admin_menu(  ) { 

	add_menu_page( 'Redsys_Direct', 'Redsys_Direct', 'manage_options', 'redsys_direct', 'redsys_direct_options_page' );

}


function redsys_direct_settings_init(  ) { 

	register_setting( 'pluginPage', 'redsys_direct_settings' );


	add_settings_section(
		'redsys_direct_pluginPage_section', 
		__( 'Settings', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'redsys_direct_select_field_0', 
		__( 'Entorno', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_0_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_1', 
		__( 'Clave SHA-256C', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_1_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_2', 
		__( 'Cod Comercio FUC', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_2_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_3', 
		__( 'Terminal', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_3_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_4', 
		__( 'Nombre del comercio', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_4_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_5', 
		__( 'Página pago OK', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_5_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_6', 
		__( 'Página pago KO', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_6_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_7', 
		__( 'Página de pago', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_7_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);




}


function redsys_direct_select_field_0_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_0]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_0'], 1 ); ?>>Real</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_0'], 2 ); ?>>Pruebas</option>
	</select>

<?php

}


function redsys_direct_text_field_1_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_1]' value='<?php echo $options['redsys_direct_text_field_1']; ?>'>
	<?php

}


function redsys_direct_text_field_2_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_2]' value='<?php echo $options['redsys_direct_text_field_2']; ?>'>
	<?php

}


function redsys_direct_text_field_3_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_3]' value='<?php echo $options['redsys_direct_text_field_3']; ?>'>
	<?php

}


function redsys_direct_text_field_4_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_4]' value='<?php echo $options['redsys_direct_text_field_4']; ?>'>
	<?php

}


function redsys_direct_select_field_5_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_5]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_5'], 1 ); ?>>Página kkk</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_5'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_select_field_6_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_6]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_6'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_6'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_select_field_7_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_7]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_7'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_7'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_settings_section_callback(  ) { 

	echo __( 'El plugin más molón Para Redsys', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' );

}


function redsys_direct_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Redsys_Direct</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

/* Página de opciones del plugin*/


add_action( 'admin_menu', 'redsys_direct_add_admin_menu' );
add_action( 'admin_init', 'redsys_direct_settings_init' );


function redsys_direct_add_admin_menu(  ) { 

	add_menu_page( 'Redsys_Direct', 'Redsys_Direct', 'manage_options', 'redsys_direct', 'redsys_direct_options_page' );

}


function redsys_direct_settings_init(  ) { 

	register_setting( 'pluginPage', 'redsys_direct_settings' );


	add_settings_section(
		'redsys_direct_pluginPage_section', 
		__( 'Settings', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'redsys_direct_select_field_0', 
		__( 'Entorno', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_0_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_1', 
		__( 'Clave SHA-256C', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_1_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_2', 
		__( 'Cod Comercio FUC', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_2_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_3', 
		__( 'Terminal', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_3_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_text_field_4', 
		__( 'Nombre del comercio', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_text_field_4_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_5', 
		__( 'Página pago OK', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_5_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_6', 
		__( 'Página pago KO', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_6_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);

	add_settings_field( 
		'redsys_direct_select_field_7', 
		__( 'Página de pago', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' ), 
		'redsys_direct_select_field_7_render', 
		'pluginPage', 
		'redsys_direct_pluginPage_section' 
	);




}


function redsys_direct_select_field_0_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_0]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_0'], 1 ); ?>>Real</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_0'], 2 ); ?>>Pruebas</option>
	</select>

<?php

}


function redsys_direct_text_field_1_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_1]' value='<?php echo $options['redsys_direct_text_field_1']; ?>'>
	<?php

}


function redsys_direct_text_field_2_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_2]' value='<?php echo $options['redsys_direct_text_field_2']; ?>'>
	<?php

}


function redsys_direct_text_field_3_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_3]' value='<?php echo $options['redsys_direct_text_field_3']; ?>'>
	<?php

}


function redsys_direct_text_field_4_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<input type='text' name='redsys_direct_settings[redsys_direct_text_field_4]' value='<?php echo $options['redsys_direct_text_field_4']; ?>'>
	<?php

}


function redsys_direct_select_field_5_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_5]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_5'], 1 ); ?>>Página kkk</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_5'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_select_field_6_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_6]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_6'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_6'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_select_field_7_render(  ) { 

	$options = get_option( 'redsys_direct_settings' );
	?>
	<select name='redsys_direct_settings[redsys_direct_select_field_7]'>
		<option value='1' <?php selected( $options['redsys_direct_select_field_7'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['redsys_direct_select_field_7'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function redsys_direct_settings_section_callback(  ) { 

	echo __( 'El plugin más molón Para Redsys', 'http://desarrolloroi.vl15501.dinaserver.com/desarrollo2/' );

}


function redsys_direct_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Redsys_Direct</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}


/* Página de opciones del plugin*/
