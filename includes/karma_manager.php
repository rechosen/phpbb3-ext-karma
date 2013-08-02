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

use Symfony\Component\DependencyInjection\ContainerBuilder;

class phpbb_ext_phpbb_karma_includes_karma_manager
{
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
	 * Name of the karma database table
	 * @var string
	 */
	private $table_name;

	/**
	 * Constructor
	 * NOTE: The parameters of this method must match in order and type with
	 * the dependencies defined in the services.yml file for this service.
	 * 
	 * @param ContainerBuilder	$container	Container object
	 * @param phpbb_db_driver	$db			Database Object
	 * @param string			$table_name	Name of the karma database table
	 */
	public function __construct(ContainerBuilder $container, phpbb_db_driver $db, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	 * Stores given karma in the database
	 * 
	 * @param	array	$karma_row	An array containing at least the
	 * 								following keys (with appropriate values):
	 * 								post_id, giving_user_id, and karma_score.
	 * 								The other fields (receiving_user_id,
	 * 								karma_time, karma_comment) will be set to
	 * 								a default unless they are specified.
	 */
	public function store_karma($item_id, $karma_type_name, $giving_user_id, $karma_score, $karma_comment = '', $karma_time = -1)
	{
		// Set the receiving user ID
		$karma_type = $this->get_type_class($karma_type_name);
		$receiving_user_id = $karma_type->get_author($item_id);

		// Get the karma_type_id, simultaneously checking if the karma_type_name exists TODO make less ugly :P
		$sql_array = array(
			'SELECT'	=> 'karma_type_id',
			'FROM'		=> array('phpbb_karma_type' => 'k'),
			'WHERE'		=> "karma_type_name = '" . $this->db->sql_escape($karma_type_name) . "'",
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_type_id = $this->db->sql_fetchfield('karma_type_id');
		$this->db->sql_freeresult($result);
		if ($karma_type_id === false)
		{
			throw new OutOfBoundsException('NO_KARMA_TYPE');
		}

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
		$current_score = $this->get_karma_score($item_id, $karma_type_id, $giving_user_id);
		if ($current_score === 0)
		{
			$sql_ary = array(
				'item_id'			=> (int) $item_id,
				'karma_type_id'		=> (int) $karma_type_id,
				'giving_user_id'	=> (int) $giving_user_id,
				'receiving_user_id'	=> (int) $receiving_user_id,
				'karma_score'		=> (int) $karma_score,
				'karma_time'		=> $karma_time,
				'karma_comment'		=> $karma_comment,
			);
			$this->db->sql_query(
				'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $sql_ary)
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
				UPDATE ' . $this->table_name . '
				SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE item_id = ' . (int) $item_id . '
					AND karma_type_id = ' . (int) $karma_type_id . '
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
	 * Retrieves the url, title and author user_id of the specified item
	 * 
	 * @param	string	$karma_type_name	The type of the item
	 * @param	int		$item_id			The ID of the item
	 * @return	array						An array containing the keys 'url', 'title' and 'author' pointing to the corresponding information
	 */
	public function get_item_data($karma_type_name, $item_id)
	{
		$karma_type = $this->get_type_class($karma_type_name);

		return array(
			'url'		=> $karma_type->get_url($item_id),
			'title'		=> $karma_type->get_title($item_id),
			'author'	=> $karma_type->get_author($item_id),
		);
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
	 * Returns the ID of the user who wrote the specified post
	 * 
	 * @param	int	$post_id	The ID of the post of which the author is
	 * 							requested
	 * @return	int				The ID of the user who wrote post $post_id,
	 * 							or false if the post doesn't exist
	 */
	private function get_author_of_post($post_id)
	{
		$sql_array = array(
			'SELECT'	=> 'poster_id',
			'FROM'		=> array(POSTS_TABLE => 'p'),
			'WHERE'		=> 'post_id = ' . (int) $post_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row === false)
		{
			return false;
		}
		return $row['poster_id'];
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
	 * @param	int		$item_id		The item on which the karma was given
	 * @param	int		$karma_type_id	The type of the item on which the karma was given
	 * @param	int		$giving_user_id	The user which gave the karma
	 * @return	int						The given karma, or 0 if no karma was found
	 */
	private function get_karma_score($item_id, $karma_type_id, $giving_user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'karma_score',
			'FROM'		=> array($this->table_name => 'k'),
			'WHERE'		=> 'item_id = ' . (int) $item_id . '
							AND karma_type_id = ' . (int) $karma_type_id . '
							AND giving_user_id = ' . (int) $giving_user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$karma_score = $this->db->sql_fetchfield('karma_score');
		$this->db->sql_freeresult($result);

		return (int) $karma_score;
	}
}
