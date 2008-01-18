<?php
/**
 * Root for installation
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: index.php 1170 2007-10-03 08:29:07Z crivis_s $
 */

define('CONFIG_FILE', "../config.ini");
define('HTACCESS_FILE', "../.htaccess");

header("Content-encoding: UTF-8");

set_include_path(get_include_path() .PATH_SEPARATOR . "..");
require_once 'USVN/autoload.php';
require_once 'Install.php';

$GLOBALS['language'] = 'en_US';
if (file_exists(CONFIG_FILE)) {
	try {
		$config = new USVN_Config_Ini(CONFIG_FILE, 'general');
		if (isset($config->translation->locale)) {
			$GLOBALS['language'] = $config->translation->locale;
		}
		if (isset($config->timezone)) {
			date_default_timezone_set($config->timezone);
		}
		if (isset($config->system->locale)) {
			USVN_ConsoleUtils::setLocale($config->system->locale);
		}
		if (isset($config->database->adapterName)) {
			Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
			Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
			USVN_Db_Table::$prefix = $config->database->prefix;
		}
		Zend_Registry::set('config', $config);
	}
	catch (Exception $e) {
	}
}
USVN_Translation::initTranslation($GLOBALS['language'], '../locale');


include "views/head.html";

try{
	$install_is_possible = Install::installPossible(CONFIG_FILE);
}
catch (USVN_Exception $e) {
	displayError($e->getMessage());
	include "views/footer.html";
	exit(0);
}
if ($install_is_possible) {
	if (!isset($_GET['step'])) {
		$step = 1;
	}
	else {
		$step = $_GET['step'];
	}
	try {
		installationOperation($step);
	}
	catch (USVN_Exception $e) {
		if ($step == 1) {
			include "views/install_error.html";
		}
		else {
			displayError($e->getMessage());
		}
		$step--;
	}
	installationStep($step);
}
else {
	echo "<div class='usvn_error'>" . T_("Error") . ": " . T_("USVN is already install.") . "</div>";
}

include "views/footer.html";

//------------------------------------------------------------------------------------------------
function displayError($message)
{
	echo "<div class='usvn_error'>" . T_("Error"). ": " . nl2br($message) . "</div>";
}

function installationOperation($step)
{
	$language = isset($_POST['language']) ? $_POST['language'] : $GLOBALS['language'];
	switch ($step) {
		case 1:
			if (!isset($_GET['force'])) {
				Install::checkSystem();
			}
			Install::installUrl(CONFIG_FILE, HTACCESS_FILE, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']));
		break;

		case 4:
			if ($_POST['agreement'] != "ok") {
				throw new USVN_Exception(T_("You need to accept the licence to continue installation."));
			}
		break;

		case 3:
			Install::installLanguage(CONFIG_FILE, $language);
			Install::installTimezone(CONFIG_FILE, $_POST['timezone']);
			Install::installLocale(CONFIG_FILE);
			$GLOBALS['language'] = $_POST['language'];
			USVN_Translation::initTranslation($GLOBALS['language'], '../locale');
		break;

		case 4:
			if ($_POST['agreement'] != "ok") {
				throw new USVN_Exception(T_("You need to accept the licence to continue installation."));
			}
		break;

		case 5:
			Install::installConfiguration(CONFIG_FILE, $_POST['title']);
			Install::installSubversion(CONFIG_FILE, $_POST['pathSubversion'], $_POST['passwdFile'], $_POST['authzFile'], $_POST['urlSubversion']);
		break;

		case 6:
			if (isset($_POST['createdb'])) {
				$createdb = true;
			}
			else {
				$createdb = false;
			}
			Install::installDb(CONFIG_FILE, "../SQL/", $_POST['host'], $_POST['user'], $_POST['password'], $_POST['database'], $_POST['prefix'], $_POST['adapter'], $createdb);
		break;

		case 7:
			Install::installAdmin(CONFIG_FILE, $_POST['login'], $_POST['password'], $_POST['firstname'], $_POST['lastname'], $_POST['email']);
			Install::installEnd(CONFIG_FILE);
			$GLOBALS['apacheConfig'] = Install::getApacheConfig(CONFIG_FILE);
		break;
	}
}

function installationStep($step)
{
	$language = $GLOBALS['language'];
	if ($step >= 1 && $step <= 7) {
		include "views/step$step.html";
	}
}
?>

