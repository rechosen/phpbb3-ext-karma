<?php
/**
*
* @package phpBB Karma Testing
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

// Include these files to make truncate_string() work in includes/karma_model.php
require_once(dirname(__FILE__) . '/../../../../../includes/utf/utf_tools.php');
require_once(dirname(__FILE__) . '/../../../../../includes/functions_content.php');

class phpbb_ext_phpbb_karma_tests_karma_karma_test extends phpbb_ext_phpbb_karma_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/karma.xml');
	}

	protected $karma_model;

	public function setUp()
	{
		global $phpbb_root_path, $phpEx;

		parent::setUp();

		$this->karma_model = new phpbb_ext_phpbb_karma_includes_karma_model(
			$this->db, 'phpbb_karma'
		);
	}

	public function create_data()
	{
		// Basic test (should succeed)
		$basic_test = array(
			'post_id'			=> 1,
			'giving_user_id'	=> 1,
			'karma_score'		=> 1,
			'karma_time'		=> time(),
			'karma_comment'		=> '',
		);

		// Big values (should succeed)
		$big_number = 1000000;
		$big_string = str_repeat('a', pow(2, 16) + 100);
		$big_values_test = array(
			'post_id'			=> $big_number,
			'giving_user_id'	=> $big_number,
			'karma_score'		=> -128,
			'karma_time'		=> pow(2, 32) - 1,
			'karma_comment'		=> $big_string,
		);
		
		// Missing values (should succeed as the missing values are optional)
		$missing_values_test = array(
			'post_id'			=> 1,
			'giving_user_id'	=> 1,
			'karma_score'		=> 1,
		);

		// Illegal values (OutOfBoundsException expected)
		// These are all tried individually, with the basic test as a template
		$too_large_int = pow(2, 32);
		$illegal_values = array(
			'post_id'			=> array(-1, $too_large_int),
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
				$this->karma_model->store_karma($karma['post_id'], $karma['giving_user_id'], $karma['karma_score']);
			}
			else
			{
				$this->karma_model->store_karma($karma['post_id'], $karma['giving_user_id'], $karma['karma_score'], $karma['karma_comment']);
			}
		}
		else
		{
			$this->karma_model->store_karma($karma['post_id'], $karma['giving_user_id'], $karma['karma_score'], $karma['karma_comment'], $karma['karma_time']);
		}

		if (empty($expected_exception))
		{
			$this->assert_karma_row_exists($karma);
			$this->assert_user_karma_score_equals($karma['post_id'], $karma['karma_score']);
		}
	}
	
	public function test_update_karma()
	{
		$time = time();
		$this->karma_model->store_karma(1, 1, 1, '', $time);
		$time++;
		$this->karma_model->store_karma(1, 1, -1, 'abc', $time);
		$this->assert_karma_row_exists(array(
			'post_id'			=> 1,
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
		$sql = 'SELECT COUNT(*) AS num_rows FROM phpbb_karma WHERE ' . $this->db->sql_build_array('SELECT', $sql_ary);
		$row['karma_comment'] = $this->db->sql_escape($row['karma_comment']);
		$sql .= " AND '{$row['karma_comment']}' LIKE CONCAT(karma_comment, '%')";
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
}
