<?php
/**
 * Onyx Custom Post Types
 *
 * This class was inspired by https://github.com/jjgrainger/PostTypes/
 * Thanks to Joe Grainger https://github.com/jjgrainger
 *
 * @package Onyx Theme
 */

namespace Onyx;

class Cpt {

	/**
	 * The key for post type
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The names params to create the keys passed to the post type
	 * [name, singular, plural, slug]
	 *
	 * @var array
	 */
	public $names;

	/**
	 * The name (singular) for post type
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The plural name for post type
	 *
	 * @var string
	 */
	public $plural;

	/**
	 * The slug for post type
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Taxonomies for post type
	 *
	 * @var string|array
	 */
	public $taxonomies;

	/**
	 * Arguments for post type
	 *
	 * @var array
	 */
	public $arguments;

	/**
	 * Labels for post type
	 *
	 * @var array
	 */
	public $labels;

	/**
	 * Custom capability for post type
	 *
	 * @var string
	 */
	public $capability;

	/**
	 * Custom capabilities for post type
	 *
	 * @var array
	 */
	public $capabilities;

	/**
	 * Icon for post type
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * The column manager for the post type
	 *
	 * @var mixed
	 */
	public $columns;

	/**
	 * Constructor
	 *
	 * @param string|array $names A string for the name, or an array of names [required]
	 * @param array        $arguments Arguments to register a post type with register_post_type [optional]
	 * @param array        $labels Custom labels to create the post type [optional]
	 * @return void
	 */
	public function __construct( $names, $arguments = [], $labels = [] ) {
		$this->names( $names );
		$this->options( $arguments );
		$this->labels( $labels );
	}

	/**
	 * Set the names and key for the post type
	 *
	 * @param string|array $names A string for the name, or an array of names [required]
	 * @return void
	 */
	public function names( $names ) {
		// if the name is a string, then assign the name in array.
		$this->validate_name_params( $names );

		// set name (singular).
		$name       = $this->get_name();
		$this->name = ( substr( $name, -1 ) === 's' ) ? substr( $name, 0, -1 ) : $name;

		// set plural name.
		$plural       = ( isset( $this->names['plural'] ) ) ? $this->names['plural'] : $name;
		$this->plural = ( substr( $plural, -1 ) !== 's' ) ? "{$plural}s" : $plural;

		// set slug.
		$slug       = ( isset( $this->names['slug'] ) ) ? $this->names['slug'] : $this->plural;
		$this->slug = sanitize_title( $slug );

		// set key.
		$this->key = $this->slug;
	}

	/**
	 * Set the options for post type
	 *
	 * @param array $arguments An array of arguments for post type
	 * @return void
	 */
	public function options( $arguments ) {
		$this->arguments = $arguments;
	}

	/**
	 * Set custom capability for post type
	 *
	 * @param string $capability
	 * @return void
	 */
	public function capability( $capability ) {
		$this->capability = $capability;
	}

	/**
	 * Set custom capabilities for post type
	 *
	 * @param array $capabilities
	 * @return void
	 */
	public function capabilities( $capabilities = [] ) {
		$this->capabilities = $capabilities;
	}

	/**
	 * Set the labels for post type
	 *
	 * @param array $labels An array of labels for post type
	 * @return void
	 */
	public function labels( $labels ) {
		$this->labels = $labels;
	}

	/**
	 * Set the icon for the post type
	 *
	 * @link https://developer.wordpress.org/resource/dashicons/
	 * @param string $icon A dashicon class for the menu icon
	 * @return void
	 */
	public function icon( $icon ) {
		$this->icon = $icon;
	}

	/**
	 * Register a taxonomy to the post type
	 *
	 * @param mixed $taxonomies The Taxonomy name(s) to add
	 * @return void
	 */
	public function taxonomies( $taxonomies ) {
		$taxonomies = is_string( $taxonomies ) ? [ $taxonomies ] : $taxonomies;

		foreach ( $taxonomies as $taxonomy ) {
			$this->taxonomies[] = $taxonomy;
		}
	}

	/**
	 * Get the formated post type name
	 *
	 * @return string
	 */
	private function get_name() {
		$value = str_replace( [ '-', '_' ], ' ', $this->names['name'] );
		$value = strtolower( $value );
		return ucwords( $value );
	}

	/**
	 * Validate $names params passed from __construct
	 *
	 * @param string|array $names A string for the name, or an array of names [required]
	 * @throws \Exception The name(s) param is required.
	 * @return void
	 */
	private function validate_name_params( $names ) {
		if ( is_string( $names ) ) {
			$this->names['name'] = $names;
		} elseif ( is_array( $names ) ) {
			$this->names = $names;
		} else {
			throw new \Exception( 'Please, provide a string or array as param on constructor', 1 );
		}
	}

	/**
	 * Create the labels for post type
	 *
	 * @return array
	 */
	private function create_labels() {
		// default labels.
		$plural_lower = strtolower( $this->plural );
		$labels       = [
			'name'               => __( $this->plural ),
			'singular_name'      => __( $this->name ),
			'menu_name'          => __( $this->plural ),
			'all_items'          => __( $this->plural ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( "Add New {$this->name}" ),
			'edit_item'          => __( "Edit {$this->name}" ),
			'new_item'           => __( "New {$this->name}" ),
			'view_item'          => __( "View {$this->name}" ),
			'search_items'       => __( "Search {$this->plural}" ),
			'not_found'          => __( "No $plural_lower found" ),
			'not_found_in_trash' => __( "No $plural_lower found in Trash'" ),
			'parent_item_colon'  => __( "Parent {$this->name}:" ),
		];

		// replace defaults with custom labels passed and return
		return array_replace_recursive( $labels, $this->labels );
	}

	/**
	 * Create arguments to register post type
	 *
	 * @return array Arguments to pass to register_cpt
	 */
	private function create_cpt_arguments() {
		// default arguments.
		$arguments = [
			'public'  => true,
			'rewrite' => [
				'slug' => $this->slug,
			],
		];

		// replace defaults with the options passed.
		$arguments = array_replace_recursive( $arguments, $this->arguments );

		// set capability.
		if ( ! isset( $arguments['capability_type'] ) && ! empty( $this->capability ) ) {
			$arguments['capability_type'] = $this->capability;
		}

		// set capability.
		if ( ! isset( $arguments['capabilities'] ) && ! empty( $this->capabilities ) ) {
			$arguments['capabilities'] = $this->capabilities;
			$arguments['map_meta_cap'] = ( ! empty( $this->arguments['map_meta_cap'] )) ? $this->arguments['map_meta_cap'] : true;
		}

		// register taxonomies.
		if ( ! isset( $arguments['taxonomies'] ) && ! empty( $this->taxonomies ) ) {
			$arguments['taxonomies'] = $this->taxonomies;
		}

		// create and set labels.
		if ( ! isset( $arguments['labels'] ) ) {
			$arguments['labels'] = $this->create_labels();
		}

		// set the menu icon.
		if ( ! isset( $arguments['menu_icon'] ) && isset( $this->icon ) ) {
			$arguments['menu_icon'] = $this->icon;
		}

		return $arguments;
	}

	/**
	 * Register the custom post type
	 *
	 * @return WP_Post_Type|WP_Error
	 */
	public function register_cpt() {
		$arguments = $this->create_cpt_arguments();

		if ( ! post_type_exists( $this->key ) ) {
			return register_post_type( $this->key, $arguments );
		}
	}

	/**
	 * Hook post type to WordPress
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_cpt' ] );

		if ( isset( $this->columns ) ) {
			// manage/motify the admin edit columns.
			add_filter( "manage_{$this->key}_posts_columns", [ $this, 'manage_columns' ], 10, 1 );

			// add custom columns
			add_filter( "manage_{$this->key}_posts_custom_column", [ $this, 'custom_columns' ], 10, 2 );

			// run filter to make columns sortable.
			add_filter( "manage_edit-{$this->key}_sortable_columns", [ $this, 'sortable_columns' ] );

			// run action that sorts columns on request.
			add_action( 'pre_get_posts', [ $this, 'orderby_columns' ] );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| MANAGE ADMIN COLUMNS
	|--------------------------------------------------------------------------
	*/

	/**
	 * Manage/Modify the columns for post type
	 *
	 * @param array $columns Default WordPress columns
	 * @return array The modified columns
	 */
	public function manage_columns( $columns ) {
		$columns = $this->columns->manage_columns( $columns );
		return $columns;
	}

	/**
	 * Populate custom columns for post type
	 *
	 * @param string $column   The column slug
	 * @param int    $post_id  The post ID
	 */
	public function custom_columns( $column, $post_id ) {
		if ( isset( $this->columns->populate[$column] ) ) {
			// $this->columns()->populate[$column] = [ $column, $post_id ];
			call_user_func_array( $this->columns()->populate[$column], [ $column, $post_id ] );
		}
	}

	/**
	 * Make custom columns sortable
	 *
	 * @param array $columns  Default WordPress sortable columns
	 */
	public function sortable_columns( $columns ) {
		if ( ! empty( $this->columns()->sortable ) ) {
			$columns = array_merge( $columns, $this->columns()->sortable );
		}

		return $columns;
	}

	/**
	 * Set query to sort custom columns
	 *
	 * @param object $query WP_Query
	 */
	public function orderby_columns( $query ) {
		// don't modify the query if we're not in the post type admin
		if ( ! is_admin() || $query->get( 'post_type' ) !== $this->key ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		// if the sorting a custom column
		if ( $this->columns()->is_sortable( $orderby ) ) {
			// get the custom column options
			$meta = $this->columns()->sortable_meta( $orderby );

			// determine type of ordering
			if ( is_string( $meta ) ) {
				$meta_key   = $meta;
				$meta_value = 'meta_value';
			} else {
				$meta_key   = $meta[0];
				$meta_value = 'meta_value_num';
			}

			// set the custom order
			$query->set( 'meta_key', $meta_key );
			$query->set( 'orderby', $meta_value );
		}
	}

	/**
	 * Get the Column Manager for the PostType
	 *
	 * @return \Onyx\Columns
	 */
	public function columns() {
		if ( ! isset( $this->columns ) ) {
			$this->columns = new \Onyx\Columns();
		}

		return $this->columns;
	}

	/**
	 * Register custom admin post types columns
	 *
	 * @param array $columns Custom columns [required]
	 * @return void
	 */
	public function register_columns( $columns ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! empty( $columns['add'] ) ) {
			$this->columns()->register_columns( $columns['add'] );
		}

		if ( ! empty( $columns['hide'] ) ) {
			$this->columns()->hide( $columns['hide'] );
		}

		if ( ! empty( $columns['order'] ) ) {
			$this->columns()->order( array_flip( $columns['order'] ) );
		}
	}

}
