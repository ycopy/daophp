<?php


use daophp\models\TestDBObject;
use daophp\core\Controller;


class TestDB extends Controller {


	public function init() {
		
		$this->setNoLayout();
		$this->setNoRender();
	
	}

	
	
	public function create() {
	
		$object = new TestDBObject();
		
		$object->set('name', 'my name');
		
		$rs = $object->save() ;
		
		var_dump($rs) ;	
		
	}
	
	
	public function edit() {
	
	}
	
	public function delete() {
	
	}
	
	public function read() {
	
	}
	
	
	public function find() {
	
	}

}