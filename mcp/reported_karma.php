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
		global $phpbb_container, $phpbb_log, $request, $user, $template;

		$this->container = $phpbb_container;
		$this->phpbb_log = $phpbb_log;
		$this->request = $request;
		$this->user = $user;
		$this->template = $template;

		$user->add_lang_ext('phpbb/karma', 'karma');
	}

	public function main($id, $mode)
	{
		global $action; // TODO is using the global $action ok?

		$this->page_title = 'MCP_REPORTED_KARMA';

		$report_model = $this->container->get('karma.includes.report_model');
		switch ($mode)
		{
			case 'report_details':
				// Determine if the moderator wants to close or delete the report
				switch ($action)
				{
					case 'close':
					case 'delete':
						$karma_report_id_list = $this->request->variable('karma_report_id_list', array(0));
						$this->close_karma_report($karma_report_id_list, $action);
					break;
				}

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

				$karma_manager = $this->container->get('karma.includes.manager');
				$karma = $karma_manager->get_karma_data($karma_report['karma_id']);
				if ($karma === false)
				{
					trigger_error('NO_KARMA');
				}

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
				// Determine if the item was edited after the reported karma was given
				if ($karma['item_last_edit'] > $karma_report['karma_report_time'])
				{
					$block_row['ITEM_REMARK'] = $this->user->lang['KARMA_REPORT_ITEM_EDITED'];
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
					// TODO Determine if the item was edited after the karma was updated?
					$this->template->assign_block_vars('received_karma', $block_row);
				}

				$this->template->assign_vars(array(
					'KARMA_REPORTER'				=> get_username_string('full', $karma_report['user_id'], $karma_report['username'], $karma_report['user_colour']),
					'KARMA_REPORT_DATE'				=> $this->user->format_date($karma_report['karma_report_time']),
					'KARMA_REPORT_TEXT'				=> $karma_report['karma_report_text'],
					'KARMA_REPORT_ID'				=> $karma_report['karma_report_id'],
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

	private function close_karma_report($karma_report_id_list, $action)
	{
		global $phpbb_root_path, $phpEx; // TODO see note with user_get_id_name() below
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

		// TODO check moderator permissions

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
				if ($karma_row === false)
				{
					$item_data = array(
						'author'	=> '[karma deleted]',
						'title'		=> '[karma deleted]',
					);
				}
				else
				{
					$item_data = $karma_manager->get_item_data($karma_row['karma_type_name'], $karma_row['karma_id']);

					// TODO there must be a prettier way to convert a user_id to a username
					include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
					$user_ids = array($item_data['author']);
					$usernames = array();
					user_get_id_name($user_ids, $usernames);
					$item_data['author'] = reset($usernames);
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
						$item_data['title'],
						$item_data['author'],
					)
				);
				// TODO Would it be a good idea to use the forum_id and topic_id when the karma_type_name === 'post'?
			}

			// TODO notifications

			// Show the succes page
			$redirect = build_url(array('mode', 'r', 'quickmod', 'confirm_key')) . '&amp;mode=reports'; // TODO how to redirect to the right module id? Perhaps give the karma module a string name?
// 			meta_refresh(3, $redirect); TODO enable this redirect once karma report listing is implemented
			trigger_error($this->user->lang['KARMA_REPORT_' . ((sizeof($karma_report_id_list) > 1) ? 'S' : '') . strtoupper($action) . 'D_SUCCESS']); // TODO return links
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
