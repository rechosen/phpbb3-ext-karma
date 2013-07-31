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

class phpbb_ext_phpbb_karma_includes_type_base
{
	/**
	 * Database object
	 * @var phpbb_db_driver
	 */
	protected $db;

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
	protected $table_name;

	/**
	 * Constructor
	 * NOTE: The parameters of this method must match in order and type with
	 * the dependencies defined in the services.yml file for this service.
	 * 
	 * @param phpbb_db_driver	$db					Database Object
	 * @param string			$phpbb_root_path	phpBB root path
	 * @param string			$php_ext			php file extension
	 * @param string			$table_name			Name of the karma database table
	 */
	public function __construct(phpbb_db_driver $db, $phpbb_root_path, $php_ext, $table_name)
	{
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_name = $table_name;
	}
}
