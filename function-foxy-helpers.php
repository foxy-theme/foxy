<?php
/**
 * @package Foxy/Core
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @license GPL
 * @link https://wpclouds.com
 */

function foxy_get_domain_name( $host ) {
	/**
	 * Last dot in host name
	 */
	$last_dot = strrpos( $host, '.' );
	if ( false === $last_dot ) {
		return false;
	}

	/**
	 * The dot separates the subdomain and the domain name
	 */
	$offset        = strlen( $host ) - $last_dot + 1;
	$subdomain_dot = strrpos( $host, '.', -$offset );

	if ( false === $subdomain_dot ) {
		$domain_name = substr( $host, 0, $last_dot );
	} else {
		$subdomain_dot++;
		$domain_name = substr( $host, $subdomain_dot, $last_dot - $subdomain_dot );
	}
	return $domain_name;
}


/**
 * Check action & filter hooks is empty callback
 *
 * @param string $hook_name Hook name need to check is empty.
 * @return bool
 */
function foxy_check_empty_hook( $hook_name ) {
	global $wp_filter;

	/**
	 * If object doesn't exists this mean hook is empty
	 */
	if ( empty( $wp_filter[ $hook_name ] ) ) {
		return true;
	}

	return ! isset( $wp_filter[ $hook_name ]->callbacks ) && count( $wp_filter[ $hook_name ]->callbacks ) > 0;
}


function foxy_get_theme_name() {
	return foxy_make_slug( basename( FOXY_ACTIVE_THEME_DIR ) );
}

function foxy_get_template_name() {
	return foxy_make_slug( basename( FOXY_TEMPLATE_DIR ) );
}

/**
 * Create slug for post type, taxonomy or others
 *
 * @param string $source Source need to make slug.
 * @return string
 */
function foxy_make_slug( $source ) {
	return preg_replace(
		'/_/',
		'-',
		sanitize_title( $source )
	);
}


function foxy_get_object_id( $object_or_id, $class_name ) {
	if ( is_numeric( $object_or_id ) ) {
		return $object_or_id;
	} elseif ( is_null( $object_or_id ) ) {
		return foxy_get_current_object_id( $class_name );
	} else {
		if (
			in_array(
				$class_name,
				array( 'WP_User', 'WP_Post', 'WP_Term' ),
				true
			)
			&& $object_or_id instanceof $class_name
		) {
			return $object_or_id->ID;
		}
	}
	return 0;
}

function foxy_get_current_object_id( $class_name ) {
	$current_id = 0;
	switch ( $class_name ) {
		case 'WP_Post':
			$current_id = get_the_ID();
			break;
		case 'WP_User';
			$current_id = get_current_user_id();
		break;
	}
	return apply_filters( 'foxy_get_current_object_id', $current_id, $class_name );
}

/**
 * Undocumented function
 *
 * @param [type] $partial_file
 * @return void
 */
function foxy_get_partial_info( $partial_file ) {
	return get_file_data(
		$partial_file,
		array(
			'Name'        => 'Partial Name',
			'PluginURI'   => 'Partial URI',
			'Version'     => 'Version',
			'Description' => 'Description',
			'Author'      => 'Author',
			'AuthorURI'   => 'Author URI',
			'TextDomain'  => 'Text Domain',
			'DomainPath'  => 'Domain Path',
		)
	);
}

function foxy_filter_post_type_metas( $post_type, $metas ) {
	$results = array();
	foreach ( $metas as $id => $args ) {
		if ( ! in_array( $post_type, (array) $args['post_type'], true ) ) {
			// Free up memory.
			unset( $metas[ $id ] );
			continue;
		}
		$results = array_merge( $results, $args['fields'] );
		// Free up memory.
		unset( $metas[ $id ] );
	}
	return $results;
}


function foxy_group_all_meta_fields( $original_fields ) {
	$tabs   = array();
	$fields = array();
	foreach ( $original_fields as $field ) {
		if ( 'tab' === $field['type'] ) {
			$tabs = array_merge( $tabs, $field );
		} else {
			if ( ! empty( $field['tab'] ) ) {
				$fields[ $field['tab'] ][] = $field;
			} else {
				$fields['fxng'][] = $field;
			}
		}
	}
	return array( $tabs, $fields );
}


function array_get( $arr, $index, $default_value = false, $check_empty = false ) {
	if ( isset( $arr[ $index ] ) && ( empty( $check_empty ) || ! empty( $arr[ $index ] ) ) ) {
		return $arr[ $index ];
	}
	return $default_value;
}

function array_set_values( &$dest_arr, $values ) {
	foreach ($values as $key1 => $key2) {
		if (is_numeric($key)) {
			$key = $key2;
			$value = '';
		} else {
			$key = $key1;
			$value = $key2;
		}
		if(!isset($dest_arr[$key])) {
			$dest_arr[$key]= $value;
		}
	}
}