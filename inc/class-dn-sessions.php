<?php
session_start();

class DN_Sessions
{

	// instance
	static $_instance = null;

	// $session
	public $session = null;

	// live time of cookie
	private $live_item = null;

	// remember
	private $remember = false;

	/**
	 * prefix
	 * @var null
	 */
	public $prefix = null;

	function __construct( $prefix = '', $remember = true )
	{
		if( ! $prefix )
			return;

		$this->prefix = $prefix;
		$this->remember = $remember;

		$this->live_item = 12 * HOUR_IN_SECONDS;

		// get all
		$this->session = $this->load();

	}

	/**
	 * load all with prefix
	 * @return
	 */
	function load()
	{
		if( isset( $_SESSION[ $this->prefix ] ) )
		{
			return $_SESSION[ $this->prefix ];
		}
		else if( $this->remember && isset( $_COOKIE[ $this->prefix ] ) )
		{
			return maybe_unserialize( $_COOKIE[ $this->prefix ] );
		}

		return array();
	}

	/**
	 * set key
	 * @param $key
	 * @param $value
	 */
	function set( $name = '', $value = null )
	{
		if( ! $name )
			return;

		$time = time();
		if( ! $value )
		{
			unset( $this->session[ $name ] );
			$time = time() - $this->live_item;
		}
		else
		{
			$this->session[ $name ] = $value;
			$time = time() + $this->live_item;
		}

		// save session
		$_SESSION[ $this->prefix ] = $this->session;

		// save cookie
		donate_setcookie( $this->prefix, maybe_serialize( $this->session ), $time );
		return $this->session[ $name ];

	}

	/**
	 * get value
	 * @param  $key
	 * @return anythings
	 */
	function get( $key = null, $default = null )
	{
		if( ! $key )
			return $default;

		if( isset( $this->session[ $name ] ) )
			return $this->session[ $name ];
	}

	static function instance( $prefix = '' )
	{
		if( ! empty( self::$_instance[ $prefix ] ) )
			return self::$_instance[ $prefix ];

		return self::$_instance[ $prefix ] = new self( $prefix );
	}

}
