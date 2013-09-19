<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 phpBB
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_ext_phpbb_karma_migrations_0_0_1 extends phpbb_db_migration
{
	public function effectively_installed()
	{
		$sql = 'SELECT config_value
				FROM ' . $this->table_prefix . "config
				WHERE config_name = 'phpbb_karma_version'";
		$result = $this->db->sql_query($sql);
		$version = $this->db->sql_fetchfield('config_value');
		$this->db->sql_freeresult($result);

		return $version && (version_compare($version, '0.0.1') >= 0);
	}

	static public function depends_on()
	{
		return array('phpbb_db_migration_data_310_dev');
	}

	public function update_schema()
	{
		$ret = array(
			'add_tables'	=> array(
				$this->table_prefix . 'karma'			=> array(
					'COLUMNS'		=> array(
						'karma_id'					=> array('UINT', NULL, 'auto_increment'),
						'karma_type_id'				=> array('UINT', 0),
						'item_id'					=> array('UINT', 0),
						'giving_user_id'			=> array('UINT', 0),
						'receiving_user_id'			=> array('UINT', 0),
						'karma_score'				=> array('TINT:4', 0),
						'karma_time'				=> array('TIMESTAMP', 0),
						'karma_comment'				=> array('TEXT_UNI', ''),
						'karma_reported'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'karma_id',
					// TODO add indexes to speed up SELECT queries
				),
				$this->table_prefix . 'karma_types'		=> array(
					'COLUMNS'		=> array(
						'karma_type_id'				=> array('UINT', NULL, 'auto_increment'),
						'karma_type_name'			=> array('VCHAR:255', ''),
						'karma_type_enabled'		=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'karma_type_id',
				),
				$this->table_prefix . 'karma_reports'	=> array(
					'COLUMNS'		=> array(
						'karma_report_id'			=> array('UINT', NULL, 'auto_increment'),
						'karma_id'					=> array('UINT', 0),
						'reporter_id'				=> array('UINT', 0),
						'karma_report_closed'		=> array('BOOL', 0),
						'karma_report_time'			=> array('TIMESTAMP', 0),
						'karma_report_text'			=> array('TEXT_UNI', ''),
						'reported_karma_score'		=> array('TINT:4', 0),
						'reported_karma_time'		=> array('TIMESTAMP', 0),
						'reported_karma_comment'	=> array('TEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'karma_report_id',
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_karma_score'	=> array('INT:11', 0),
				),
			),
		);
		return $ret;
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_karma_score',
				),
			),
			'drop_tables'	=> array(
				$this->table_prefix . 'karma',
				$this->table_prefix . 'karma_types',
				$this->table_prefix . 'karma_reports',
			),
		);
	}

	public function update_data()
	{
		return array(
			// Config values
			array('config.add', array('phpbb_karma_version', '0.0.1')),

			// Permissions
			// Note that the boolean parameter indicates if the permission is global,
			// while the last parameter indicates which permission to copy settings from.
			array('permission.add', array('m_karma_report', true)),
			array('permission.add', array('m_karma_edit', true)),
			array('permission.add', array('m_karma_delete', true)),
			// Copy only the global permission settings for the moderator permissions
			array('custom', array(array($this, 'copy_karma_moderator_permissions'))),
			// Need to run a permission.add with a copy_from argument now
			// because it calls acl_clear_prefetch(), and copy_karma_moderator_permissions() doesn't
			array('permission.add', array('u_givekarma', true, 'u_sendpm')),
			array('permission.add', array('u_karma_edit', true, 'u_pm_edit')),
			array('permission.add', array('u_karma_delete', true, 'u_pm_delete')),

			// UCP module
			array('module.add', array('ucp', '', 'UCP_KARMA')),
			array('module.add', array('ucp', 'UCP_KARMA', array(
				'module_basename'	=> 'phpbb_ext_phpbb_karma_ucp_received_karma',
				'module_langname'	=> 'UCP_RECEIVED_KARMA',
				'module_mode'		=> 'overview',
				'module_auth'		=> '',
			))),

			// MCP module
			array('module.add', array('mcp', '', 'MCP_KARMA')),
			array('module.add', array('mcp', 'MCP_KARMA', array(
				'module_basename'	=> 'phpbb_ext_phpbb_karma_mcp_reported_karma',
				'module_langname'	=> 'MCP_KARMA_REPORTS_OPEN',
				'module_mode'		=> 'reports',
				'module_auth'		=> 'acl_m_karma_report',
			))),
			array('module.add', array('mcp', 'MCP_KARMA', array(
				'module_basename'	=> 'phpbb_ext_phpbb_karma_mcp_reported_karma',
				'module_langname'	=> 'MCP_KARMA_REPORTS_CLOSED',
				'module_mode'		=> 'reports_closed',
				'module_auth'		=> 'acl_m_karma_report',
			))),
			array('module.add', array('mcp', 'MCP_KARMA', array(
				'module_basename'	=> 'phpbb_ext_phpbb_karma_mcp_reported_karma',
				'module_langname'	=> 'MCP_KARMA_REPORT_DETAILS',
				'module_mode'		=> 'report_details',
				'module_auth'		=> 'acl_m_karma_report',
			))),
		);
	}

	public function copy_karma_moderator_permissions()
	{
		// TODO I promised nickvergessen to make the normal permission copy function smart about copying only global/local parts, but would rather do that after namespaces are merged :)
		// Which permissions map to which?
		$copy_from_to = array(
			'm_report'	=> 'm_karma_report',
			'm_edit'	=> 'm_karma_edit',
			'm_delete'	=> 'm_karma_delete',
		);

		// Get the ids of the source and target permission options
		$auth_options = array_merge(array_keys($copy_from_to), array_values($copy_from_to));
		$sql = 'SELECT auth_option, auth_option_id
				FROM ' . ACL_OPTIONS_TABLE . '
				WHERE ' . $this->db->sql_in_set('auth_option', $auth_options);
		$result = $this->db->sql_query($sql);
		$permission_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$permission_ids[$row['auth_option']] = $row['auth_option_id'];
		}
		$this->db->sql_freeresult($result);
		foreach ($auth_options as $auth_option)
		{
			if (!isset($permission_ids[$auth_option]))
			{
				throw new phpbb_db_migration_exception("Failed to get the id of the $auth_option permission!");
			}
		}

		// The tables to copy permission settings in
		$tables = array(ACL_GROUPS_TABLE, ACL_ROLES_DATA_TABLE, ACL_USERS_TABLE);

		foreach ($copy_from_to as $from => $to)
		{
			$old_id = $permission_ids[$from];
			$new_id = $permission_ids[$to];

			// Check if there already are entries for the $to permission
			foreach ($tables as $table)
			{
				$sql = 'SELECT COUNT(auth_option_id) as entries
					FROM ' . $table . '
					WHERE auth_option_id = ' . $new_id;
				$result = $this->db->sql_query($sql);
				$entries = $this->db->sql_fetchfield('entries');
				$this->db->sql_freeresult($result);

				if ($entries > 0)
				{
					// Do not copy if there already are entries
					continue 2; // TODO Can I use this or is this frowned upon?
				}
			}

			// Now copy only the global permission data
			// Based on phpbb_db_migration_tool_permission::add()
			foreach ($tables as $table)
			{
				$sql = 'SELECT *
					FROM ' . $table . '
					WHERE auth_option_id = ' . $old_id;
				if ($table === ACL_USERS_TABLE)
				{
					// Prevent local permissions from being copied
					$sql .= ' AND forum_id = 0';
				}
				$result = $this->db->sql_query($sql);

				$sql_ary = array();
				while ($row = $this->db->sql_fetchrow($result))
				{
					$row['auth_option_id'] = $new_id;
					$sql_ary[] = $row;
				}
				$this->db->sql_freeresult($result);

				if (!empty($sql_ary))
				{
					$this->db->sql_multi_insert($table, $sql_ary);
				}
			}
		}
	}
}
