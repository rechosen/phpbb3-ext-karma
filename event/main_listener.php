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
			'core.user_setup'						=> 'load_global_translations',
			'core.viewtopic_cache_user_data'		=> 'viewtopic_body_add_karma_score_to_user_cache_data',
			'core.viewtopic_modify_post_row'		=> 'viewtopic_body_postrow_add_karma_score_and_controls',
			'core.ucp_pm_view_messsage'				=> 'ucp_pm_viewmessage_add_pm_author_karma_score',
			'core.memberlist_prepare_profile_data'	=> 'memberlist_view_add_karma_score_to_user_statistics',
		);
	}

	public function load_global_translations($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'phpbb/karma',
			'lang_set' => 'karma_global',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function viewtopic_body_add_karma_score_to_user_cache_data($event)
	{
		$user_cache_data = $event['user_cache_data'];
		$user_cache_data['karma_score'] = $event['row']['user_karma_score'];
		$event['user_cache_data'] = $user_cache_data;
	}

	public function viewtopic_body_postrow_add_karma_score_and_controls($event)
	{
		global $user, $phpbb_root_path, $phpEx;

		if ($event['row']['user_id'] != ANONYMOUS
			&& $event['row']['user_id'] != $user->data['user_id'])
		{
			// Load the karma language file
			$user->add_lang_ext('phpbb/karma', 'karma');

			$post_row = $event['post_row'];
			$post_row['POSTER_KARMA_SCORE'] = $event['user_poster_data']['karma_score'];
			// TODO Only show these if the user can give karma
			$post_row['U_GIVEKARMA_POSITIVE'] = append_sid(
				"{$phpbb_root_path}app.$phpEx",
				"controller=givekarma/post/{$event['row']['post_id']}&amp;score=positive"
			);
			$post_row['U_GIVEKARMA_NEGATIVE'] = append_sid(
				"{$phpbb_root_path}app.$phpEx",
				"controller=givekarma/post/{$event['row']['post_id']}&amp;score=negative"
			);
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

	public function memberlist_view_add_karma_score_to_user_statistics($event)
	{
		global $user;

		// Load the karma language file
		$user->add_lang_ext('phpbb/karma', 'karma');

		$template_data = $event['template_data'];
		$template_data['USER_KARMA_SCORE'] = $event['data']['user_karma_score'];
		$event['template_data'] = $template_data;
	}
}
