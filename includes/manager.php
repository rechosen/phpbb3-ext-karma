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

class phpbb_ext_phpbb_karma_includes_manager
{
	/**
	 * Array that contains all available karma types which are passed via the
	 * service container
	 * @var array
	 */
	private $karma_types;

	/**
	 * Cache object
	 * @var phpbb_cache_service
	 */
	private $cache;

	/**
	 * Container object
	 * @var ContainerBuilder
	 */
	private $container;

	/**
	 * Database object
	 * @var phpbb_db_driver
	 */
	private $db;

	/**
	 * User object
	 * @var phpbb_user
	 */
	private $user;

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
	 * @param array					$karma_types		Available karma type names
	 * @param phpbb_cache_service	$cache				Cache object
	 * @param ContainerBuilder		$container			Container object (no type verification to allow testing with a mock container)
	 * @param phpbb_db_driver		$db					Database Object
	 * @param string				$karma_table		Name of the karma database table
	 * @param string				$karma_types_table	Name of the karma_types database table
	 */
	public function __construct($karma_types, phpbb_cache_service $cache, $container, phpbb_db_driver $db, phpbb_user $user, $karma_table, $karma_types_table)
	{
		$this->karma_types = $karma_types;
		$this->cache = $cache;
		$this->container = $container;
		$this->db = $db;
		$this->user = $user;
		$this->karma_table = $karma_table;
		$this->karma_types_table = $karma_types_table;
	}

	/**
	 * Stores given karma in the database
	 * 
	 * @param string	$karma_type_name	The type of item on which the karma was given
	 * @param int		$item_id			The ID of the item on which the karma was given
	 * @param int		$giving_user_id		The ID of the user giving the karma
	 * @param int		$karma_score		The given karma score
	 * @param string	$karma_comment		The comment given with the karma
	 * @param int		$karma_time			The time on which the karma was given
	 */
	public function store_karma($karma_type_name, $item_id, $giving_user_id, $karma_score, $karma_comment = '', $karma_time = -1)
	{
		// Set the receiving user ID
		$karma_type = $this->get_type_class($karma_type_name);
		$receiving_user_id = $karma_type->get_author($item_id);

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
		$karma_comment = truncate_string($karma_comment, 65535, 65535);

		// Validate the karma time and ensure it is set
		if ($karma_time >= pow(2, 32))
		{
			throw new OutOfBoundsException('KARMA_TIME_TOO_LARGE');
		}
		if ($karma_time < 0)
		{
			$karma_time = time();
		}

		// Insert the karma into the database
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
		$change_sign = ($score_change < 0) ? '-' : '+';
		$this->db->sql_query('
			UPDATE ' . USERS_TABLE . '
			SET user_karma_score = user_karma_score ' . $change_sign . ' ' . abs($score_change) . '
			WHERE user_id = ' . (int) $receiving_user_id
		);
	}

	/**
	 * Gets the karma score of the user(s) with the specified ID(s)
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
		$karma_type->check_permission($item_id);

		return array(
			'url'		=> $karma_type->get_url($item_id),
			'title'		=> $karma_type->get_title($item_id),
			'author'	=> $karma_type->get_author($item_id),
		);
	}

	/**
	 * Gets all karma that was ever received by a certain user
	 * 
	 * @param	int		$user_id		The ID of the user
	 * @param	bool	$newestfirst	Sort on time DESC (true) or ASC (false)
	 * @return	array					The received karma, one array per row, with keys 'score', 'item_url', 'item_title', 'received_at', 'given_by', 'comment'
	 */
	public function get_karma_received_by_user($user_id, $newestfirst = true)
	{
		$received_karma = array();

		$sql_array = array(
			'SELECT'	=> 'k.*, kt.*, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				$this->karma_table 			=> 'k',
				$this->karma_types_table	=> 'kt',
				USERS_TABLE					=> 'u',
			),
			'WHERE'		=> 'k.karma_type_id = kt.karma_type_id
							AND k.giving_user_id = u.user_id
							AND kt.karma_type_enabled = 1
							AND receiving_user_id = ' . (int) $user_id,
			'ORDER BY'	=> 'karma_time '. (($newestfirst) ? 'DESC' : 'ASC'),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$karma_type = $this->get_type_class($row['karma_type_name']);

			$received_karma[] = array(
				'score'			=> $row['karma_score'],
				'item_url'		=> $karma_type->get_url($row['item_id']),
				'item_title'	=> $karma_type->get_title($row['item_id']),
				'received_at'	=> $this->user->format_date($row['karma_time']),
				'given_by'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'comment'		=> $row['karma_comment'],
			);
		}
		$this->db->sql_freeresult($result);

		return $received_karma;
	}

	/**
	 * Helper to get the type class of a certain karma type
	 * 
	 * @param	string	$karma_type_name						The name of the type to get a class instance of
	 * @return	phpbb_ext_phpbb_karma_includes_type_interface	An instance of the corresponding type class
	 */
	private function get_type_class($karma_type_name)
	{
		$karma_type_name = (strpos($karma_type_name, 'karma.type.') === 0) ? $karma_type_name : 'karma.type.' . $karma_type_name;

		return $this->container->get($karma_type_name);
	}

	/**
	 * Checks if the given user ID belongs to an existing user
	 * 
	 * @param	int		$user_id	The user ID to be validated
	 * @return	bool				true if the user exists, false otherwise
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
	 * @param	int		$karma_type_id	The type of the item on which the karma was given
	 * @param	int		$item_id		The item on which the karma was given
	 * @param	int		$giving_user_id	The user which gave the karma
	 * @return	int|bool				The given karma, or false if no karma was found
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
