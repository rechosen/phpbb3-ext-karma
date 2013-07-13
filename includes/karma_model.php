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

class phpbb_ext_phpbb_karma_includes_karma_model
{
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
	 * @param	phpbb_db_driver	$db			Database Object
	 * @param	string			$table_name	Name of the karma database table
	 */
	public function __construct(phpbb_db_driver $db, $table_name)
	{
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
	public function store_karma($post_id, $giving_user_id, $karma_score, $karma_comment = '', $karma_time = -1)
	{
		// Set the receiving user ID and check if post_id is valid at the same time
		$receiving_user_id = $this->get_author_of_post($post_id);
		if ($receiving_user_id === false)
		{
			throw new OutOfBoundsException('NO_POST');
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
		if (!$this->karma_exists($post_id, $giving_user_id))
		{
			$sql_ary = array(
				'post_id'			=> (int) $post_id,
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
				WHERE post_id = ' . (int) $post_id . '
					AND giving_user_id = ' . (int) $giving_user_id
			);
		}
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
	 * @return	boolean				true if the user exists, false otherwise
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
	 * Checks if the karma identified by post_id and giving_user_id exists
	 * 
	 * @param	int		$post_id		The post on which the karma was given
	 * @param	int		$giving_user_id	The user which gave the karma
	 * @return	boolean					true if the karma exists, false otherwise
	 */
	private function karma_exists($post_id, $giving_user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'count(*) AS num_karma',
			'FROM'		=> array($this->table_name => 'k'),
			'WHERE'		=> 'post_id = ' . (int) $post_id . '
							AND giving_user_id = ' . (int) $giving_user_id,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$num_karma = $this->db->sql_fetchfield('num_karma');
		$this->db->sql_freeresult($result);

		return $num_karma == 1;
	}
}
