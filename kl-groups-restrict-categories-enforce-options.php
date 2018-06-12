<?php
/*
KL Groups Restrict Categories Enforce Options
Author: b.cunningham@ucl.ac.uk
Author URI: https://educate.london
License: GPL2
*/

// create custom plugin settings menu
add_action('admin_menu', 'klgrce_plugin_create_menu');

function klgrce_plugin_create_menu() {
    //create options page
    add_options_page('KL Groups Restrict Categories Enforce', 'KL Groups Restrict Categories Enforce', 'manage_options', __FILE__, 'klgrce_plugin_settings_page' , __FILE__ );

    //call register settings function
    add_action( 'admin_init', 'register_klgrce_plugin_settings' );	
}

function register_klgrce_plugin_settings() {
    //register our settings
    register_setting( 'klgrce-plugin-settings-group', 'klgrce_redirect' );	    
    register_setting( 'klgrce-plugin-settings-group', 'klgrce_simulate' );	    
}

function klgrce_plugin_settings_page() {
?>
    <div class="wrap">
    <h1>KL Groups Restrict Categories Enforce</h1>

    <form method="post" action="options.php">
    <?php settings_fields( 'klgrce-plugin-settings-group' ); ?>
    <?php do_settings_sections( 'klgrce-plugin-settings-group' ); ?>
    <table class="form-table">
        
        <tr valign="top">
        <th scope="row">Redirect URL</th>
        <td>
        	<input type="text" name="klgrce_redirect" value="<?php echo esc_attr( get_option('klgrce_redirect') ); ?>"  />
        	<p><small>URL to redirect to if disallow.</small></p>
        </td>
        </tr>        
        
        <tr valign="top">
        <th scope="row">Simulate</th>
        <td>
        	<input type="checkbox" name="klgrce_simulate" value="1" <?php if( get_option('klgrce_simulate')) { echo ' checked '; }  ?>  />
        	<p><small>If simulate, output html comments with info rather than enforce restrictions.</small></p>
        </td>
        </tr>                
                            
    </table>
    
    <?php submit_button(); ?>
    </form>

</div>
<?php } 