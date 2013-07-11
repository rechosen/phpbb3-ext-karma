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

class phpbb_ext_phpbb_karma_model_karma
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
	public function store_karma($karma_row)
	{
		// Get the optional fields if they're set, otherwise use the default
		if (isset($karma_row['receiving_user_id']))
		{
			$receiving_user_id = $karma_row['receiving_user_id'];
		}
		else
		{
			$receiving_user_id = $this->get_author_of_post($karma_row['post_id']);
			if ($receiving_user_id === false)
			{
				throw new OutOfBoundsException('NO_POST');
			}
		}
		if (isset($karma_row['karma_time']))
		{
			$karma_time = $karma_row['karma_time'];
		}
		else
		{
			$karma_time = time();
		}
		if (isset($karma_row['karma_comment']))
		{
			$karma_comment = $karma_row['karma_comment'];
		}
		else
		{
			$karma_comment = '';
		}

		// Insert the karma into the database
		$sql_ary = array(
			'post_id'			=> $karma_row['post_id'],
			'giving_user_id'	=> $karma_row['giving_user_id'],
			'receiving_user_id'	=> $receiving_user_id,
			'karma_score'		=> $karma_row['karma_score'],
			'karma_time'		=> $karma_time,
			'karma_comment'		=> $karma_comment,
		);
		$this->db->sql_query('INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $sql_ary)); // TODO 80 characters wide?
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
}
