<?php
/*
Plugin Name: Slimstat Analytics - Extended Overview
Description: Add custom columns to the User Overview widget and export file.
Version: 1.0
Author: Slimstat Analytics
Author URI: http://www.wp-slimstat.com/
*/

class wp_slimstat_extended_overview {
	public static $columns = array( 
		'company' => array(
			'order' => '25', // Default columns are defined as Username=20, Full Name=40, Email=60, etc... By setting this value=25, will place this column in between Username and Full Name
			'label' => 'Company',
			'class' => 'manage-column',
			'type' => 'varchar',
			'in_csv' => true, // Switch to determine if this column should be added to the CSV file
			'in_widget' => true // Switch to determine if this column should be added to User Overview widget
		),
		'segment' => array(
			'order' => '45',
			'label' => 'Segment',
			'class' => 'manage-column',
			'type' => 'varchar',
			'in_csv' => true,
			'in_widget' => false
		)
	);

	public static function init(){
		if ( !class_exists( 'wp_slimstat' ) ) {
			return true;
		}

		// Define extra column names to the CSV export
		add_filter( 'slimstat_column_names', array( __CLASS__, 'add_column_names' ) );

		// Append columns to the table: this is separate from the filter here above because
		// you may want to show a different set of columns in the admin compared to the CSV export
		add_filter( 'slimstat_user_overview_table_columns', array( __CLASS__, 'add_table_columns' ), 20, 1 );

		// Append custom values to the dataset
		add_filter( 'slimstat_user_overview_get_data', array( __CLASS__, 'get_data' ) );
	}

	public static function add_column_names( $_columns = array() ) {
		foreach ( self::$columns as $a_id => $a_column ) {
			if ( !empty( $a_column[ 'in_csv' ] ) ) {
				$_columns[ $a_id ] = array( $a_column[ 'label' ], $a_column[ 'type' ] );
			}
		}

		return $_columns;
	}

	public static function add_table_columns( $_columns = array() ) {
		foreach ( self::$columns as $a_id => $a_column ) {
			if ( !empty( $a_column[ 'in_widget' ] ) ) {
				$_columns[ $a_column[ 'order' ] ] = array(
					'id' => $a_id,
					'label' => $a_column[ 'label' ],
					'class' => $a_column[ 'class' ]
				);
			}
		}

		return $_columns;
	}

	public static function get_data( $_rows = array() ) {
		foreach ( $_rows as $i => $a_row ) {
			// Retrieve the value to append to this user; you can use $_rows[ $i ][ 'username' ] or any other values already available in the data set
			$_rows[ $i ][ 'company' ] = $_rows[ $i ][ 'username' ] . "'s company";
			$_rows[ $i ][ 'segment' ] = $_rows[ $i ][ 'username' ] . "'s segment";
		}

		return $_rows;
	}
}
// end of class declaration

// Bootstrap
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', array( 'wp_slimstat_extended_overview', 'init' ), 10 );
}