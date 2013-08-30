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

class phpbb_ext_phpbb_karma_mcp_reported_karma
{
	public function __construct()
	{
		global $phpbb_container, $request, $user, $template;

		$this->container = $phpbb_container;
		$this->request = $request;
		$this->user = $user;
		$this->template = $template;

		$user->add_lang_ext('phpbb/karma', 'karma');
	}

	public function main($id, $mode)
	{
		$this->page_title = 'MCP_REPORTED_KARMA';

		$report_model = $this->container->get('karma.includes.report_model');
		switch ($mode)
		{
			case 'report_details':
				// Get the report and the karma
				try
				{
					$karma_report_id = $this->request->variable('r', 0);
					$karma_report = $report_model->get_karma_report($karma_report_id);
				}
				catch (OutOfBoundsException $e)
				{
					trigger_error('NO_KARMA_REPORT');
				}

				// TODO What happens if the karma the report refers to doesn't exist anymore?
				$karma_manager = $this->container->get('karma.includes.manager');
				$karma = $karma_manager->get_karma_data($karma_report['karma_id']);

				// Ready the reported karma for display
				$block_row = array();
				foreach ($karma as $key => $value)
				{
					switch ($key)
					{
						case 'score':
						case 'received_at':
						case 'comment':
							$value = ($key == 'received_at') ? $this->user->format_date($karma_report['reported_karma_time']) : $karma_report['reported_karma_' . $key];
							// TODO perhaps it wasn't the best idea to stop the karma_data keys from matching database field names? :)
					}
					$block_row[strtoupper($key)] = $value;
				}
				$this->template->assign_block_vars('received_karma', $block_row);

				// Determine if the karma was altered after being reported
				if ($karma['score'] != $karma_report['reported_karma_score'] || $karma['comment'] != $karma_report['reported_karma_comment'])
				{
					$this->template->assign_var('S_KARMA_REPORT_KARMA_EDITED', true);

					// Add the karma as it is now to the template
					$block_row = array();
					foreach ($karma as $key => $value)
					{
						$block_row[strtoupper($key)] = $value;
					}
					$this->template->assign_block_vars('received_karma', $block_row);
				}

				$this->template->assign_vars(array(
					'KARMA_REPORTER'				=> get_username_string('full', $karma_report['user_id'], $karma_report['username'], $karma_report['user_colour']),
					'KARMA_REPORT_DATE'				=> $this->user->format_date($karma_report['karma_report_time']),
					'KARMA_REPORT_TEXT'				=> $karma_report['karma_report_text'],
					'S_KARMA_REPORT_ITEM_EDITED'	=> $karma['item_last_edit'] > $karma_report['karma_report_time'],
				));

				$this->tpl_name = 'mcp_karma_report_details';
				break;
			case 'reports':
			case 'reports_closed':
				// Get the reports TODO
				// TODO only show reports for forums that this moderator may moderate
		}

		$this->template->assign_vars(array(
			'L_TITLE'			=> $this->user->lang['MCP_REPORTED_KARMA'],
		));
	}
}
