<?php
interface Foxy_Option_Interface {
	public function get( $option_name, $default_value = false );

	public function update( $option_name, $option_value );

	public function add_section();

	public function add_tab();

	public function add_field();
}