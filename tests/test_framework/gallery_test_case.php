<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

abstract class phpbb_ext_gallery_test_case extends phpbb_test_case
{
	public function get_test_case_helpers()
	{
		if (!$this->test_case_helpers)
		{
			$this->test_case_helpers = new phpbb_ext_gallery_test_case_helpers($this);
		}

		return $this->test_case_helpers;
	}
}
