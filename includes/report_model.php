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

class phpbb_ext_phpbb_karma_includes_report_model
{
	/**
	 * Container object
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * Database object
	 * @var phpbb_db_driver
	 */
	protected $db;

	/**
	 * User object
	 * @var phpbb_user
	 */
	protected $user;

	/**
	 * Name of the karma_reports database table
	 * @var string
	 */
	protected $karma_reports_table;

	/**
	 * Constructor
	 * NOTE: The parameters of this method must match in order and type with
	 * the dependencies defined in the services.yml file for this service.
	 * 
	 * @param ContainerBuilder		$container				Container object (no type verification to allow testing with a mock container)
	 * @param phpbb_db_driver		$db						Database Object
	 * @param phpbb_user			$user					User object
	 * @param string				$karma_reports_table	Name of the karma_reports database table
	 */
	public function __construct($container, phpbb_db_driver $db, phpbb_user $user, $karma_table, $karma_reports_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->user = $user;
		$this->karma_table = $karma_table;
		$this->karma_reports_table = $karma_reports_table;
	}

	public function report_karma($karma_id, $reporter_id, $karma_report_text = '', $karma_report_time = -1)
	{
		// Retrieve information about the reported karma
		$karma_manager = $this->container->get('karma.includes.manager');
		$karma_row = $karma_manager->get_karma_row($karma_id);
		if ($karma_row === false)
		{
			throw new OutOfBoundsException('NO_KARMA');
		}

		// Check the reporter_id
		if (!$this->user_id_exists($reporter_id))
		{
			throw new OutOfBoundsException('NO_USER');
		}

		// Ensure the report text isn't too long
		$karma_report_text = truncate_string($karma_report_text, 65535, 65535);

		// Validate the karma report time and ensure it is set
		if ($karma_report_time >= pow(2, 32))
		{
			throw new OutOfBoundsException('KARMA_REPORT_TIME_TOO_LARGE');
		}
		if ($karma_report_time < 0)
		{
			$karma_report_time = time();
		}

		// Insert the report into the database
		$sql_ary = array(
			'karma_id'					=> $karma_id,
			'reporter_id'				=> $reporter_id,
			'karma_report_time'			=> $karma_report_time,
			'karma_report_text'			=> $karma_report_text,
			'reported_karma_score'		=> $karma_row['karma_score'],
			'reported_karma_time'		=> $karma_row['karma_time'],
			'reported_karma_comment'	=> $karma_row['karma_comment'],
		);
		$this->db->sql_query(
			'INSERT INTO ' . $this->karma_reports_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary)
		);

		// Mark the karma in question as reported
		$sql_ary = array(
			'karma_reported'	=> 1,
		);
		$sql = 'UPDATE ' . $this->karma_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE karma_id = ' . (int) $karma_id;
		$this->db->sql_query($sql);
	}

	public function get_karma_report($karma_report_id)
	{
		$sql_array = array(
			'SELECT'	=> 'kr.*, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				$this->karma_reports_table => 'kr',
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'kr.reporter_id = u.user_id
							AND karma_report_id = ' . (int) $karma_report_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_report = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($karma_report === false)
		{
			throw new OutOfBoundsException('NO_KARMA_REPORT');
		}

		return $karma_report;
	}

	public function get_karma_reports($karma_report_id_list)
	{
		// TODO the code of these functions isn't dry enough; they all look so alike that I often type something in the wrong function :P
		$sql_array = array(
			'SELECT'	=> 'kr.*, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				$this->karma_reports_table => 'kr',
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'kr.reporter_id = u.user_id
							AND ' . $this->db->sql_in_set('kr.karma_report_id', $karma_report_id_list),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_reports = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$karma_reports[] = $row;
		}
		$this->db->sql_freeresult($result);

		if (empty($karma_reports))
		{
			throw new OutOfBoundsException('NO_KARMA_REPORT');
		}

		return $karma_reports;
	}

	public function list_karma_reports($closed, $reports_per_page, $start_at)
	{
		// First, count the total amount of reports
		$sql_array = array(
			'SELECT'	=> 'COUNT(kr.karma_report_id) as total',
			'FROM'		=> array(
				$this->karma_reports_table => 'kr',
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'kr.reporter_id = u.user_id
							AND kr.karma_report_closed = ' . (int) $closed,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$total = $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		// Now get the reports to be listed
		$sql_array['SELECT'] = 'kr.*, u.user_id, u.username, u.user_colour';
		$sql_array['ORDER_BY'] = 'kr.karma_report_time DESC'; // TODO this should probably be an option
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, (int) $reports_per_page, (int) $start_at);
		$karma_reports = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$karma_reports[] = $row;
		}
		$this->db->sql_freeresult($result);

		// TODO is not throwing an exception the right way?
// 		if (empty($karma_reports))
// 		{
// 			throw new OutOfBoundsException('NO_KARMA_REPORT');
// 		}

		return array(
			'total'			=> $total,
			'karma_reports'	=> $karma_reports,
		);
	}

	public function close_karma_reports($karma_report_id_list, $delete = false)
	{
		// Get the ids of the karma reported
		$karma_reports = $this->get_karma_reports($karma_report_id_list);
		$karma_id_list = array();
		foreach ($karma_reports as $karma_report)
		{
			$karma_id_list[] = $karma_report['karma_id'];
		}

		// Start a transaction to prevent ending up in an inconsistent state
		$this->db->sql_transaction('begin');

		// Close/delete the reports
		if ($delete)
		{
			$sql = 'DELETE FROM ' . $this->karma_reports_table;
		}
		else
		{
			$sql = 'UPDATE ' . $this->karma_reports_table . '
					SET karma_report_closed = 1';
		}
		$sql .= ' WHERE ' . $this->db->sql_in_set('karma_report_id', $karma_report_id_list);
		$this->db->sql_query($sql);

		// Unmark the reported karma
		$sql = 'UPDATE ' . $this->karma_table . '
				SET karma_reported = 0
				WHERE ' . $this->db->sql_in_set('karma_id', $karma_id_list);
		$this->db->sql_query($sql);

		// Commit the transaction
		$this->db->sql_transaction('commit');
	}

	public function delete_karma_reports_by_karma_id($karma_id)
	{
		// Delete the karma reports
		$sql = 'DELETE FROM ' . $this->karma_reports_table . '
				WHERE karma_id = ' . (int) $karma_id;
		$this->db->sql_query($sql);

		// Unmark the reported karma (if applicable)
		$sql = 'UPDATE ' . $this->karma_table . '
				SET karma_reported = 0
				WHERE karma_id = ' . (int) $karma_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Checks if the given user ID belongs to an existing user
	 * 
	 * @param	int		$user_id	The user ID to be validated
	 * @return	bool				true if the user exists, false otherwise
	 */
	private function user_id_exists($user_id) {
		// TODO Just copied this from the manager, but there should be some way to make this DRY
		$sql_array = array(
			'SELECT'	=> 'count(*) AS num_users',
			'FROM'		=> array(USERS_TABLE => 'u'),
			'WHERE'		=> 'user_id = ' . (int) $user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$num_users = $this->db->sql_fetchfield('num_users');
		$this->db->sql_freeresult($result);

		return $num_users == 1;
	}
}
