<?php
/*
Plugin Name: KL Groups Restrict Categories Enforce
Plugin URI: https://bitbucket.org/educate-project/kl_groups-restrict-categories-enforce
Description: Wordpress plugin to enforce Groups Restrict Categories settings
Version: 0.1
Author: b.cunningham@ucl.ac.uk
Author URI: https://educate.london
License: GPL2
*/

require_once('kl-groups-restrict-categories-enforce-options.php');

function klgrce_install() {
	if (!get_option('klgrce_redirect')) { update_option('klgrce_redirect','/404'); }
	if (!get_option('klgrce_simulate')) { update_option('klgrce_simulate','1'); }

}

/* helper: get groups restricted for category */
function klgrce_get_groups_restrict_categories($category) {
    global $wpdb;
    
    return $wpdb->get_results( 
        '
            SELECT meta_value 
            FROM '.$wpdb->prefix.'termmeta  
            LEFT JOIN '.$wpdb->prefix.'terms ON '.$wpdb->prefix.'termmeta.term_id = '.$wpdb->prefix.'terms.term_id 
            WHERE '.$wpdb->prefix.'termmeta.meta_key = "groups-read" 
            AND '.$wpdb->prefix.'terms.slug = "'.$category.'";'
    );
}

/* helper: get user's groups ids */
function klgrce_get_user_groups($user_id = null) {
	if (!$user_id) { $user_id = get_current_user_id(); }
	// ref http://docs.itthinx.com/document/groups/api/examples/
	$groups = array(); // to populate
	$groups_user = new Groups_User( $user_id );
	// get group objects
	$user_groups = $groups_user->groups;
	// get group ids (user is direct member)
	$user_group_ids = $groups_user->group_ids;
	// get group ids (user is direct member or by group inheritance)
	$user_group_ids_deep = $groups_user->group_ids_deep;	
	return $user_group_ids_deep;
}


function klgrce($template) {
	global $post;
//	echo $post->ID;
//	echo get_the_ID();

	// only non-administrator levels
	if (!current_user_can('administrator')) {
		$allowed = true;
		foreach((get_the_category($post->ID)) as $category) {
			$group_allows = klgrce_get_groups_restrict_categories($category->slug);
			// if any set, default to disallow
			if (!empty($group_allows)) { 
				$allowed = false; 
			}
			$user_group_ids = klgrce_get_user_groups();
			// check for allow
			foreach ($group_allows as $group_allow) {
				foreach ($user_group_ids as $user_group_id) {
					if ((int) $group_allow->meta_value == (int) $user_group_id) {
						$allowed = true;
						break(3);
					}
				}
			}
		}
		if (get_option('klgrce_simulate')) {
			echo($allowed)?'<!-- Allowed -->':'<!-- Not allowed -->';			
		} else {
			if (!$allowed) {
				wp_redirect( get_option('klgrce_redirect'));
				exit;
			}		
		}
	}

	return $template;
}

register_activation_hook( __FILE__, 'klgrce_install');
//add_action( 'init', 'klgrce' );
add_filter( 'template_include','klgrce');