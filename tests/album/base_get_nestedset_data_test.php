<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/nestedset_base.php';

class phpbb_ext_gallery_tests_album_base_get_nestedset_data_test extends phpbb_ext_gallery_tests_album_nestedset_base
{
	public function get_path_and_subtree_data_data()
	{
		return array(
			array(1, true, true, array(1, 2, 3)),
			array(1, true, false, array(2, 3)),
			array(1, false, true, array(3, 2, 1)),
			array(1, false, false, array(3, 2)),

			array(2, true, true, array(1, 2)),
			array(2, true, false, array(1)),
			array(2, false, true, array(2, 1)),
			array(2, false, false, array(1)),

			array(5, true, true, array(4, 5, 6)),
			array(5, true, false, array(4, 6)),
			array(5, false, true, array(6, 5, 4)),
			array(5, false, false, array(6, 4)),
		);
	}

	/**
	* @dataProvider get_path_and_subtree_data_data
	*/
	public function test_get_path_and_subtree_data($album_id, $order_asc, $include_item, $expected)
	{
		$album = $this->factory->get($album_id);
		$this->assertEquals($expected, array_keys($album->get_path_and_subtree_data($order_asc, $include_item)));
	}

	public function get_path_data_data()
	{
		return array(
			array(1, true, true, array(1)),
			array(1, true, false, array()),
			array(1, false, true, array(1)),
			array(1, false, false, array()),

			array(2, true, true, array(1, 2)),
			array(2, true, false, array(1)),
			array(2, false, true, array(2, 1)),
			array(2, false, false, array(1)),

			array(5, true, true, array(4, 5)),
			array(5, true, false, array(4)),
			array(5, false, true, array(5, 4)),
			array(5, false, false, array(4)),
		);
	}

	/**
	* @dataProvider get_path_data_data
	*/
	public function test_get_path_data($album_id, $order_asc, $include_item, $expected)
	{
		$album = $this->factory->get($album_id);
		$this->assertEquals($expected, array_keys($album->get_path_data($order_asc, $include_item)));
	}

	public function get_subtree_data_data()
	{
		return array(
			array(1, true, true, array(1, 2, 3)),
			array(1, true, false, array(2, 3)),
			array(1, false, true, array(3, 2, 1)),
			array(1, false, false, array(3, 2)),

			array(2, true, true, array(2)),
			array(2, true, false, array()),
			array(2, false, true, array(2)),
			array(2, false, false, array()),

			array(5, true, true, array(5, 6)),
			array(5, true, false, array(6)),
			array(5, false, true, array(6, 5)),
			array(5, false, false, array(6)),
		);
	}

	/**
	* @dataProvider get_subtree_data_data
	*/
	public function test_get_subtree_data($album_id, $order_asc, $include_item, $expected)
	{
		$album = $this->factory->get($album_id);
		$this->assertEquals($expected, array_keys($album->get_subtree_data($order_asc, $include_item)));
	}

	public function get_path_basic_data_data()
	{
		return array(
			array(1, '', array()),
			array(1, serialize(array()), array()),
			array(2, '', array(1)),
			array(2, serialize(array(1 => array())), array(1)),
			array(10, '', array(7, 9)),
			array(10, serialize(array(7 => array(), 9 => array())), array(7, 9)),
		);
	}

	/**
	* @dataProvider get_path_basic_data_data
	*/
	public function test_get_path_basic_data($album_id, $album_parents, $expected)
	{
		$album = $this->factory->get($album_id);
		$album_data = $this->album_data[$album_id];
		$album_data['album_parents'] = $album_parents;
		$album->set_datarow($album_data);
		$this->assertEquals($expected, array_keys($album->get_path_basic_data($album_data)));
	}
}
