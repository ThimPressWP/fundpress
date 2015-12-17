<?php

abstract class DN_Setting_Page extends DN_Setting
{

	/**
	 * $_id tab id
	 * @var null
	 */
	protected $_id = null;

	/**
	 * $_title tab display
	 * @var null
	 */
	protected $_title = null;

	/**
	 * $_fields
	 * @var array
	 */
	protected $_fields = array();

	public $_options = null;

	/**
	 * $_position
	 * @var integer
	 */
	protected $_position = 1;

	public function __construct()
	{
		if( is_admin() )
		{
			add_filter( 'donate_admin_settings', array( $this, 'add_tab' ), $this->_position, 1 );
			add_action( 'donate_admin_setting_' . $this->_id . '_content', array( $this, 'layout' ), $this->_position, 1 );
		}

		$this->_options = $this->options();
	}

	/**
	 * add_tab setting
	 * @param array
	 */
	public function add_tab( $tabs )
	{
		if( $this->_id && $this->_title )
		{
			$tabs[ $this->_id ] = $this->_title;
			return $tabs;
		}
	}

	/**
	 * generate layout
	 * @return html layout
	 */
	public function layout()
	{
		// before tab content
		do_action( 'donate_admin_setting_before_' . $this->_id, $this->_id );

		// donate()->_include( 'inc/admin/views/tab_' . $this->_id . '.php' ); return;
		$this->_fields = apply_filters( 'donate_admin_' . $this->_id  . '_fields', $this->load_field(), $this->_id );

		if( $this->_fields )
		{
			$html = array();
			foreach( $this->_fields as $key => $group )
			{
				if( isset( $group[ 'title' ], $group[ 'desc' ] ) )
				{
					$html[] = '<h3>' . sprintf( '%s', $group[ 'title' ] ) . '</h3>';
					$html[] = '<p>' . sprintf( '%s', $group[ 'desc' ] ) . '</p>';
				}

				if( isset( $group[ 'fields' ] ) )
				{
					$html[] = '<table>';
					foreach( $group[ 'fields' ] as $type => $field )
					{

						if( isset( $field[ 'name' ], $field[ 'type' ] ) )
						{
							$html[] = '<tr>';

							// label
							$html[]	= '<th><label for="'.$this->get_field_id( $field[ 'name' ] ).'">' . sprintf( '%s', $field['label'] ) . '</label>' ;

							if( isset( $field[ 'desc' ] ) )
							{
								$html[] = '<p><small>' . sprintf( '%s', $field['desc'] ) . '</small></p>';
							}

							$html[]	= '</th>';
							// end label

							// field
							$html[] = '<td>';

							$default = array(
											'type'		=> '',
											'label'		=> '',
											'desc'		=> '',
											'atts'		=> array(
													'id'	=> '',
													'class'	=> ''
												),
											'name'		=> '',
											'group'		=> $this->_id ? $this->_id : null,
											'options'	=> array(

												)
										);

							$field = wp_parse_args( $field, $default );

							ob_start();
							include TP_DONATE_INC . '/admin/views/html/' . $field[ 'type' ] . '.php';
							$html[] = ob_get_clean();

							$html[] = '</td>';
							// end field

							$html[]	= '</tr>';
						}
					}
					$html[] = '</table>';
				}
			}
			echo implode( '' , $html );

		}
		// after tab content
		do_action( 'donate_admin_setting_after_' . $this->_id, $this->_id );
	}

	protected function load_field()
	{
		return array();
	}

	/**
	 * genarate input atts
	 * @param  $atts
	 * @return string
	 */
	public function render_atts( $atts = array() )
	{
		if( ! is_array( $atts ) )
			return;

		$html = array();
		foreach ( $atts as $key => $value ) {
			if( is_array( $value ) )
			{
				$value = implode( ' ', $value );
			}
			$html[] = $key . '="' . esc_attr( $value ) . '"';
		}
		return implode( ' ' , $html );
	}

	/**
	 * options load options
	 * @return array || null
	 */
	protected function options()
	{
		$options = get_option( $this->_prefix, null );
		if( isset( $options[ $this->_id ] ) )
			return $options[ $this->_id ];

		return null;
	}

	/**
	 * get option value
	 * @param  $name
	 * @return option value. array, string, boolean
	 */
	public function get( $name = null, $default = null )
	{
		if( ! $this->_options )
			$this->_options = $this->options();

		if( $name && isset( $this->_options[ $name ] ) )
			return $this->_options[ $name ];

		return $default;

	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_id( $name = null, $group = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		if( ! $group )
			$group = $this->_id;

		if( $group )
			return $this->_prefix . '_' . $group . '_' . $name;

		return $this->_prefix . '_' . $name;

	}

	/**
	 * get_name_field
	 * @param  $name of field option
	 * @return string name field
	 */
	public function get_field_name( $name = null, $group = null )
	{
		if( ! $this->_prefix || ! $name )
			return;

		if( ! $group )
			$group = $this->_id;

		if( $group )
			return $this->_prefix . '[' . $group . '][' . $name . ']' ;

		return $this->_prefix . '[' . $name . ']' ;

	}

}
