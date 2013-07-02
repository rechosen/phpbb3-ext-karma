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
	* @param phpbb_request				$request	Request object
	* @param phpbb_template				$template	Template object
	* @param phpbb_controller_helper	$helper		Controller helper object
	*/
	public function __construct(phpbb_request $request, phpbb_template $template, phpbb_controller_helper $helper)
	{
		$this->request = $request;
		$this->template = $template;
		$this->helper = $helper;
	}

	/**
	* @return Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($postid)
	{
		$this->template->assign_var('KARMA_GIVEKARMA_POSTID', $postid);
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
