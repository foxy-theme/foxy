<?php
/**
 * Abstract class for UI framework use to extends
 *
 * @package Foxy/Core
 * @subpackage UI
 * @author Puleeno Nguyen <puleeno@gmail.com>
 * @link https://wpclouds.com
 */

/**
 * Foxy_UI_Framework_Base class
 */
abstract class Foxy_UI_Framework_Base implements Foxy_UI_Framework_Interface {
	/**
	 * UI Framework version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Mobile class name prefix for tag generator
	 *
	 * @var string
	 */
	protected $mobile_class_prefix = '';

	/**
	 * Small tablet class name prefix for tag generator
	 *
	 * @var string
	 */
	protected $small_tablet_class_prefix = '';

	/**
	 * Tablet class name prefix for tag generator
	 *
	 * @var string
	 */
	protected $tablet_class_prefix = '';

	/**
	 * Desktop class name prefix for tag generator
	 *
	 * @var string
	 */
	protected $desktop_class_prefix = '';

	/**
	 * Large desktop class name prefix for tag generator
	 *
	 * @var string
	 */
	protected $xtra_class_prefix = '';

	/**
	 * Foxy_UI_Framework_Base constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 33 );
	}

	/**
	 * This method will register assets automatic by UI framework name
	 * Other plugin or theme function can integrate with foxy ui framework via filter & action hook.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! apply_filters( 'foxy_ui_framework_enqueue_scripts', false, $this->get_name(), $this->version ) ) {
			wp_enqueue_style( Foxy::get_template_name() );
			wp_enqueue_script( Foxy::get_template_name() );
			if ( is_child_theme() ) {
				wp_enqueue_style( Foxy::get_theme_name() );
				wp_enqueue_script( Foxy::get_theme_name() );
			}
		}
		do_action( 'foxy_ui_framework_enqueue_scripts' );
	}

	/**
	 * Open or close HTML tag with many option
	 *
	 * @param array $args Setting for tag.
	 * @param array $attributes Attributes list will be generated to HTML.
	 * @return string
	 */
	public function tag( $args = array(), $attributes = null ) {
		$context     = '';
		$args        = wp_parse_args(
			$args, array(
				'name'            => 'div',
				'id'              => '',
				'context'         => '',
				'class'           => '',
				'responsive'      => true,
				'mobile_columns'  => '',
				'small_tablet_columns'  => '',
				'tablet_columns'  => '',
				'desktop_columns' => '',
				'xtra_columns'    => '',
				'close'           => false,
				'echo'            => true,
			)
		);
		$class_names = $this->generate_class( $args );
		$id          = '';
		if ( ! empty( $args['id'] ) ) {
			$id          = sprintf( ' id="%s"', esc_attr( $args['id'] ) );
			if ( empty( $args['context'] ) ) {
				$args['context'] = $args['id'];
			}
		}
		if ( ! empty( $args['context'] ) ) {
			$context     = 'tag_' . $args['context'];
			$args        = apply_filters( "foxy_ui_tag_{$context}", $args, $context );
			$attributes  = apply_filters( "foxy_ui_tag_{$context}_attr", $attributes, $args, $context );
			$class_names = apply_filters( "foxy_ui_{$context}_class_name", $class_names );
		}

		if ( ! empty( $args['close'] ) ) {
			$tag = sprintf( '</%s>', $args['name'] );
		} else {
			$tag = sprintf(
				'<%1$s%2$s%3$s%4$s>',
				$args['name'],
				$id,
				$class_names ? ' ' . $class_names : '',
				$this->generate_attributes( $attributes )
			);
		}
		if ( empty( $args['echo'] ) ) {
			return $tag;
		}
		echo $tag; // WPCS: XSS ok.
	}

	public function generate_class( $args ) {
		$support_columns = array(
			'mobile' => 'mobile_columns',
			'small_tablet' => 'small_tablet_columns',
			'tablet' => 'tablet_columns',
			'desktop' => 'desktop_columns',
			'extra' => 'xtra_columns',
		);
		// Init class name.
		$class_names = $args['class'];
		foreach ( $support_columns as $prefix => $column ) {
			/**
			 * If column type is empty it will be skipped.
			 */
			if ( empty( $args[ $column ] ) ) {
				continue;
			}
			$class_names .= sprintf( ' %1$s%2$s', $this->{$prefix . '_class_prefix'}, $args[ $column ] );
		}

		if ( ! empty( $class_names ) ) {
			$class_names = sprintf(' class="%s"', trim( $class_names ) );
		}
		return apply_filters( 'foxy_ui_generate_class_names_output', $class_names, $support_columns, $args );
	}

	public function generate_attributes( $attributes = null ) {
		return '';
	}
}
