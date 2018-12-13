<?php
/*************************************************

DaoPHP - the PHP Web Framework
Author: cpingg@gmail.com
Copyright (c): 2008-2010 DaoPHP Group, all rights reserved
Version: 1.0.0

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

You may contact the author of DaoPHP by e-mail at:
cpingg@gmail.com

The latest version of DaoPHP can be obtained from:
https://cp-daophp.googlecode.com/svn/trunk/

*************************************************/

namespace daophp\responser;

use daophp\core\Debug;
use daophp\core\object\DPObject;


abstract class Responser extends DPObject {
	
	const TYPE_NULL     = 'null' ;
	const TYPE_JSON 	= 'json' ;
	const TYPE_XHTML	= 'xhtml' ;
	
	private static $_typeRange = array(
		self::TYPE_JSON,
		self::TYPE_XHTML
	);
	
	public static function createResponser( $type ) {
		
		switch ( $type ) {
			
			case self::TYPE_JSON :
				return new JsonResponser( self::TYPE_JSON );
			case self::TYPE_XHTML:
				return new XhtmlResponser(self::TYPE_XHTML);
			case self::TYPE_NULL:
			    return new NullResponser(self::TYPE_NULL);
			default:
				throw new \InvalidArgumentException( 'must in ['. implode(',', self::$_typeRange) .'] ' );
				break;
		}
	}
	
	private $_type;
	public function __construct( $type ) {
	    parent::__construct();    
	    $this->_type = $type;
	}
	
	public function setType( $type ) {
	    if( !in_array($type, self::$_typeRange, true)) {
	        throw new \InvalidArgumentException( 'must in ['. implode(',', self::$_typeRange) .'] ' );        	        
	    }
	    
	    $this->_type = $type;	    
	}
	
	public function getType() {
	    return $this->_type;	    
	}
	
	private $_charset = 'utf-8';
	public function getCharset() {
		return $this->_charset ;
	}
	
	public function setCharset( $charset ) {
		$this->_charset =  $charset;
		return $this;
	}	
	
	public $layoutSuffix = '.phtml';
	public $layout = 'index';

	public function setLayout($layout) {
		$previous = $this->layout;
		$this->layout = trim ( $layout );
		return $this ;
	}

	public function getLayout() {
		return $this->layout;
	}

	private $title = 'page title';
	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		if (! empty ( $title )) {
			$previous = $this->title;
			$this->title = $title;
			return $this ;
		}
	}

	private $keywords ;
	public function getKeywords() {
		return $this->keywords;
	}
	public function setKeywords( $keywords ) {
		$this->keywords = $keywords ;
	}

	private $description ;
	public function getDescription() {
		return $this->description;
	}
	public function setDescription( $desc ) {
		$this->description = trim($desc) ;
	}

	protected $headerLines = array ();
	public function addHeaderLine($line) {
		Debug::trace("ADD HEADER FOR LAYOUT: ". htmlentities($line) );
		array_push( $this->headerLines, trim($line));
		
		//$this->headerLines [] = trim ( $line );
	}
	public function getHeaderLines() {
		return $this->headerLines;
	}

	public function disableCache() {
		$this->addHeaderLine('<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT" />') ;
		$this->addHeaderLine('<meta http-equiv="pragma" content="no-cache" />') ;
	}
	
	private $content = '';
	public function appendContent( $content ) {
		$this->content .= $content ;
		return $this;
	}
	public function getContent() {
		return $this->content ;
	}
	private $var = array();
	public function assign( $key, $value ) {
		$this->var[$key] = $value;
	}
	
	public function getAllVars() {
		return $this->var;
	}
	
	abstract public function render();
}
?>