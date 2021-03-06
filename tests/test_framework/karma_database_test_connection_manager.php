<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_database_test_connection_manager extends phpbb_database_test_connection_manager
{
	public function load_schema()
	{
		// Load the phpBB schema's
		parent::load_schema();

		$this->ensure_connected(__METHOD__);

		$directory = dirname(__FILE__) . '/../schemas/';
		$this->load_schema_from_file($directory);

	}
}
