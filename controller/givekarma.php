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

use Symfony\Component\DependencyInjection\ContainerBuilder;

class phpbb_ext_phpbb_karma_controller_givekarma
{
	/**
	 * Auth object
	 * @var phpbb_auth
	 */
	protected $auth;

	/**
	 * Container object
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * Request object
	 * @var phpbb_request
	 */
	protected $request;

	/**
	 * Template object
	 * @var phpbb_template
	 */
	protected $template;

	/**
	 * User object
	 * @var phpbb_user
	 */
	protected $user;

	/**
	 * Controller helper object
	 * @var phpbb_controller_helper
	 */
	protected $helper;

	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param phpbb_auth					$auth				Auth object
	* @param ContainerBuilder			$container			Container object
	* @param phpbb_request				$request			Request object
	* @param phpbb_template				$template			Template object
	* @param phpbb_user					$user				User object
	* @param phpbb_controller_helper	$helper				Controller helper object
	*/
	public function __construct(phpbb_auth $auth, ContainerBuilder $container, phpbb_request $request, phpbb_template $template, phpbb_user $user, phpbb_controller_helper $helper)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($karma_type_name, $item_id)
	{
		// Load the extension's language file and the posting language file
		$this->user->add_lang_ext('phpbb/karma', 'karma');
		$this->user->add_lang(array('posting'));

		// Retrieve info about the item karma is given on
		$karma_manager = $this->container->get('karma.includes.manager');
		try
		{
			$item_data = $karma_manager->get_item_data($karma_type_name, $item_id);
		}
		catch (Exception $e)
		{
			trigger_error($e->getMessage());
		}

		// Determine the mode in which this controller is being user right now
		$giving_user_id = $this->request->variable('giver', $this->user->data['user_id']);
		$given_karma = $karma_manager->get_given_karma_row($karma_type_name, $item_id, $giving_user_id);
		if ($giving_user_id === $this->user->data['user_id'])
		{
			// This user is giving, editing or deleting karma on his/her own behalf
			if ($this->request->is_set('delete'))
			{
				// The user is trying to delete karma
				$mode = 'u_karma_delete';
			}
			else if ($given_karma === false)
			{
				// The user is trying to give new karma
				$mode = 'u_givekarma';
			}
			else
			{
				// The user is trying to edit his/her karma
				$mode = 'u_karma_edit';
			}
		}
		else
		{
			// This is a moderator editing or deleting reported karma
			if ($this->request->is_set('delete'))
			{
				// The moderator is trying to delete karma
				$mode = 'm_karma_delete';
			}
			else
			{
				// The moderator is trying to edit karma
				$mode = 'm_karma_edit';
			}
		}

		// Now check if the user is authorised to use the controller in this mode
		$this->check_permission($mode);

		// If we're in moderator mode, check if given karma exists and was reported
		if (strpos($mode, 'm_') === 0 && ($given_karma === false || !$given_karma['karma_reported']))
		{
			// No specific error messages to avoid yielding information about
			// existence of given karma to moderators
			trigger_error('NO_KARMA');
		}

		// If we're in user mode, check if the user isn't giving him/herself karma
		// TODO should I also prevent moderators from editing karma given to their own items? If I don't, a moderator gone bad might make all karma to him/herself positive
		if (strpos($mode, 'u_') === 0 && $giving_user_id == $item_data['author']['user_id'])
		{
			trigger_error('NO_SELF_KARMA');
		}

		// Set the necessary variables depending on the mode
		$submitted = false;
		switch ($mode)
		{
			case 'u_givekarma':
				$title = $this->user->lang['KARMA_GIVE_KARMA'];
				$giving_karma_text = $this->user->lang['KARMA_GIVING_KARMA'];
				$giving_user = ''; // Not used
				$karma_given_text = $this->user->lang['KARMA_SUCCESSFULLY_GIVEN'];
				$karma_score = 0;
				$karma_comment = '';
				$allow_delete = false;
			break;
			case 'u_karma_edit':
			case 'm_karma_edit':
				$title = $this->user->lang['KARMA_EDIT_KARMA'];
				$giving_karma_text = $this->user->lang['KARMA_EDITING_KARMA'];
				$giving_user = ($mode === 'm_karma_edit')
					? get_username_string('full', $giving_user_id, $given_karma['giving_username'], $given_karma['giving_user_colour'])
					: $this->user->lang['KARMA_EDITING_KARMA_YOU'];
				$karma_given_text = $this->user->lang['KARMA_SUCCESSFULLY_EDITED'];
				$karma_score = $given_karma['karma_score'];
				$karma_comment = $given_karma['karma_comment'];
				$allow_delete = $this->auth->acl_get(($mode === 'm_karma_edit') ? 'm_karma_delete' : 'u_karma_delete');
			break;
			case 'u_karma_delete':
			case 'm_karma_delete':
				$title = $this->user->lang['KARMA_DELETE_KARMA'];
				$giving_karma_text = ''; // Not used
				$giving_user = ''; // Not used
				$karma_given_text = $this->user->lang['KARMA_SUCCESSFULLY_DELETED'];
				$karma_score = 0;
				$karma_comment = '';
				// Use a confirm box
				if (confirm_box(true))
				{
					// The delete operation was confirmed, so carry it out
					$allow_delete = true;
					// Indicate a successful submit happened; the rest is done later by validate_and_store_karma()
					$submitted = true;
				}
				else
				{
					// No confirmation received, display the confirm_box
					$allow_delete = false; // Just to be sure that no karma gets deleted
					$s_hidden_fields = build_hidden_fields(array(
						'karma_score' => 0,
					));
					confirm_box(false, $this->user->lang['KARMA_DELETE_CONFIRM'], $s_hidden_fields);

					// If we arrive here, the confirm_box was cancelled
					// TODO Find a way to redirect moderators back to the right karma report
					redirect($item_data['url']);
				}
			break;
		}

		// If new or edited karma was submitted, check the form key
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('give_karma_form'))
			{
				trigger_error('FORM_INVALID');
			}

			// Indicate a successful submit happened
			$submitted = true;
		}

		if ($submitted)
		{
			// Validate the input and store the karma
			$store_result = $this->validate_and_store_karma($karma_type_name, $item_id, $giving_user_id, $allow_delete);
			if ($store_result['karma_deleted'])
			{
				$karma_given_text = $this->user->lang['KARMA_SUCCESSFULLY_DELETED'];
			}
			$error = $store_result['error'];

			if (empty($error))
			{
				// Show the success page and redirect after three seconds
				meta_refresh(3, $item_data['url']);
				$message = $karma_given_text . '<br /><br />' . sprintf($this->user->lang['KARMA_VIEW_ITEM'], "<a href=\"{$item_data['url']}\">", '</a>');
				trigger_error($message);
			}
		}

		// Set the template variables to display the form for giving or editing karma
		// In *_karma_delete mode, the confirm_box and success page make sure we never get here
		$item_link = "<a href=\"{$item_data['url']}\">{$item_data['title']}</a>";
		$receiving_user = get_username_string('full', $item_data['author']['user_id'], $item_data['author']['username'], $item_data['author']['user_colour']);
		// Check if there the request variables contain a karma_score that should overrule the default
		$karma_score = $this->request->variable('karma_score', (int) $karma_score);
		if ($mode === 'u_givekarma' && $karma_score === 0)
		{
			// Preset the karma score if the appropriate request variable is set
			$get_score = $this->request->variable('score', '');
			if ($get_score === 'positive')
			{
				$karma_score = 1;
			}
			if ($get_score === 'negative')
			{
				$karma_score = -1;
			}
		}
		$template_vars = array(
			'L_TITLE'				=> $title,
			'S_DELETE_KARMA'		=> $allow_delete,
			'ERROR'					=> (!empty($error)) ? implode('<br />', $error) : '',
			'KARMA_GIVING_KARMA'	=> sprintf($giving_karma_text, $giving_user, $item_link, $receiving_user),
			'KARMA_COMMENT'			=> $this->request->variable('karma_comment', $karma_comment),
			'KARMA_SCORE'			=> $karma_score,
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
		return $this->helper->render('givekarma_body.html', $title, 200);
	}

	/**
	 * Validates the POST variables and, if successful, stores the karma
	 * 
	 * @param	string	$karma_type_name	The name of the type of the item on which the karma is given
	 * @param	int		$item_id			The ID of the item on which karma is given
	 * @param	int		$giving_user_id		The ID of the user giving this karma
	 * @param	bool	$allow_delete		Whether the current user is allowed to delete this karma
	 * @return	array	An array with the following key-value pairs:
	 * 						'karma_deleted' => (bool) Whether the karma was deleted or not
	 * 						'error' => (array) An array of form errors to be displayed to the user
	 */
	private function validate_and_store_karma($karma_type_name, $item_id, $giving_user_id, $allow_delete)
	{
		$karma_deleted = false;
		$error = array();

		// Validate the input TODO make karma_comment required depending on a setting
		// Do _not_ use a default of 0 for the karma score, as any stray input then deletes the karma
		$karma_score = $this->request->variable('karma_score', '');
		if (((string) ((int) $karma_score)) !== $karma_score)
		{
			$error[] = 'KARMA_SCORE_INVALID';
		}
		
		// Now we may safely cast the karma score to an int and use it as such
		$karma_score = (int) $karma_score;
		if ($karma_score !== 0)
		{
			// Any positive number becomes 1, any negative number -1
			// TODO change this when implementing weighted karma scores
			$karma_score /= abs($karma_score);
		}

		// Store the karma into the database
		if (empty($error))
		{
			$karma_manager = $this->container->get('karma.includes.manager');
			try
			{
				if ($karma_score === 0)
				{
					if (!$allow_delete)
					{
						// For security reasons, do not give a specific error message as that
						// could yield the information that this user gave karma on the item
						trigger_error('NO_KARMA');
					}

					$karma_manager->delete_karma($karma_type_name, $item_id, $giving_user_id);
					$karma_deleted = true;
				}
				else
				{
					$karma_manager->store_karma($karma_type_name, $item_id, $giving_user_id, $karma_score, $this->request->variable('karma_comment', ''));
				}
			}
			catch (Exception $e)
			{
				trigger_error($e->getMessage());
			}
		}

		// Replace "error" strings with their real, localised form
		$error = array_map(array($this->user, 'lang'), $error);

		return array(
			'karma_deleted'	=> $karma_deleted,
			'error'			=> $error,
		);
	}

	private function check_permission($permission_name)
	{
		// Errors to display when certain permissions aren't YES
		$errors = array(
			'm_karma_delete'	=> 'SORRY_AUTH_KARMA_DELETE',
			'm_karma_edit'		=> 'SORRY_AUTH_KARMA_EDIT',
			'u_karma_delete'	=> 'SORRY_AUTH_KARMA_DELETE',
			'u_givekarma'		=> 'SORRY_AUTH_KARMA',
			'u_karma_edit'		=> 'SORRY_AUTH_KARMA_EDIT',
		);

		// Check if the user is allowed to give karma
		if (!$this->auth->acl_get($permission_name))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				trigger_error($errors[$permission_name]);
			}

			login_box('');
		}
	}
}
