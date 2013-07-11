<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_tests_base_db_test extends phpbb_ext_phpbb_karma_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/config.xml');
	}

	public function test_database_connection()
	{
		$result = $this->db->sql_query('SELECT * FROM phpbb_config');
		$this->assertEquals($this->db->sql_fetchrowset($result), array());
	}
}
