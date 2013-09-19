<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
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
	public function __construct($p_master)
	{
		global $auth, $config, $phpbb_container, $phpbb_log, $phpbb_root_path, $phpEx, $request, $user, $template;

		$this->auth = $auth;
		$this->config = $config;
		$this->container = $phpbb_container;
		$this->helper = $phpbb_container->get('controller.helper');
		$this->phpbb_log = $phpbb_log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;
		$this->request = $request;
		$this->user = $user;
		$this->template = $template;

		$this->p_master = $p_master;

		$user->add_lang_ext('phpbb/karma', 'karma');
	}

	public function main($id, $mode)
	{
		global $action; // TODO is using the global $action ok?

		$this->page_title = 'MCP_REPORTED_KARMA';

		// Hide the "Karma report details" module tab (this is undone if $mode == 'report_details')
		$this->p_master->set_display('phpbb_ext_phpbb_karma_mcp_reported_karma', 'report_details', false);

		// Determine if the moderator wants to close or delete reports
		switch ($action)
		{
			case 'close':
			case 'delete':
				$karma_report_id_list = $this->request->variable('karma_report_id_list', array(0));
				$this->close_karma_report($karma_report_id_list, $action);
			break;
		}

		$report_model = $this->container->get('karma.includes.report_model');
		$karma_manager = $this->container->get('karma.includes.manager');
		switch ($mode)
		{
			case 'report_details':
				// Display this module's tab (it is hidden by default)
				$this->p_master->set_display('phpbb_ext_phpbb_karma_mcp_reported_karma', 'report_details', true);

				// Get the report and the karma
				try
				{
					$karma_report_id = $this->request->variable('r', 0);
					$karma_report = $report_model->get_karma_report($karma_report_id);
				}
				catch (OutOfBoundsException $e)
				{
					trigger_error($e->getMessage());
				}

				$karma_row = $karma_manager->get_karma_row($karma_report['karma_id']);
				if ($karma_row === false)
				{
					trigger_error('NO_KARMA');
				}

				// Reconstruct the karma the way it was when it was reported
				$report_fields = array(
					'karma_score',
					'karma_time',
					'karma_comment',
				);
				$reported_karma_row = $karma_row;
				foreach ($report_fields as $report_field)
				{
					$reported_karma_row[$report_field] = $karma_report['reported_' . $report_field];
				}
				// Ready the reported karma for display
				$reported_karma_data = $karma_manager->format_karma_row($reported_karma_row);
				$block_row = array();
				foreach ($reported_karma_data as $key => $value)
				{
					$block_row[strtoupper($key)] = $value;
				}
				// Determine if the item was edited after the reported karma was given
				if ($reported_karma_data['item_last_edit'] > $karma_report['karma_report_time'])
				{
					$block_row['ITEM_REMARK'] = $this->user->lang['KARMA_REPORT_ITEM_EDITED'];
				}
				$this->template->assign_block_vars('received_karma', $block_row);

				// Determine if the karma was altered after being reported
				if ($karma_row['karma_score'] != $karma_report['reported_karma_score'] || $karma_row['karma_comment'] != $karma_report['reported_karma_comment'])
				{
					$this->template->assign_var('S_KARMA_REPORT_KARMA_EDITED', true);

					// Add the karma as it is now to the template
					$karma_data = $karma_manager->format_karma_row($karma_row);
					$block_row = array();
					foreach ($karma_data as $key => $value)
					{
						$block_row[strtoupper($key)] = $value;
					}
					// TODO Determine if the item was edited after the karma was updated?
					$this->template->assign_block_vars('received_karma', $block_row);
				}

				$this->template->assign_vars(array(
					'L_TITLE'			=> $this->user->lang['MCP_REPORTED_KARMA'],

					'KARMA_REPORTER'				=> get_username_string('full', $karma_report['user_id'], $karma_report['username'], $karma_report['user_colour']),
					'KARMA_REPORT_DATE'				=> $this->user->format_date($karma_report['karma_report_time']),
					'KARMA_REPORT_TEXT'				=> nl2br($karma_report['karma_report_text']),
					'KARMA_REPORT_ID'				=> $karma_report['karma_report_id'],
					'S_KARMA_REPORT_CLOSED'			=> $karma_report['karma_report_closed'],
					'U_KARMA_EDIT'					=> ($this->auth->acl_get('m_karma_edit')) ? $this->helper->url("givekarma/{$karma_row['karma_type_name']}/{$karma_row['item_id']}", "giver={$karma_row['giving_user_id']}") : '',
					'U_KARMA_DELETE'				=> ($this->auth->acl_get('m_karma_delete')) ? $this->helper->url("givekarma/{$karma_row['karma_type_name']}/{$karma_row['item_id']}", "giver={$karma_row['giving_user_id']}&amp;delete") : '',
				));

				$this->tpl_name = 'mcp_karma_report_details';
			break;
			case 'reports':
			case 'reports_closed':
				// Get the reports
				// TODO only show reports for forums that this moderator may moderate
				// TODO allow different ways of sorting
				// TODO allow showing only recent reports?
				$closed = ($mode == 'reports_closed');
				$start = $this->request->variable('start', 0);
				$karma_reports_list = $report_model->list_karma_reports($closed, $this->config['topics_per_page'], $start);
				$total = $karma_reports_list['total'];
				$karma_reports = $karma_reports_list['karma_reports'];

				// Put them in a template block variable
				foreach ($karma_reports as $karma_report)
				{
					// Get the reported karma
					$karma_row = $karma_manager->get_karma_row($karma_report['karma_id']);
					if ($karma_row !== false)
					{
						// TODO it might make sense to write a get_item_data alternative that takes a karma_id :P
						// On the other hand, I could follow the pattern I see most in phpBB and always get the reported karma with the report... Hmmm...
						$item_data = $karma_manager->get_item_data($karma_row['karma_type_name'], $karma_row['item_id']);
					}
					
					$this->template->assign_block_vars('karma_reports', array(
						// TODO that module name is ridiculously long right now, there probably should be a way to define a shorter one
						'U_VIEW_DETAILS'				=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", "i=phpbb_ext_phpbb_karma_mcp_reported_karma&amp;mode=report_details&amp;r={$karma_report['karma_report_id']}"),
						'REPORTED_KARMA_SUMMARY'		=> ($karma_row !== false)
							? sprintf($this->user->lang['REPORTED_KARMA_SUMMARY'], $karma_manager->format_karma_score($karma_report['reported_karma_score']), $item_data['title'])
							: $this->user->lang['KARMA_DELETED'],
 						'REPORTED_KARMA_GIVER_FULL'		=> get_username_string('full', $karma_row['giving_user_id'], $karma_row['giving_username'], $karma_row['giving_user_colour']),
 						'REPORTED_KARMA_RECEIVER_FULL'	=> get_username_string('full', $karma_row['receiving_user_id'], $karma_row['receiving_username'], $karma_row['receiving_user_colour']),
						'REPORTED_KARMA_TIME'			=> $this->user->format_date($karma_report['reported_karma_time']),

						'REPORTER_FULL'	=> get_username_string('full', $karma_report['user_id'], $karma_report['username'], $karma_report['user_colour']),
						'REPORT_TIME'	=> $this->user->format_date($karma_report['karma_report_time']),
						'REPORT_ID'		=> $karma_report['karma_report_id']
					));
				}

				// Generate pagination
				$base_url = $this->u_action;
				phpbb_generate_template_pagination($this->template, $base_url, 'pagination', 'start', $total, $this->config['topics_per_page'], $start);

				$this->template->assign_vars(array(
					'L_EXPLAIN'				=> ($mode == 'reports') ? $this->user->lang['MCP_KARMA_REPORTS_OPEN_EXPLAIN'] : $this->user->lang['MCP_KARMA_REPORTS_CLOSED_EXPLAIN'],
					'L_TITLE'				=> ($mode == 'reports') ? $this->user->lang['MCP_KARMA_REPORTS_OPEN'] : $this->user->lang['MCP_KARMA_REPORTS_CLOSED'],
					
					'S_MCP_ACTION'			=> $this->u_action,
					'S_CLOSED'				=> $closed,

					'PAGE_NUMBER'			=> phpbb_on_page($this->template, $this->user, $base_url, $total, $this->config['topics_per_page'], $start),
					'TOTAL'					=> $total,
					'TOTAL_KARMA_REPORTS'	=> $this->user->lang('LIST_KARMA_REPORTS', (int) $total),
					)
				);

				$this->tpl_name = 'mcp_karma_reports';
		}
	}

	private function close_karma_report($karma_report_id_list, $action)
	{
		$report_model = $this->container->get('karma.includes.report_model');

		// Get the karma reports
		try
		{
			// TODO this probably isn't necessary, though it makes sense to somehow check if the reports exist
			$karma_reports = $report_model->get_karma_reports($karma_report_id_list);
		}
		catch (OutOfBoundsException $e)
		{
			trigger_error($e->getMessage());
		}

		// Prepare the confirm_box
		$redirect = $this->request->variable('redirect', '');

		$s_hidden_fields = build_hidden_fields(array(
			'karma_report_id_list'	=> $karma_report_id_list,
			'action'				=> $action,
			'redirect'				=> $redirect,
		));

		// Check the confirm_box
		if (confirm_box(true))
		{
			// The moderator has confirmed this close/delete operation, so carry it out
			$delete = ($action == 'delete');
			$report_model->close_karma_reports($karma_report_id_list, $delete);

			// Log the operation
			$karma_manager = $this->container->get('karma.includes.manager');
			foreach ($karma_reports as $karma_report)
			{
				$karma_row = $karma_manager->get_karma_row($karma_report['karma_id']);
				if ($karma_row !== false)
				{
					$item_data = $karma_manager->get_item_data($karma_row['karma_type_name'], $karma_row['item_id']);
				}

				// TODO That phpbb_log::add() function seriously needs some examples or documentation; requires lots of source reading now :/
				$this->phpbb_log->add(
					'mod',
					$this->user->data['user_id'],
					$this->user->ip,
					'LOG_KARMA_REPORT_' . strtoupper($action) . 'D',
					time(),
					array(
						'forum_id'	=> 0,
						'topic_id'	=> 0,
						($karma_row !== false) ? $item_data['title'] : $this->user->lang['KARMA_DELETED'],
						($karma_row !== false) ? $item_data['author']['username'] : $this->user->lang['KARMA_DELETED'],
					)
				);
				// TODO Would it be a good idea to use the forum_id and topic_id when the karma_type_name === 'post'?
			}

			// TODO notifications

			// Show the succes page
			$redirect = build_url(array('mode', 'r', 'quickmod', 'confirm_key')) . '&amp;mode=reports';
 			meta_refresh(3, $redirect);
			trigger_error($this->user->lang['KARMA_REPORT' . ((sizeof($karma_report_id_list) > 1) ? 'S_' : '_') . strtoupper($action) . 'D_SUCCESS'] . '<br /><br />' . sprintf($this->user->lang['RETURN_PAGE'], "<a href=\"$redirect\">", '</a>'));
		}
		else
		{
			// No confirmation received, display the confirm_box
			confirm_box(false, $this->user->lang[strtoupper($action) . '_REPORT' . ((sizeof($karma_report_id_list) > 1) ? 'S' : '') . '_CONFIRM'], $s_hidden_fields);
			// TODO karma-specific translations for this confirmation message necessary?

			// If we arrive here, the confirm_box was cancelled
			if (empty($redirect))
			{
				$redirect = build_url(array('confirm_key')); // Go back to the report details
			}
			redirect($redirect);
		}
	}
}
