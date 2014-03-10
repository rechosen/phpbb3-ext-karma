<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\karma\includes;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class manager
{
	/**
	* Array that contains all available karma types which are passed via the
	* service container
	* @var array
	*/
	private $karma_types;

	/**
	* Cache object
	* @var \phpbb\cache\service
	*/
	private $cache;

	/**
	* Container object
	* @var ContainerBuilder
	*/
	private $container;

	/**
	* Database object
	* @var \phpbb\db\driver\driver
	*/
	private $db;

	/**
	* Dispatcher object
	* @var \phpbb\event\dispatcher
	*/
	private $dispatcher;

	/**
	* Controller helper object
	* @var \phpbb\controller\helper
	*/
	protected $helper;

	/**
	* User object
	* @var \phpbb\user
	*/
	private $user;

	/**
	* phpBB root path
	* @var string
	*/
	protected $phpbb_root_path;

	/**
	* php file extension
	* @var string
	*/
	protected $php_ext;

	/**
	* Name of the karma database table
	* @var string
	*/
	private $karma_table;

	/**
	* Name of the karma_types database table
	* @var string
	*/
	private $karma_types_table;

	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	* 
	* @param array						$karma_types		Available karma type names
	* @param \phpbb\cache\service		$cache				Cache object
	* @param ContainerBuilder			$container			Container object (no type verification to allow testing with a mock container)
	* @param \phpbb\db\driver\driver			$db					Database Object
	* @param \phpbb\event\dispatcher			$dispatcher			Dispatcher object
	* @param \phpbb\controller\helper	$helper				Controller helper object
	* @param \phpbb\user					$user				User object
	* @param string						$phpbb_root_path	phpBB root path
	* @param string						$php_ext			php file extension
	* @param string						$karma_table		Name of the karma database table
	* @param string						$karma_types_table	Name of the karma_types database table
	*/
	public function __construct($karma_types, \phpbb\cache\service $cache, $container, \phpbb\db\driver\driver $db, \phpbb\event\dispatcher $dispatcher, \phpbb\controller\helper $helper, \phpbb\user $user, $phpbb_root_path, $php_ext, $karma_table, $karma_types_table)
	{
		$this->karma_types = $karma_types;
		$this->cache = $cache;
		$this->container = $container;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->helper = $helper;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->karma_table = $karma_table;
		$this->karma_types_table = $karma_types_table;
	}

	/**
	* Stores given karma in the database
	* 
	* @param	string	$karma_type_name	The type of item on which the karma was given
	* @param	int		$item_id			The ID of the item on which the karma was given
	* @param	int		$giving_user_id		The ID of the user giving the karma
	* @param	int		$karma_score		The given karma score
	* @param	string	$karma_comment		The comment given with the karma
	* @param	int		$karma_time			The time on which the karma was given
	* @return	null
	*/
	public function store_karma($karma_type_name, $item_id, $giving_user_id, $karma_score, $karma_comment = '', $karma_time = -1 /* TODO perhaps false is better than -1 as a default? */)
	{
		// Set the receiving user ID
		$karma_type = $this->get_type_class($karma_type_name);
		$receiving_user = $karma_type->get_author($item_id);
		$receiving_user_id = $receiving_user['user_id'];

		// Get the karma_type_id
		$karma_type_id = $this->get_karma_type_id($karma_type_name);

		// Check if the giving user ID exists
		if (!$this->user_id_exists($giving_user_id))
		{
			throw new OutOfBoundsException('NO_USER');
		}

		// Check if the karma score is within bounds
		if ($karma_score < -128 || $karma_score > 127)
		{
			throw new OutOfBoundsException('KARMA_SCORE_OUTOFBOUNDS');
		}

		// Ensure the karma comment isn't too long
		$karma_comment = truncate_string($karma_comment, 4000, 4000);

		// Validate the karma time and ensure it is set
		if ($karma_time >= pow(2, 31))
		{
			throw new OutOfBoundsException('KARMA_TIME_TOO_LARGE');
		}
		if ($karma_time < 0)
		{
			$karma_time = time();
		}

		// Insert the karma into the database
		// TODO make this a transaction to prevent ending up in an inconsistent state
		// TODO and look for other places where a transaction might be appropriate
		$current_score = $this->get_karma_score($karma_type_id, $item_id, $giving_user_id);
		if ($current_score === false)
		{
			$sql_ary = array(
				'karma_type_id'		=> (int) $karma_type_id,
				'item_id'			=> (int) $item_id,
				'giving_user_id'	=> (int) $giving_user_id,
				'receiving_user_id'	=> (int) $receiving_user_id,
				'karma_score'		=> (int) $karma_score,
				'karma_time'		=> $karma_time,
				'karma_comment'		=> $karma_comment,
			);
			$this->db->sql_query(
				'INSERT INTO ' . $this->karma_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary)
			);
		}
		else
		{
			// TODO only allow updating if an option is true
			$sql_ary = array(
				'karma_score'	=> (int) $karma_score,
				'karma_time'	=> $karma_time,
				'karma_comment'	=> $karma_comment,
			);
			$this->db->sql_query('
				UPDATE ' . $this->karma_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE karma_type_id = ' . (int) $karma_type_id . '
					AND item_id = ' . (int) $item_id . '
					AND giving_user_id = ' . (int) $giving_user_id
			);
		}

		// Now update the karma_score column in the _users table
		$score_change = $karma_score - $current_score;
		$this->update_user_karma_score($receiving_user_id, $score_change);
	}

	/**
	* Deletes karma given on the specified item from the database
	* 
	* @param	string	$karma_type_name	The name of the type of the item
	* @param	int		$item_id			The ID of the item on which the karma was given
	* @return	null
	*/
	public function delete_karma_given_on_item($karma_type_name, $item_id)
	{
		// Set the receiving user ID to update the user_karma_score later
		$karma_type = $this->get_type_class($karma_type_name);
		$receiving_user = $karma_type->get_author($item_id);
		$receiving_user_id = $receiving_user['user_id'];

		// Begin a transaction because we're doing multiple related database operations in a row
		$this->db->sql_transaction('begin');

		// Get the karma to be deleted
		$sql_array = array(
			'SELECT'	=> 'k.karma_id, k.karma_score, kt.karma_type_enabled',
			'FROM'		=> array(
				$this->karma_table			=> 'k',
				$this->karma_types_table	=> 'kt',
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							kt.karma_type_name = \'' . $this->db->sql_escape($karma_type_name) . '\'
							AND item_id = ' . (int) $item_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		// Now build an array of both karma IDs and karma score modifications per user
		$karma_id_list = array();
		$score_changes = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$karma_id_list[] = $row['karma_id'];

			if ($row['karma_type_enabled'])
			{
				if (!isset($score_changes[$row['receiving_user_id']]))
				{
					$score_changes[$row['receiving_user_id']] = 0;
				}
				$score_changes[$row['receiving_user_id']] -= $row['karma_score'];
			}
		}
		$this->db->sql_freeresult($result);

		// Delete the karma from the database
		$this->delete_karma_by_ids($karma_id_list, $score_changes);

		// End the transaction
		$this->db->sql_transaction('commit');
	}

	/**
	* Deletes given karma from the database
	* 
	* @param	string	$karma_type_name	The type of item on which the karma was given
	* @param	int		$item_id			The ID of the item on which the karma was given
	* @param	int		$giving_user_id		The ID of the user who gave the karma
	* @return	null
	*/
	public function delete_karma($karma_type_name, $item_id, $giving_user_id)
	{
		// Set the receiving user ID to update the user_karma_score later
		$karma_type = $this->get_type_class($karma_type_name);
		$receiving_user = $karma_type->get_author($item_id);
		$receiving_user_id = $receiving_user['user_id'];

		// Check if the giving user ID exists
		if (!$this->user_id_exists($giving_user_id))
		{
			throw new OutOfBoundsException('NO_USER');
		}

		// Begin a transaction because we're doing multiple related database operations in a row
		$this->db->sql_transaction('begin');

		// Get the karma to be deleted
		$karma_row = $this->get_given_karma_row($karma_type_name, $item_id, $giving_user_id);

		// Get the score change that will occur when this karma is deleted
		$score_change = array();
		if ($karma_row)
		{
			$score_change[$karma_row['receiving_user_id']] = -$karma_row['karma_score'];
		}

		// Delete the karma from the database
		$this->delete_karma_by_ids(array($karma_row['karma_id']), $score_change);
		
		// End the transaction
		$this->db->sql_transaction('commit');
	}

	/**
	* Deletes karma given by the specified user from the database
	* 
	* @param	int		$giving_user_id		The ID of the user who gave the karma
	* @return	null
	*/
	public function delete_karma_given_by_user($giving_user_id)
	{
		// Check if the giving user ID exists
		if (!$this->user_id_exists($giving_user_id))
		{
			throw new OutOfBoundsException('NO_USER');
		}

		// Begin a transaction because we're doing multiple related database operations in a row
		$this->db->sql_transaction('begin');

		// Get the karma to be deleted
		$sql_array = array(
			'SELECT'	=> 'k.karma_id, k.receiving_user_id, k.karma_score, kt.karma_type_enabled',
			'FROM'		=> array(
				$this->karma_table			=> 'k',
				$this->karma_types_table	=> 'kt',
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							AND giving_user_id = ' . (int) $giving_user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		// Now build an array of both karma IDs and karma score modifications per user
		$karma_id_list = array();
		$score_changes = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$karma_id_list[] = $row['karma_id'];

			if ($row['karma_type_enabled'])
			{
				if (!isset($score_changes[$row['receiving_user_id']]))
				{
					$score_changes[$row['receiving_user_id']] = 0;
				}
				$score_changes[$row['receiving_user_id']] -= $row['karma_score'];
			}
		}
		$this->db->sql_freeresult($result);

		// Delete the karma from the database
		$this->delete_karma_by_ids($karma_id_list, $score_changes);

		// End the transaction
		$this->db->sql_transaction('commit');
	}

	/**
	* Gets the karma score of the user(s) with the specified ID(s)
	* TODO Currently unused; can it be deleted or will it be used externally?
	* 
	* @param	int|array	$user_id	A user_id or array of user_ids
	* @return	int						The karma score of the specified user
	*/
	public function get_user_karma_score($user_id)
	{
		if (is_array($user_id))
		{
			$karma_scores = array();
			foreach ($user_id as $id)
			{
				$karma_score[$id] = get_user_karma_score($id);
			}
			return $karma_scores;
		}

		$sql_array = array(
			'SELECT'	=> 'user_karma_score',
			'FROM'		=> array(USERS_TABLE => 'u'),
			'WHERE'		=> 'user_id = ' . (int) $user_id
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$user_karma_score = $this->db->sql_fetchfield('user_karma_score');
		$this->db->sql_freeresult($result);

		if ($user_karma_score === false)
		{
			throw new OutOfBoundsException('NO_USER');
		}

		return (int) $user_karma_score;
	}

	/**
	* Retrieves the url, title and author user_id of the specified item
	* 
	* @param	string	$karma_type_name	The type of the item
	* @param	int		$item_id			The ID of the item
	* @return	array						An array containing the keys 'url', 'title' and 'author' pointing to the corresponding information
	*/
	public function get_item_data($karma_type_name, $item_id)
	{
		$karma_type = $this->get_type_class($karma_type_name);

		// Ensure the current user has permission to get this information
		if (!$karma_type->check_permission($item_id))
		{
			trigger_error('NOT_AUTHORISED');
		}

		return array(
			'url'		=> $karma_type->get_url($item_id),
			'title'		=> $karma_type->get_title($item_id),
			'author'	=> $karma_type->get_author($item_id),
		);
	}

	/**
	* Gets all karma that was ever received by a certain user in template-ready format
	* 
	* @param	int		$user_id		The ID of the user
	* @param	int		$karma_per_page	How many karma rows to get for this page
	* @param	int		$start_at		The row to start at for this page
	* @param	bool	$newestfirst	Sort on time DESC (true) or ASC (false)
	* @return	array					An array with the following keys:
	* 											'total' => (int) The total amount of karma rows received by this user
	* 											'received_karma' => (array) An array of arrays with received karma generated by format_karma_row
	*/
	public function get_karma_received_by_user($user_id, $karma_per_page, $start_at, $newestfirst = true)
	{
		// First, count the total amount of karma received by this user
		$sql_array = array(
			'SELECT'	=> 'COUNT(k.karma_id) as total',
			'FROM'		=> array(
				$this->karma_table 			=> 'k',
				$this->karma_types_table	=> 'kt',
				USERS_TABLE					=> 'u',
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							AND k.giving_user_id = u.user_id
							AND kt.karma_type_enabled = 1
							AND receiving_user_id = ' . (int) $user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$total = $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		// Now get the karma to be listed
		$sql_array['SELECT'] = 'k.*, kt.*, u.username as giving_username, u.user_colour as giving_user_colour';
		$sql_array['ORDER_BY'] = 'karma_time '. (($newestfirst) ? 'DESC' : 'ASC');
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $karma_per_page, $start_at);
		$received_karma = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$received_karma[] = $this->format_karma_row($row);
		}
		$this->db->sql_freeresult($result);

		return array(
			'total'				=> $total,
			'received_karma'	=> $received_karma,
		);
	}
	
	/**
	* Gets a specific karma row and returns it in template-ready format
	* 
	* @param	int	$karma_id	The ID of the karma
	* @return	array|bool		An array with the information generated by format_karma_row; false otherwise
	*/
	public function get_karma_data($karma_id)
	{
		$karma_row = $this->get_karma_row($karma_id);
		return ($karma_row !== false) ? $this->format_karma_row($karma_row) : false;
	}

	/**
	* Gets the raw database row of the karma with the specified ID
	* 
	* @param	int	$karma_id	The ID of the karma to retrieve
	* @return	array|bool		The row of the karma, or false if it doesn't exist
	*/
	public function get_karma_row($karma_id)
	{
		$sql_array = array(
			'SELECT'	=> 'k.*, kt.karma_type_name, gu.username as giving_username, gu.user_colour as giving_user_colour, ru.username as receiving_username, ru.user_colour as receiving_user_colour',
			'FROM'		=> array(
				$this->karma_table			=> 'k',
				$this->karma_types_table	=> 'kt',
				USERS_TABLE					=> 'gu',
				USERS_TABLE . ' '			=> 'ru', // TODO this looks kind of ugly, improvement possible?
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							AND k.giving_user_id = gu.user_id
							AND k.receiving_user_id = ru.user_id
							AND kt.karma_type_enabled = 1
							AND karma_id = ' . (int) $karma_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_row = $this->db->sql_fetchrow();
		$this->db->sql_freeresult($result);

		return $karma_row;
	}

	/**
	* Converts raw karma database values to template-ready values
	* TODO: This function probably shouldn't be here, but it should be somewhere to keep the code DRY
	* 
	* @param	array	$karma_row	The karma row from the database
	* @return	array				The formatted karma row with the following keys:
	* 		'score' => (string) The formatted karma score
	* 		'item_url' => (string) The URL of the item the karma was given on
	* 		'item_title' => (string) The title of the item the karma was given on
	* 		'received_at' => (string) The user-formatted date on which the karma was received
	* 		'given_by' => (string) The full username_string of the giving user
	* 		'comment' => (string) The comment given with the karma
	* 		'reported' => (bool) Whether the karma was reported or not
	* 		'report_url' => (string) The URL of the page on which the karma may be reported
	* 		'item_last_edit' => (int) The timestamp of the last time the item the karma was given on was edited
	* 		'receiving_user_id' => (int) The ID of the user receiving the karma
	*/
	public function format_karma_row($karma_row) {
		$karma_type = $this->get_type_class($karma_row['karma_type_name']);

		return array(
			'score'				=> $this->format_karma_score($karma_row['karma_score']),
			'item_url'			=> $karma_type->get_url($karma_row['item_id']),
			'item_title'		=> $karma_type->get_title($karma_row['item_id']),
			'received_at'		=> $this->user->format_date($karma_row['karma_time']),
			'given_by'			=> get_username_string('full', $karma_row['giving_user_id'], $karma_row['giving_username'], $karma_row['giving_user_colour']),
			'comment'			=> $karma_row['karma_comment'],
			'reported'			=> (bool) $karma_row['karma_reported'],
			'report_url'		=> $this->helper->url("reportkarma/{$karma_row['karma_id']}"),
			'item_last_edit'	=> $karma_type->get_last_edit($karma_row['item_id']),
			'receiving_user_id'	=> $karma_row['receiving_user_id'],
		);
	}

	/**
	* Retrieve karma given on a certain item by a certain user
	* 
	* @param	string	$karma_type_name	The name of the type of the karma
	* @param	int		$item_id			The ID of the item
	* @param	int		$giving_user_id		The ID of the giving user
	* @return	array|bool					The row of the karma, or false if it doesn't exist
	*/
	public function get_given_karma_row($karma_type_name, $item_id, $giving_user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'k.*, gu.username as giving_username, gu.user_colour as giving_user_colour',
			'FROM'		=> array(
				$this->karma_table			=> 'k',
				$this->karma_types_table	=> 'kt',
				USERS_TABLE					=> 'gu',
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							AND k.giving_user_id = gu.user_id
							AND kt.karma_type_enabled = 1
							AND kt.karma_type_name = \'' . $this->db->sql_escape($karma_type_name) . '\'
							AND k.item_id = ' . (int) $item_id . '
							AND k.giving_user_id = ' . (int) $giving_user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_row = $this->db->sql_fetchrow();
		$this->db->sql_freeresult($result);

		return $karma_row;
	}

	/**
	* Put a + in front of the karma score if it is positive.
	* TODO this function probably doesn't belong here
	* 
	* @param	int	$karma_score	The karma score to be formatted
	* @return	string				The formatted karma score
	*/
	public function format_karma_score($karma_score)
	{
		return ($karma_score > 0) ? "+$karma_score" : (string) $karma_score;
	}

	/**
	* Marks/unmarks the specified karma as reported
	* 
	* @param	array	$karma_id_list	List of IDs of karma to be marked
	* @param	bool	$reported		True to mark as reported, false to unmark
	* @return	null
	*/
	public function mark_karma_reported($karma_id_list, $reported)
	{
		$sql_ary = array(
			'karma_reported'	=> (int) $reported,
		);
		$sql = 'UPDATE ' . $this->karma_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE ' . $this->db->sql_in_set('karma_id', $karma_id_list);
		$this->db->sql_query($sql);
	}

	/**
	* Deletes the specified karma.
	* Please do all karma deletions through this method as it calls an event
	* that allows handling deleted karma.
	* 
	* @param	array		$karma_id_list	List of IDs of karma to be deleted
	* @param	array|bool	$score_changes	An array of aggregated
	* 										receiving_user_id => score_change
	* 										pairs for all karma IDs to be
	* 										deleted. False if no score changes
	* 										are known.
	* @return	null
	*/
	private function delete_karma_by_ids($karma_id_list, $score_changes = false)
	{
		// Multiple database-related operations going on; ensure we're in a transaction
		$this->db->sql_transaction('begin');

		// Get the changes in user karma scores caused by these deletions
		if ($score_changes === false)
		{
			// No score changes specified, we'll have to construct them
			$sql_array = array(
				'SELECT'	=> 'k.receiving_user_id, k.karma_score',
				'FROM'		=> array(
					$this->karma_table			=> 'k',
					$this->karma_types_table	=> 'kt',
				),
				'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
								AND kt.karma_type_enabled = 1
								AND ' . $this->db->sql_in_set('karma_id', $karma_id_list),
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			// Now build an array of karma score modifications per user
			$score_changes = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				if (!isset($score_changes[$row['receiving_user_id']]))
				{
					$score_changes[$row['receiving_user_id']] = 0;
				}
				$score_changes[$row['receiving_user_id']] -= $row['karma_score'];
			}
			$this->db->sql_freeresult($result);
		}

		/**
		* Event before karma is deleted
		*
		* @event ext_phpbb_karma.delete_karma_before
		* @var	array	karma_id_list	List of IDs of karma to be deleted
		* @since 3.1-A1
		*/
		$vars = array('karma_id_list');
		extract($this->dispatcher->trigger_event('ext_phpbb_karma.delete_karma_before', compact($vars)));

		// Delete the karma
		$sql = "DELETE FROM {$this->karma_table}
				WHERE " . $this->db->sql_in_set('karma_id', $karma_id_list);
		$this->db->sql_query($sql);

		// Update the karma scores of the affected users
		foreach ($score_changes as $receiving_user_id => $score_change)
		{
			$this->update_user_karma_score($receiving_user_id, $score_change);
		}

		// End the transaction
		$this->db->sql_transaction('commit');
	}

	/**
	* Helper to get the type class of a certain karma type
	* 
	* @param	string	$karma_type_name					The name of the type to get a class instance of
	* @return	\phpbb\karma\includes\type\type_interface	An instance of the corresponding type class
	*/
	private function get_type_class($karma_type_name)
	{
		$karma_type_name = (strpos($karma_type_name, 'karma.type.') === 0) ? $karma_type_name : 'karma.type.' . $karma_type_name;

		return $this->container->get($karma_type_name);
	}

	/**
	* Checks if the given user ID belongs to an existing user
	* 
	* @param	int	$user_id	The user ID to be validated
	* @return	bool			true if the user exists, false otherwise
	*/
	private function user_id_exists($user_id) {
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

	/**
	* Gets the karma score given on the specified item by the specified user
	* 
	* @param	int	$karma_type_id	The type of the item on which the karma was given
	* @param	int	$item_id		The item on which the karma was given
	* @param	int	$giving_user_id	The user which gave the karma
	* @return	int|bool			The given karma, or false if no karma was found
	*/
	private function get_karma_score($karma_type_id, $item_id, $giving_user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'karma_score',
			'FROM'		=> array($this->karma_table => 'k'),
			'WHERE'		=> 'karma_type_id = ' . (int) $karma_type_id . '
							AND item_id = ' . (int) $item_id . '
							AND giving_user_id = ' . (int) $giving_user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_score = $this->db->sql_fetchfield('karma_score');
		$this->db->sql_freeresult($result);

		return ($karma_score !== false) ? (int) $karma_score : false;
	}

	/**
	* Updates the user_karma_score column for a certain user
	* 
	* @param	int	$user_id		The ID of the user
	* @param	int	$score_change	The value to add to the score (can be < 0)
	* @return	null
	*/
	private function update_user_karma_score($user_id, $score_change)
	{
		$change_sign = ($score_change < 0) ? '-' : '+';
		$this->db->sql_query('
			UPDATE ' . USERS_TABLE . '
			SET user_karma_score = user_karma_score ' . $change_sign . ' ' . abs($score_change) . '
			WHERE user_id = ' . (int) $user_id
		);
	}

	/**
	* Get the karma type ID from the name
	* 
	* @param	string	$karma_type_name	The karma type to get the id of
	* @return	int							The id of the requested karma type
	*/
	private function get_karma_type_id($karma_type_name) // TODO use protected instead of private unless the method _must_ not change, even when extending
	{
		$karma_type_ids = $this->cache->get('karma_type_ids');

		if ($karma_type_ids === false)
		{
			$karma_type_ids = array();

			$sql_array = array(
				'SELECT'	=> 'karma_type_id, karma_type_name',
				'FROM'		=> array($this->karma_types_table => 'kt'),
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$karma_type_ids[$row['karma_type_name']] = (int) $row['karma_type_id'];
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('karma_type_ids', $karma_type_ids);
		}

		if (!isset($karma_type_ids[$karma_type_name]))
		{
			if (!isset($this->karma_types[$karma_type_name]) && !isset($this->karma_types['karma.type.' . $karma_type_name]))
			// TODO giving a full type class name passed the first of these two tests but isn't valid at all otherwise
			// That very mistake is in the notification system code, too; ask EXreaction
			{
				//throw new OutOfBoundsException($this->user->lang('NO_KARMA_TYPE', $karma_type_name));
				throw new OutOfBoundsException(print_r($this->karma_types, true));
			}

			$sql = 'INSERT INTO ' . $this->karma_types_table . ' ' . $this->db->sql_build_array('INSERT', array(
				'karma_type_name'		=> $karma_type_name,
				'karma_type_enabled'	=> 1,
			));
			$this->db->sql_query($sql);

			$karma_type_ids[$karma_type_name] = (int) $this->db->sql_nextid();

			$this->cache->put('karma_type_ids', $karma_type_ids);
		}

		return $karma_type_ids[$karma_type_name];
	}
}
