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

class phpbb_ext_phpbb_karma_ucp_recent_karma
{
	public function __construct()
	{
		global $user, $template;

		$this->user = $user;
		$this->template = $template;

		$user->add_lang_ext('phpbb/karma', 'karma');
	}

	public function main($id, $mode)
	{
		$this->tpl_name = 'ucp_karma_recent_karma';
		$this->page_title = $this->user->lang['UCP_RECENT_KARMA'];

		$this->template->assign_vars(array(
			'L_TITLE'			=> $this->user->lang['UCP_RECENT_KARMA'],
		));
	}
}
