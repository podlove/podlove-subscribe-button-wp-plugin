<?php

namespace PodloveSubscribeButton;

define( __NAMESPACE__ . '\PLUGIN_FILE_NAME', strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', __NAMESPACE__ ) ) . '.php' );
define( __NAMESPACE__ . '\PLUGIN_DIR' , plugin_dir_path( dirname(__FILE__) ) );
define( __NAMESPACE__ . '\PLUGIN_FILE', PLUGIN_DIR . PLUGIN_FILE_NAME );
define( __NAMESPACE__ . '\PLUGIN_URL' , plugins_url( '', PLUGIN_FILE ) );