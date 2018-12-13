<?php

namespace demo\controllers;

use daophp\responser\Responser;
use daophp\core\DaoPHP;
use daophp\core\Controller ;

class Index extends Controller {

	public function init() {}



	public function index() {
		$this->assign( 'key1', 'hello worlds1');
		$this->assign( 'key2', 'hellow worlds2');
	}

	public function say() {
		$this->assign( 'words', 'welcom to daophp' );
	}

	/**
	 * if you do not want to create a view file this action , please call DaoPHP::getInstance()->setNoRender()
	 * Enter description here ...
	 */
	public function noRender() {

		$this->setNoLayout() ;

		echo 'no render';
	}

	public function json() {

		$this->setResponserType( Responser::TYPE_JSON );

		$this->assign('code', 200 );
		$this->assign('message', 'message message');

	}
	
	public function code() {		
		$this->setJsonView();		
		$this->assign('key', 404);
		$arr = array("k1"=>'v1', 'k2'=> 'v2', 'k3'=> array("k31"=>'vv31'));
		$this->assign('var', $arr);
		http_response_code(404);
	}
}