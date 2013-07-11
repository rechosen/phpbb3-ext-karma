<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_tests_base_test extends phpbb_ext_phpbb_karma_test_case
{
	public function test_true()
	{
		$this->assertTrue(true);
	}

	public function test_false()
	{
		$this->assertFalse(false);
	}
}
