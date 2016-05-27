<?php

namespace Carbon_Fields\Field;

class Number_Field extends Field {
	/*
	 * Properties
	 */
	protected $default_min = 1;
	protected $default_max = 2147483647;
	protected $default_truncate = 0;
	protected $default_step = 1;

	protected $min = 1;
	protected $max = 2147483647;
	protected $truncate = 0;
	protected $step = 1;

	function to_json($load) {
		$field_data = parent::to_json($load);

		$field_data = array_merge($field_data, array(
			'min' => is_numeric($this->min) ? $this->min : $this->default_min,
			'max' => is_numeric($this->max) ? $this->max : $this->default_max,
			'step' => is_numeric($this->step) ? $this->step : $this->default_step,
			'truncate' => is_int($this->truncate) ? $this->truncate : $this->default_truncate,
		));

		return $field_data;
	}
	
	/**
	 * template()
	 *
	 * Prints the main Underscore template
	 **/
	function template() {
		?>
		<input id="{{{ id }}}" type="number" name="{{{ name }}}" value="{{ value }}" class="regular-text" />
		<?php
	}

	/**
	 * admin_enqueue_scripts()
	 * 
	 * This method is called in the admin_enqueue_scripts action. It is called once per field type.
	 * Use this method to enqueue CSS + JavaScript files.
	 * 
	 */
	function admin_enqueue_scripts() {
		$dir = plugin_dir_url( __FILE__ );

		# Enqueue JS
		wp_enqueue_script( 'carbon-field-number', $dir . 'js/field.js', array( 'carbon-fields' ) );
		
		# Enqueue CSS
		wp_enqueue_style( 'carbon-field-number', $dir . 'css/field.css' );
	}

	function save() {
		$name = $this->get_name();
		$value = $this->get_value();

		// Set the value for the field
		$this->set_name($name);

		$field_value = '';
		if ( isset($value) && $value !== '' && is_numeric($value) ) {
			$value = floatval($value);
			$value = $this->truncate($value);

			$is_valid_min = $this->min <= $value;
			$is_valid_max = $value <= $this->max;

			if ( $value !== '' && $is_valid_min && $is_valid_max ) {
				$field_value = $value;
			}
		}

		$this->set_value($field_value);

		parent::save();
	}

	function set_max($max) {
		$this->max = $max;
		return $this;
	}

	function set_min($min) {
		$this->min = $min;
		return $this;
	}

	function set_truncate($truncate) {
		$this->truncate = $truncate;
		return $this;
	}

	function set_step($step) {
		$this->step = $step;
		return $this;
	}

	// Helper Function, save on php 5.2
	function truncate($number) {
		$decimals = $this->truncate;

		$power = pow(10, $decimals); 
		if($number > 0){
			return floor($number * $power) / $power; 
		} else {
			return ceil($number * $power) / $power; 
		}
	}
}
