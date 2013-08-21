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

class phpbb_ext_phpbb_karma_ucp_received_karma
{
	public function __construct()
	{
		global $phpbb_container, $user, $template;

		$this->container = $phpbb_container;
		$this->user = $user;
		$this->template = $template;

		$user->add_lang_ext('phpbb/karma', 'karma');
	}

	public function main($id, $mode)
	{
		$this->tpl_name = 'ucp_karma_received_karma';
		$this->page_title = $this->user->lang['UCP_RECEIVED_KARMA'];

		// Get the received karma
		$karma_manager = $this->container->get('karma.includes.manager');
		$received_karma = $karma_manager->get_karma_received_by_user($this->user->data['user_id']);
		foreach ($received_karma as $row)
		{
			$block_row = array();
			foreach ($row as $key => $value)
			{
				$block_row[strtoupper($key)] = $value;
			}
			$this->template->assign_block_vars('received_karma', $block_row);
		}

		$this->template->assign_vars(array(
			'L_TITLE'			=> $this->user->lang['UCP_RECEIVED_KARMA'],
			'S_REPORT_KARMA'	=> true, // TODO make this depend on permissions and perhaps a setting
		));
	}
}
