<?php
namespace PodloveSubscribeButton\Model;

abstract class Base {
	/**
	 * Property dictionary for all tables
	 */
	private static $properties = array();
	
	private $is_new = true;
	
	/**
	 * Contains property values
	 */
	private $data = array();
	
	public function __set( $name, $value ) {
		if ( static::has_property( $name ) ) {
			$this->set_property( $name, $value );
		} else {
			$this->$name = $value;
		}
	}
	
	private function set_property( $name, $value ) {
		$this->data[ $name ] = $value;
	}
	
	public function __get( $name ) {
		if ( static::has_property( $name ) ) {
			return $this->get_property( $name );
		} elseif ( property_exists( $this, $name ) ) {
			return $this->$name;
		} else {
			return null;
		}
	}
	
	private function get_property( $name ) {
		if ( isset( $this->data[ $name ] ) ) {
			return $this->data[ $name ];
		} else {
			return null;
		}
	}

	private static function unserialize_property($property) {
		if ( ! isset($property) )
			return;

		if ( $unserialized_string = is_serialized($property) )
			return unserialize($property);

		return $property;
	}

	/**
	 * Retrieves the database table name.
	 * 
	 * The name is derived from the namespace an class name. Additionally, it
	 * is prefixed with the global WordPress database table prefix.
	 * @todo cache
	 * 
	 * @return string database table name
	 */
	public static function table_name() {
		global $wpdb;
		
		// prefix with $wpdb prefix
		return $wpdb->prefix . static::name();
	}
	
	/**
	 * Define a property with name and type.
	 * 
	 * Currently only supports basics.
	 * @todo enable additional options like NOT NULL, DEFAULT etc.
	 * 
	 * @param string $name Name of the property / column
	 * @param string $type mySQL column type 
	 */
	public static function property( $name, $type, $args = array() ) {
		$class = get_called_class();
		
		if ( ! isset( static::$properties[ $class ] ) ) {
			static::$properties[ $class ] = array();
		}

		// "id" columns and those ending on "_id" get an index by default
		$index = $name == 'id' || stripos( $name, '_id' );
		// but if the argument is set, it overrides the default
		if (isset($args['index'])) {
			$index = $args['index'];
		}
		
		static::$properties[ $class ][] = array(
			'name'  => $name,
			'type'  => $type,
			'index' => $index,
			'index_length' => isset($args['index_length']) ? $args['index_length'] : null,
			'unique' => isset($args['unique']) ? $args['unique'] : null
		);
	}
	
	/**
	 * Return a list of property dictionaries.
	 * 
	 * @return array property list
	 */
	private static function properties() {
		$class = get_called_class();
		
		if ( ! isset( static::$properties[ $class ] ) ) {
			static::$properties[ $class ] = array();
		}
		
		return static::$properties[ $class ];
	}
	
	/**
	 * Does the given property exist?
	 * 
	 * @param string $name name of the property to test
	 * @return bool True if the property exists, else false.
	 */
	public static function has_property( $name ) {
		return in_array( $name, static::property_names() );
	}
	
	/**
	 * Return a list of property names.
	 * 
	 * @return array property names
	 */
	public static function property_names() {
		return array_map( function ( $p ) { return $p['name']; } , static::properties() );
	}
	
	/**
	 * Does the table have any entries?
	 * 
	 * @return bool True if there is at least one entry, else false.
	 */
	public static function has_entries() {
		return static::count() > 0;
	}
	
	/**
	 * Return number of rows in the table.
	 * 
	 * @return int number of rows
	 */
	public static function count() {
		global $wpdb;
		
		$sql = 'SELECT COUNT(*) FROM ' . static::table_name();
		return (int) $wpdb->get_var( $sql );
	}

	public static function find_by_id( $id ) {
		global $wpdb;
		
		$class = get_called_class();
		$model = new $class();
		$model->flag_as_not_new();
		
		$row = $wpdb->get_row( 'SELECT * FROM ' . static::table_name() . ' WHERE id = ' . (int) $id );
		
		if ( ! $row ) {
			return null;
		}
		
		foreach ( $row as $property => $value ) {
			$model->$property = static::unserialize_property($value);
		}
		
		return $model;
	}

	public static function find_one_by_property( $property, $value ) {
		global $wpdb;
		
		$class = get_called_class();
		$model = new $class();
		$model->flag_as_not_new();
		
		$query = $wpdb->prepare('SELECT * FROM ' . static::table_name() . ' WHERE ' . $property .  ' = \'%s\' LIMIT 0,1', $value);
		$row = $wpdb->get_row($query);
		
		if ( ! $row ) {
			return null;
		}
		
		foreach ( $row as $property => $value ) {
			$model->$property = static::unserialize_property($value);
		}
		
		return $model;
	}
	
	/**
	 * Retrieve all entries from the table.
	 *
	 * @return array list of model objects
	 */
	public static function all() {
		global $wpdb;
		
		$class = get_called_class();
		$models = array();
		
		$rows = $wpdb->get_results( 'SELECT * FROM ' . static::table_name() );

		foreach ( $rows as $row ) {
			$model = new $class();
			$model->flag_as_not_new();
			foreach ( $row as $property => $value ) {
				$model->$property = static::unserialize_property($value);
			}
			$models[] = $model;
		}
		
		return $models;
	}
	
	/**
	 * True if not yet saved to database. Else false.
	 */
	public function is_new() {
		return $this->is_new;
	}
	
	public function flag_as_not_new() {
		$this->is_new = false;
	}

	/**
	 * Rails-ish update_attributes for easy form handling.
	 *
	 * Takes an array of form values and takes care of serializing it.
	 * 
	 * @param  array $attributes
	 * @return bool
	 */
	public function update_attributes( $attributes ) {

		if ( ! is_array( $attributes ) )
			return false;

		$request = filter_input_array(INPUT_POST); // Do this for security reasons
			
		foreach ( $attributes as $key => $value ) {
			if ( is_array($value) ) {
				$this->{$key} = serialize($value);
			} else {
				$this->{$key} = esc_sql($value);
			}
		}
		
		if ( isset( $request['checkboxes'] ) && is_array( $request['checkboxes'] ) ) {
			foreach ( $request['checkboxes'] as $checkbox ) {
				if ( isset( $attributes[ $checkbox ] ) && $attributes[ $checkbox ] === 'on' ) {
					$this->$checkbox = 1;
				} else {
					$this->$checkbox = 0;
				}
			}
		}

		// @todo this is the wrong place to do this!
		// The feed password is the only "passphrase" which is saved. It is not encrypted!
		// However, we keep this function for later use
		if ( isset( $request['passwords'] ) && is_array( $request['passwords'] ) ) {
			foreach ( $request['passwords'] as $password ) {
				$this->$password = $attributes[ $password ];
			}
		}
		return $this->save();
	}

	/**
	 * Update and save a single attribute.
	 * 	
	 * @param  string $attribute attribute name
	 * @param  mixed  $value
	 * @return (bool) query success
	 */
	public function update_attribute($attribute, $value) {
		global $wpdb;

		$this->$attribute = $value;

		$sql = sprintf(
			"UPDATE %s SET %s = '%s' WHERE id = %s",
			static::table_name(),
			$attribute,
			mysqli_real_escape_string($value),
			$this->id
		);

		return $wpdb->query( $sql );
	}
	
	/**
	 * Saves changes to database.
	 * 
	 * @todo use wpdb::insert()
	 */
	public function save() {
		global $wpdb;

		if ( $this->is_new() ) {

			$this->set_defaults();

			$sql = 'INSERT INTO '
			     . static::table_name()
			     . ' ( '
			     . implode( ',', static::property_names() )
			     . ' ) '
			     . 'VALUES'
			     . ' ( '
			     . implode( ',', array_map( array( $this, 'property_name_to_sql_value' ), static::property_names() ) )
			     . ' );'
			;
			$success = $wpdb->query( $sql );
			if ( $success ) {
				$this->id = $wpdb->insert_id;
			}
		} else {
			$sql = 'UPDATE ' . static::table_name()
			     . ' SET '
			     . implode( ',', array_map( array( $this, 'property_name_to_sql_update_statement' ), static::property_names() ) )
			     . ' WHERE id = ' . $this->id
			;

			$success = $wpdb->query( $sql );
		}

		$this->is_new = false;

		do_action('podlove_model_save', $this);
		do_action('podlove_model_change', $this);

		return $success;
	}

	/**
	 * Sets default values.
	 * 
	 * @return array
	 */
	private function set_defaults() {
		
		$defaults = $this->default_values();

		if ( ! is_array( $defaults ) || empty( $defaults ) )
			return;

		foreach ( $defaults as $property => $value ) {
			if ( $this->$property === null )
				$this->$property = $value;
		}

	}

	/**
	 * Return default values for properties.
	 * 
	 * Can be overridden by inheriting model classes.
	 * 
	 * @return array
	 */
	public function default_values() {
		return array();
	}
	
	public function delete() {
		global $wpdb;
		
		$sql = 'DELETE FROM '
		     . static::table_name()
		     . ' WHERE id = ' . $this->id;

		$rows_affected = $wpdb->query( $sql );

	    do_action('podlove_model_delete', $this);
	    do_action('podlove_model_change', $this);

		return $rows_affected !== false;
	}

	private function property_name_to_sql_update_statement( $p ) {
		global $wpdb;

		if ( $this->$p !== null && $this->$p !== '' ) {
			return sprintf( "%s = '%s'", $p, ( is_array($this->$p) ? serialize($this->$p) : $this->$p ) );
		} else {
			return "$p = NULL";
		}
	}
	
	private function property_name_to_sql_value( $p ) {
		global $wpdb;

		if ( $this->$p !== null && $this->$p !== '' ) {
			return sprintf( "'%s'", $this->$p );
		} else {
			return 'NULL';
		}
	}
	
	/**
	 * Create database table based on defined properties.
	 * 
	 * Automatically includes an id column as auto incrementing primary key.
	 * @todo allow model changes
	 */
	public static function build() {
		global $wpdb;
		
		$property_sql = array();
		foreach ( static::properties() as $property )
			$property_sql[] = "`{$property['name']}` {$property['type']}";
		
		$sql = 'CREATE TABLE IF NOT EXISTS '
		     . static::table_name()
		     . ' ('
		     . implode( ',', $property_sql )
		     . ' ) CHARACTER SET utf8;'
		;
		
		$wpdb->query( $sql );

		static::build_indices();
	}
	
	/**
	 * Convention based index generation.
	 *
	 * Creates default indices for all columns matching both:
	 * - equals "id" or contains "_id"
	 * - doesn't have an index yet
	 */
	public static function build_indices() {
		global $wpdb;

		$indices_sql = 'SHOW INDEX FROM `' . static::table_name() . '`';
		$indices = $wpdb->get_results( $indices_sql );
		$index_columns = array_map( function($index){ return $index->Column_name; }, $indices );

		foreach ( static::properties() as $property ) {

			if ( $property['index'] && ! in_array( $property['name'], $index_columns ) ) {
				$length = isset($property['index_length']) ? '(' . (int) $property['index_length'] . ')' : '';
				$unique = isset($property['unique']) && $property['unique'] ? 'UNIQUE' : '';
				$sql = 'ALTER TABLE `' . static::table_name() . '` ADD ' . $unique . ' INDEX `' . $property['name'] . '` (' . $property['name'] . $length . ')';
				$wpdb->query( $sql );
			}
		}
	}

	/**
	 * Model identifier.
	 */
	public static function name() {
		// get name of implementing class
		$table_name = get_called_class();
		// replace backslashes from namespace by underscores
		$table_name = str_replace( '\\', '_', $table_name );
		// remove Models subnamespace from name
		$table_name = str_replace( 'Model_', '', $table_name );
		// all lowercase
		$table_name = strtolower( $table_name );

		return $table_name;
	}
}