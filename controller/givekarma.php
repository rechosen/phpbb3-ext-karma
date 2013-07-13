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
	* @param phpbb_auth					$auth				Auth object
	* @param ContainerBuilder			$container			Container object
	* @param phpbb_db_driver			$db					Database object
	* @param phpbb_request				$request			Request object
	* @param phpbb_template				$template			Template object
	* @param phpbb_user					$user				User object
	* @param phpbb_controller_helper	$helper				Controller helper object
	* @param string						$phpbb_root_path	phpBB root path
	* @param string						$php_ext			phpEx
	*/
	public function __construct(phpbb_auth $auth, /* TODO ask about impossibility of checking type */$container, phpbb_db_driver $db, phpbb_request $request, phpbb_template $template, phpbb_user $user, phpbb_controller_helper $helper, $phpbb_root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($post_id)
	{
		// Load the extension's language file and the posting language file
		$this->user->add_lang_ext('phpbb/karma', 'karma');
		$this->user->add_lang(array('posting'));
		
		// Retrieve info about the post karma is given on
		$post_data = $this->get_post_data($post_id);
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

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('give_karma_form'))
			{
				trigger_error('FORM_INVALID');
			}

			// Validate the input TODO make karma_comment required depending on a setting
			$karma_score = $this->request->variable('karma_type', 0);
			if ($karma_score == 0)
			{
				$error[] = 'KARMA_TYPE_INVALID';
			}

			// Store the karma into the database
			if (empty($error))
			{
				$karma_model = $this->container->get('karma.includes.karma_model');
				try
				{
					$karma_model->store_karma($post_id, $this->user->data['user_id'], $karma_score, $this->request->variable('karma_comment', ''));
					// TODO Check for existing karma and overwrite or fail.

					// Show the success page and redirect after three seconds
					meta_refresh(3, $post_data['post_url']);
					$message = $this->user->lang['KARMA_KARMA_GIVEN'] . '<br /><br />' . sprintf($user->lang['VIEW_MESSAGE'], '<a href="' . $post_data['post_url'] . '">', '</a>');
					trigger_error($message);
				}
				catch (Exception $e)
				{
					trigger_error($e->getMessage());
				}
			}

			// Replace "error" strings with their real, localised form
			$error = array_map(array($this->user, 'lang'), $error);
		}

		// Set the template variables
		$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
		$post_link = "<a href=\"{$post_data['post_url']}\">{$post_data['post_subject']}</a>";
		$receiving_user = get_username_string('full', $post_data['poster_id'], $post_data['username'], $post_data['user_colour']);
		$template_vars = array(
			'ERROR'					=> (!empty($error)) ? implode('<br />', $error) : '',
			'KARMA_GIVING_KARMA'	=> sprintf($this->user->lang['KARMA_GIVING_KARMA'], $post_link, $receiving_user),
			'KARMA_COMMENT'			=> $this->request->variable('karma_comment', ''),
			'KARMA_TYPE'			=> $this->request->variable('karma_type', 0),
		);
		$this->template->assign_vars($template_vars);

		// Set a form key
		add_form_key('give_karma_form');

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

	/**
	 * Gets information this controller requires about a post
	 * 
	 * @param	int	$post_id	The ID of the post
	 * @return	mixed			An array of information, or false if the post doesn't exist.
	 */
	private function get_post_data($post_id)
	{
		$sql_array = array(
			'SELECT'	=> 'p.topic_id, p.forum_id, p.post_subject, p.poster_id, u.username, u.user_colour',
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

		if (is_array($post_data))
		{
			$post_data['post_url'] = $this->get_post_url($post_id, $post_data['topic_id']);
		}

		return $post_data;
	}

	/**
	 * Generates a url for the specified post
	 * 
	 * @param	int		$post_id	The ID of the post
	 * @param	int		$topic_id	The ID of the topic the post is in
	 * @return	string				The url of the specified post
	 */
	private function get_post_url($post_id, $topic_id)
	{
		return append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, "t=$topic_id&amp;p=$post_id") . "#p$post_id";
	}
}
