<?php
/**
 * Main controller
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: Controller.php 1170 2007-10-03 08:29:07Z crivis_s $
 */
class USVN_Controller extends Zend_Controller_Action {
	/**
	 * Zend_Controller_Request_Abstract object wrapping the request environment
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request;

	/**
	* Mime type render by the controller
	*
	* @var string
	*/
	protected $_mimetype = 'text/html';

	/**
	 * Init method.
	 *
	 * Call during construction of the controller to perform some default initialization.
	 *
	 */
	public function init() {
		parent::init();
         $this->getResponse()->setHeader('Content-Type', $this->_mimetype);
        $this->_request->setParam('view', $this->_helper->viewRenderer);
        $this->_helper->viewRenderer->setViewScriptPathSpec(":action.phtml");
        $this->view->addHelperPath(USVN_HELPERS_DIR, 'USVN_View_Helper');
		$this->_request->setParam('project', $this->_request->getParam('project', '__NONE__'));
		$this->_request->setParam('area',    $this->_request->getParam('area',    '__NONE__'));
        if ($this->_mimetype != 'text/html') {
            $this->_helper->viewRenderer->setNoRender();
        }
	}

	/**
     * Pre-dispatch routines
     *
     * Called before action method. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to skip processing the current action.
     *
     * @return void
     */
	public function preDispatch()
	{
		$request = $this->getRequest();
		$controller = $request->getControllerName();

		$dir = realpath(USVN_VIEWS_DIR . "/$controller");
		if ($dir === false || !is_dir($dir)) {
			throw new Zend_Controller_Exception("Controller's views directory not found. Controller is $controller.");
		}
		$this->view->setScriptPath($dir);
		$this->view->assign('project', $request->getParam('project'));
		$this->view->assign('controller', $request->getParam('controller'));
		$this->view->assign('action', $request->getParam('action'));

		$identity = Zend_Auth::getInstance()->getIdentity();
		if ($identity === null && $controller != "login") {
			$this->_redirect("/login/");
		}

		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array("users_login = ?" => $identity['username']));
		if ($user === null && $controller != "login") {
			$this->_redirect("/logout/");
		}
		$request->setParam('user', $user);
	}

	/**
	 * Default action for every controller.
	 *
	 */
	public function indexAction() {
		$this->render();
	}

	/**
	 * Redirect to / unless an action handle the request
	 *
	 */
	public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
