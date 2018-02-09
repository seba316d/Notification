<?php
/**
 * Webhook notification
 */

namespace underDEV\Notification\Defaults\Notification;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Webhook extends Abstracts\Notification {

	public function __construct() {
		parent::__construct( 'webhook', __( 'Webhook' ) );
	}

	public function form_fields() {

		$this->add_form_field( new Field\RecipientsField( array(
			'notification'     => $this->get_slug(),
			'label'            => __( 'URLs' ),
			'name'             => 'urls',
			'add_button_label' => __( 'Add URL', 'notification' ),
		) ) );

		$this->add_form_field( new Field\RepeaterField( array(
			'label'            => __( 'Arguments' ),
			'name'             => 'args',
			'add_button_label' => __( 'Add argument', 'notification' ),
			'fields'           => array(
				new Field\InputField( array(
					'label'      => __( 'Key' ),
					'name'       => 'key',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
				new Field\InputField( array(
					'label'      => __( 'Value' ),
					'name'       => 'value',
					'resolvable' => true,
					'description' => __( 'You can use merge tags' ),
				) ),
			),
		) ) );

	}

	public function send( \underDEV\Notification\Abstracts\Trigger $trigger ) {

		$data = $this->data;

		$args = $this->parse_args( $data['args'] );
		$args = apply_filters( 'notification/webhook/args', $args, $this, $trigger );

		// Call each URL separately.
		foreach ( $data['urls'] as $url ) {

			$filtered_args = apply_filters( 'notification/webhook/args/' . $url['type'] , $args, $this, $trigger );

			if ( $url['type'] === 'get' ) {
				$this->send_get( $url['recipient'], $filtered_args );
			} elseif ( $url['type'] === 'post' ) {
				$this->send_post( $url['recipient'], $filtered_args );
			}

		}

    }

    /**
     * Sends GET request
     *
     * @since  [Unreleased]
     * @param  string $url  URL to call
     * @param  array  $args arguments
     * @return void
     */
    public function send_get( $url, $args ) {

    	$remote_url  = add_query_arg( $args, $url );
    	$remote_args = apply_filters( 'notification/webhook/remote_args/get', array(), $url, $args, $this );

    	$response = wp_remote_get( $remote_url, $remote_args );

    	do_action( 'notification/webhook/called/get', $response, $url, $args, $remote_args, $this );

    }

    /**
     * Sends POST request
     *
     * @since  [Unreleased]
     * @param  string $url  URL to call
     * @param  array  $args arguments
     * @return void
     */
    public function send_post( $url, $args ) {

    	$remote_args = apply_filters( 'notification/webhook/remote_args/get', array(
    		'body' => $args,
    	), $url, $args, $this );

    	$response = wp_remote_post( $url, $remote_args );

    	do_action( 'notification/webhook/called/post', $response, $url, $args, $remote_args, $this );

    }

    /**
     * Parses args to be understand by the wp_remote_* functions
     *
     * @since  [Unreleased]
     * @param  array $args args from saved fields
     * @return array       parsed args as key => value array
     */
    private function parse_args( $args ) {

    	$parsed_args = array();

    	foreach ( $args as $arg ) {
    		$parsed_args[ $arg['key'] ] = $arg['value'];
    	}

    	return $parsed_args;

    }

}