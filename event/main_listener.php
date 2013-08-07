<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class phpbb_ext_phpbb_karma_event_main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_modify_post_row' => 'add_postrow_user_karma_score',
		);
	}

	public function add_postrow_user_karma_score($event)
	{
		global $user, $phpbb_container;

		// Load the karma language file
		$user->add_lang_ext('phpbb/karma', 'karma');

		$karma_manager = $phpbb_container->get('karma.includes.karma_manager');

		$post_row = $event['post_row'];
		$post_row['POSTER_KARMA'] = $karma_manager->get_user_karma_score($event['row']['user_id']);
		$event['post_row'] = $post_row;
	}
}
