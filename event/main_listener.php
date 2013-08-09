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
			'core.viewtopic_cache_user_data'	=> 'viewtopic_body_add_karma_score_to_user_cache_data',
			'core.viewtopic_modify_post_row'	=> 'viewtopic_body_add_postrow_user_karma_score',
			'core.ucp_pm_view_messsage'			=> 'ucp_pm_viewmessage_add_pm_author_karma_score',
		);
	}

	public function viewtopic_body_add_karma_score_to_user_cache_data($event)
	{
		$user_cache_data = $event['user_cache_data'];
		$user_cache_data['karma_score'] = $event['row']['user_karma_score'];
		$event['user_cache_data'] = $user_cache_data;
	}

	public function viewtopic_body_add_postrow_user_karma_score($event)
	{
		global $user;

		if ($event['row']['user_id'] != ANONYMOUS)
		{
			// Load the karma language file
			$user->add_lang_ext('phpbb/karma', 'karma');

			$post_row = $event['post_row'];
			$post_row['POSTER_KARMA_SCORE'] = $event['user_poster_data']['karma_score'];
			$event['post_row'] = $post_row;
		}
	}

	public function ucp_pm_viewmessage_add_pm_author_karma_score($event)
	{
		global $user;

		// Load the karma language file
		$user->add_lang_ext('phpbb/karma', 'karma');

		$msg_data = $event['msg_data'];
		$msg_data['AUTHOR_KARMA_SCORE'] = $event['message_row']['user_karma_score'];
		$event['msg_data'] = $msg_data;
	}
}
