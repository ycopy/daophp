<?php

/**
 * the most useful usage of this file
 * 1, custom some env
 * 2, setting for some third part library, such as it's autoloader
 * 3, register some plugin
 *
 * if the file exists , DaoPHP include it automatically
 *
 * DaoPHP will do the follwoing things
 *
 *
 * 1, new Bootstrap
 *
 * 2, call Bootstrap->startup();
 *
 * 3, in Bootstrap->startup() , all the functions that start with _ will be called
 */
use admin\plugins\AclPlugins;
use daophp\core\DaoPHP;
use admin\utils\AclController;
use daophp\database\DBManager;
use daophp\core\AutoloaderManager;
use daophp\AbstractBootstrap;
use admin\plugins\LogPlugins;


class Bootstrap extends AbstractBootstrap
{

	/**
	 * All this class' method that begin with _ will be called automatically
	 */
	
	// public function _customRun() {
	// }
	//protected function _initXXXLibrary()
	//{
	//	define('__XXX__', 1);
	//	include_once DP_LIB_DIR . './XXX/XXXAutoloader.php';
	//	
	//	$autoloadManager = AutoloaderManager::getInstance();		
	//	$autoloadManager -> addAutoloader('XXX', new XXX\XXXAutoloader(DP_LIB_DIR));
	//}

	protected function _initDBConfig()
	{
		$dbConfig = array ();		

		$dbConfig[DBManager::DB_HOST] = DB_HOST;
		$dbConfig[DBManager::DB_NAME] = DB_NAME;
		$dbConfig[DBManager::DB_USER_NAME] = DB_USER_NAME;
		$dbConfig[DBManager::DB_USER_PASS] = DB_USER_PASS;
		
		DBManager::setDBConfig($dbConfig);
	}

	protected function _initPHPMailerLibrary()
	{
		define('__PHPMAILER__', 1);
		include_once DP_LIB_DIR . './PHPMailer/PHPMailerAutoloader.php';
		
		$autoloadManager = AutoloaderManager::getInstance();
		
		$autoloadManager -> addAutoloader('PHPMailer', new PHPMailer\PHPMailerAutoloader(DP_LIB_DIR));
	}

	protected function _registerPlugins()
	{
		//$adminAclController = new AclController();
		//$aclPlugin = new AclPlugins($adminAclController);
		//$logPlugin = new LogPlugins();
		
		//Daophp::getInstance() -> registerPlugin('acl', $aclPlugin);
		//Daophp::getInstance() -> registerPlugin('log', $logPlugin);
	}
}

