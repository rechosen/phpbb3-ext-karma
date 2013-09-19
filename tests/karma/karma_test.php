<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

// Include these files to make truncate_string() work in includes/manager.php
require_once(dirname(__FILE__) . '/../../../../../includes/utf/utf_tools.php');
require_once(dirname(__FILE__) . '/../../../../../includes/functions_content.php');

class phpbb_ext_phpbb_karma_tests_karma_karma_test extends phpbb_ext_phpbb_karma_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/karma.xml');
	}

	protected $karma_manager;

	public function setUp()
	{
		global $phpbb_root_path, $phpEx;

		parent::setUp();

		$this->config = new phpbb_config(array());
		$this->cache = new phpbb_cache_service(
			new phpbb_cache_driver_null(),
			$this->config,
			$this->db,
			$phpbb_root_path,
			$phpEx
		);
		$this->container = new phpbb_mock_container_builder();
		$this->user = new phpbb_user();

		$this->phpbb_filesystem = new phpbb_filesystem(
			new phpbb_symfony_request(
				new phpbb_mock_request()
			),
			$phpbb_root_path,
			$phpEx
		);
		$this->template = new phpbb_template_twig($this->phpbb_filesystem, $this->config, $this->user, new phpbb_template_context());
		$this->helper = new phpbb_controller_helper($this->template, $this->user, $this->config, '', 'php');

		$this->karma_manager = new phpbb_ext_phpbb_karma_includes_manager(
			array('karma.type.post' => array()),
			$this->cache,
			$this->container,
			$this->db,
			$this->helper,
			$this->user,
			$phpbb_root_path,
			$phpEx,
			'phpbb_karma',
			'phpbb_karma_types'
		);

		$this->container->set(
			'karma.type.post',
			new phpbb_ext_phpbb_karma_includes_type_post(
				new phpbb_mock_karma_auth(), $this->db, $this->user, $phpbb_root_path, $phpEx, 'phpbb_karma'
			)
		);
	}

	public function create_data()
	{
		// Basic test (should succeed)
		$basic_test = array(
			'item_id'			=> 1,
			'karma_type_name'	=> 'post',
			'giving_user_id'	=> 1,
			'karma_score'		=> 1,
			'karma_time'		=> time(),
			'karma_comment'		=> '',
		);

		// Big values (should succeed)
		$big_number = 1000000;
		$big_string = str_repeat('a', pow(2, 16) + 100);
		$big_values_test = array(
			'item_id'			=> $big_number,
			'karma_type_name'	=> 'post',
			'giving_user_id'	=> $big_number,
			'karma_score'		=> -128,
			'karma_comment'		=> $big_string,
			'karma_time'		=> pow(2, 32) - 1,
		);
		
		// Missing values (should succeed as the missing values are optional)
		$missing_values_test = array(
			'item_id'			=> 1,
			'karma_type_name'	=> 'post',
			'giving_user_id'	=> 1,
			'karma_score'		=> 1,
		);

		// Bogus karma type name (Exception expected)
		$bogus_karma_type_name_test = $basic_test;
		$bogus_karma_type_name_test['karma_type_name'] = 'bogus_karma_type';

		// Illegal values (OutOfBoundsException expected)
		// These are all tried individually, with the basic test as a template
		$too_large_int = pow(2, 32);
		$illegal_values = array(
			'item_id'			=> array(-1, $too_large_int),
			'giving_user_id'	=> array(-1, $too_large_int),
			'karma_score'		=> array(-129, 128),
			'karma_time'		=> array($too_large_int),
		);

		// Combine the above test values into an array of data
		$return = array(
			array($basic_test, ''),
			array($big_values_test, ''),
			array($missing_values_test, ''),
		);
		foreach ($illegal_values as $field => $values)
		{
			$template = $basic_test;
			foreach ($values as $value)
			{
				$template[$field] = $value;
				$return[] = array($template, 'OutOfBoundsException');
			}
		}
		return $return;
	}

	/**
	 * @dataProvider create_data
	 */
	public function test_store_karma($karma, $expected_exception)
	{
		if (!empty($expected_exception))
		{
			$this->setExpectedException($expected_exception);
		}

		if (!isset($karma['karma_time']))
		{
			if (!isset($karma['karma_comment']))
			{
				$this->karma_manager->store_karma($karma['karma_type_name'], $karma['item_id'], $karma['giving_user_id'], $karma['karma_score']);
			}
			else
			{
				$this->karma_manager->store_karma($karma['karma_type_name'], $karma['item_id'], $karma['giving_user_id'], $karma['karma_score'], $karma['karma_comment']);
			}
		}
		else
		{
			$this->karma_manager->store_karma($karma['karma_type_name'], $karma['item_id'], $karma['giving_user_id'], $karma['karma_score'], $karma['karma_comment'], $karma['karma_time']);
		}

		if (empty($expected_exception))
		{
			$this->assert_karma_row_exists($karma);
			$this->assert_user_karma_score_equals($karma['item_id'], $karma['karma_score']);
		}
	}
	
	public function test_update_karma()
	{
		$time = time();
		$this->karma_manager->store_karma('post', 1, 1, 1, '', $time);
		$time++;
		$this->karma_manager->store_karma('post', 1, 1, -1, 'abc', $time);
		$this->assert_karma_row_exists(array(
			'item_id'			=> 1,
			'karma_type_id'		=> $this->get_karma_type_id('post'),
			'giving_user_id'	=> 1,
			'karma_score'		=> -1,
			'karma_time'		=> $time,
			'karma_comment'		=> 'abc',
		));
		$this->assert_user_karma_score_equals(1, -1);
	}
	
	protected function assert_karma_row_exists($row)
	{
		$sql_ary = $row;
		unset($sql_ary['karma_comment']); // Need to match this with LIKE due to string truncation
		unset($sql_ary['karma_type_name']); // Need to convert this to an ID

		$sql = 'SELECT COUNT(*) AS num_rows FROM phpbb_karma WHERE ' . $this->db->sql_build_array('SELECT', $sql_ary);
		$row['karma_comment'] = $this->db->sql_escape($row['karma_comment']);
		$sql .= " AND '{$row['karma_comment']}' LIKE CONCAT(karma_comment, '%')";
		if (!isset($row['karma_type_id']))
		{
			$sql .= ' AND karma_type_id = ' . $this->get_karma_type_id($row['karma_type_name']);
		}
		$result = $this->db->sql_query($sql);
		$this->assertEquals(1, $this->db->sql_fetchfield('num_rows'));
		$this->db->sql_freeresult($result);
	}
	
	protected function assert_user_karma_score_equals($post_id, $karma_score)
	{
		$result = $this->db->sql_query("
			SELECT u.user_karma_score
			FROM phpbb_posts AS p, phpbb_users AS u
			WHERE p.poster_id = u.user_id
				AND p.post_id = $post_id"
		);
		$this->assertEquals($karma_score, $this->db->sql_fetchfield('user_karma_score'));
		$this->db->sql_freeresult($result);
	}

	protected function get_karma_type_id($karma_type_name)
	{
		$result = $this->db->sql_query('
			SELECT karma_type_id
			FROM phpbb_karma_types
			WHERE karma_type_name = \'' . $this->db->sql_escape($karma_type_name) . '\''
		);
		$karma_type_id = $this->db->sql_fetchfield('karma_type_id');
		$this->db->sql_freeresult($result);
		return $karma_type_id;
	}
}
