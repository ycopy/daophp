<?php


namespace daophp\plugins;

use daophp\request\Request;

abstract class AbstractPlugin {

	public function preRun( Request $request) {
	}


	public function postRun( Request $request ) {
	}
	
	public function preDisplay(Request $request) {}


	public function postDisplay(Request $request) {}
}