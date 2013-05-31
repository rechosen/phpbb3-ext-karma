<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_gallery_tests_base_test extends phpbb_ext_gallery_test_case
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
