<?php
/**
 * User nicename merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\User;

use underDEV\Notification\Defaults\MergeTag\StringTag;

/**
 * User nicename merge tag class
 */
class UserNicename extends StringTag {

	/**
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
    private $trigger;

    /**
     * Constructor
     *
     * @param object $trigger Trigger object to access data from.
     */
    public function __construct( $trigger ) {

        $this->trigger = $trigger;

    	parent::__construct( array(
			'slug'        => 'user_nicename',
			'name'        => __( 'User nicename' ),
			'description' => __( 'Will be resolved to a user nicename' ),
			'resolver'    => function() {
				return $this->trigger->user_object->user_nicename;
			}
        ) );

    }

    /**
     * Function for checking requirements
     *
     * @return boolean
     */
    public function check_requirements( ) {

        return isset( $this->trigger->user_object->user_nicename );

    }

}