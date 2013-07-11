<?php
/**
*
* @package phpBB Karma
* @copyright (c) 2013 rechosen
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class phpbb_ext_phpbb_karma_controller_givekarma
{
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_auth					$auth		Auth object
	* @param ContainerBuilder			$container	Container object
	* @param phpbb_db_driver			$db			Database object
	* @param phpbb_request				$request	Request object
	* @param phpbb_template				$template	Template object
	* @param phpbb_user					$user		User object
	* @param phpbb_controller_helper	$helper		Controller helper object
	* @param string						$php_ext	phpEx
	*/
	public function __construct(phpbb_auth $auth, /* TODO ask about impossibility of checking type */$container, phpbb_db_driver $db, phpbb_request $request, phpbb_template $template, phpbb_user $user, phpbb_controller_helper $helper, $php_ext)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->php_ext = $php_ext;
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($post_id)
	{
		// Load the extension's language file
		$this->user->add_lang_ext('phpbb/karma', 'karma');
		
		// Retrieve info about the post karma is given on
		$sql_array = array(
			'SELECT'	=> 'p.forum_id, p.post_subject, p.poster_id, u.username, u.user_colour',
			'FROM'		=> array(
				POSTS_TABLE		=> 'p',
				USERS_TABLE		=> 'u',
			),
			'WHERE'		=> "p.post_id = $post_id AND u.user_id = p.poster_id",
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$post_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// If $post_data === false, the post doesn't exist
		if ($post_data === false)
		{
			trigger_error('NO_POST');
		}

		// Does the user have permission to view this post?
		if (!$this->auth->acl_get('f_read', $post_data['forum_id']))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error('SORRY_AUTH_READ');
			}

			login_box('', $user->lang['LOGIN_VIEWFORUM']);
		}

		// Does the user have permission to give karma on this post? TODO
		/*if (!$this->auth->acl_get('u_give_karma') {
			trigger_error(TODO);
		}*/

		// Set the template variables TODO needs a form key
		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
		$post_url = append_sid("{$phpbb_root_path}viewtopic.{$this->php_ext}", "p=$post_id") . "#p$post_id";
		$post_link = "<a href=\"$post_url\">{$post_data['post_subject']}</a>";
		$receiving_user = get_username_string('full', $post_data['poster_id'], $post_data['username'], $post_data['user_colour']);
		$template_vars = array(
			'KARMA_GIVING_KARMA'	=> sprintf($this->user->lang['KARMA_GIVING_KARMA'], $post_link, $receiving_user),
		);
		$this->template->assign_vars($template_vars);

		/*
		* The render method takes up to three other arguments
		* @param string Name of the template file to display
		* Template files are searched for two places:
		* - phpBB/styles/<style_name>/template/
		* - phpBB/ext/<all_active_extensions>/styles/<style_name>/template/
		* @param string Page title
		* @param int Status code of the page (200 - OK [ default ], 403 - Unauthorized, 404 - Page not found)
		*/
		return $this->helper->render('karma/givekarma_body.html', 'Give karma', 200);
	}
}
