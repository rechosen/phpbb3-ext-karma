<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbb\karma\includes\type;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class type_base
{
	/**
	* Authentication object
	* @var \phpbb\auth\auth
	*/
	protected $auth;

	/**
	* Database object
	* @var \phpbb\db\driver\driver
	*/
	protected $db;

	/**
	* User object
	* @var \phpbb\user
	*/
	protected $user;

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
	* @param \phpbb\auth\auth			$auth				Authentication object
	* @param \phpbb\db\driver\driver	$db					Database object
	* @param \phpbb\user			$user				User object
	* @param string				$phpbb_root_path	phpBB root path
	* @param string				$php_ext			php file extension
	* @param string				$table_name			Name of the karma database table
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\db\driver\driver $db, \phpbb\user $user, $phpbb_root_path, $php_ext, $table_name)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_name = $table_name;
	}
}
