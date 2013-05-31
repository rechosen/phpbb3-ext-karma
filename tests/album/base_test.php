<?php
/**
*
* @package phpBB Gallery Testing
* @copyright (c) 2013 nickvergessen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_gallery_tests_album_base_test extends phpbb_ext_gallery_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/albums.xml');
	}

	protected $album;

	public function setUp()
	{
		parent::setUp();

		$config = new phpbb_config(array('phpbb_gallery_album_lock' => 0));
		set_config(null, null, null, $config);
		$lock = new phpbb_lock_db('phpbb_gallery_album_lock', $config, $this->db);
		$this->nestedset = new phpbb_ext_gallery_core_album_nestedset($this->db, $lock, 'phpbb_gallery_albums');
		$this->nestedset->set_user_id(0);
		$this->album = new phpbb_mock_gallery_core_album_type_base($this->db, $this->nestedset, 'phpbb_gallery_albums');
	}

	public function get_data()
	{
		return array(
			array(array('album_id' => 1), 'id', 1),
			array(array('album_id' => 1), 'album_id', 1),
			array(array('album_name' => 'foobar'), 'album_name', 'foobar'),
		);
	}

	/**
	* @dataProvider get_data
	*/
	public function test_get($set_row, $key, $expected)
	{
		$this->album->set_datarow($set_row);
		$this->assertEquals($expected, $this->album->get($key));
	}

	public function set_data()
	{
		return array(
			array('album_name', '', true),
			array('album_name', '0', true),
			array('album_name', 'foobar', true),
		);
	}

	/**
	* @dataProvider set_data
	*/
	public function test_set($key, $value, $expected)
	{
		// Value was changed
		$this->assertEquals($expected, $this->album->set($key, $value));
		$this->assertEquals($value, $this->album->get($key));

		// Value was not changed
		$this->assertFalse($this->album->set($key, $value));
	}

	public function set_throws_data()
	{
		return array(
			array('id', 2),
			array('album_id', 2),
		);
	}

	/**
	* @dataProvider set_throws_data
	*
	* @expectedException			phpbb_ext_gallery_core_exception
	* @expectedExceptionMessage		GALLERY_ALBUM_CANNOT_SET_VALUE
	*/
	public function test_set_throws($key, $value)
	{
		$this->album->set($key, $value);
	}

	public function set_values_data()
	{
		return array(
			array(array('album_name' => '', 'album_unchanged' => ''), $expected),
		);
	}

	/**
	* @dataProvider set_values_data
	*/
	public function test_set_values($values, $expected)
	{
		$this->album->set_datarow(array(
			'album_name'	=> '',
			'album_unchanged'	=> '',
		));

		// Values were changed
		$this->assertEquals($expected, $this->album->set_values($values));

		foreach ($values as $key => $value)
		{
			$this->assertEquals($value, $this->album->get($key));
		}

		// Values were not changed
		$this->assertFalse($this->album->set_values($values));
	}

	public function set_values_throws_data()
	{
		return array(
			array(array('id' => 1)),
			array(array('album_id' => 1)),

			array(array('id' => 1, 'album_id' => 1)),
			array(array('album_id' => 1, 'id' => 1)),

			array(array('album_name' => 'foobar', 'album_id' => 1)),
			array(array('album_id' => 1, 'album_name' => 'foobar')),

			array(array('album_name' => 'foobar', 'id' => 1)),
			array(array('id' => 1, 'album_name' => 'foobar')),
		);
	}

	/**
	* @dataProvider set_values_throws_data
	*
	* @expectedException			phpbb_ext_gallery_core_exception
	* @expectedExceptionMessage		GALLERY_ALBUM_CANNOT_SET_VALUE
	*/
	public function test_set_values_throws($values)
	{
		 $this->album->set_values($values);
	}

	public function submit_data()
	{
		return array(
			array(array('album_id' => 1, 'album_name' => 'category'), array('album_name' => 'foorbar'), true, 1),
			array(array('album_id' => 1, 'album_name' => 'category'), array('album_name' => 'category'), false, 1),
			array(array(), array('album_name' => 'foorbar'), true, 3),
			array(array('album_name' => 'category'), array('album_name' => 'foorbar'), true, 3),
		);
	}

	/**
	* @dataProvider submit_data
	*/
	public function test_submit($current_data, $new_data, $expected_updated, $expected_id)
	{
		$this->album->set_datarow($current_data);

		// Values were changed
		$this->assertEquals($expected_updated, $this->album->set_values($new_data));
		$this->assertEquals($expected_updated, $this->album->submit());
		$this->assertFalse($this->album->set_values($new_data));
		$this->assertFalse($this->album->submit());
	}
}
