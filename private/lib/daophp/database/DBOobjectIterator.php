<?php
/*************************************************

DaoPHP - the PHP Web Framework
Author: cpingg@gmail.com
Copyright (c): 2008-2010 New Digital Group, all rights reserved
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

namespace daophp\core;


/**
 * 
 * 
 * This is abstract model class which will be extended by any Model Class, 
 * 
 * the class implements interface SeekAbleIterator from php spl , read more from the followwing url to learn spl
 * http://www.php.net/~helly/php/ext/spl/
 * 
 * 
 * @date 2009-07-06
 * @author ping.cao
 *
 */
class DBObjectIterator implements \SeekAbleIterator {
	
	
	/**
	 * retry flag, if set to true, we should retry db operatoins
	 * be false by default
	 * 
	 */
	
	private $retryDBOperations = false;
	private $retryTimes = 3;
	private $failedList = array();
	
	/**
	 * data object array
	 * @var a array of stdClass
	 */
	
	private $dbObject = array();
	
	/**
	 * the index flag the current key
	 * @var unknown_type
	 */
	private $key = 0 ;
	
	
	public function __construct( $dbObjectList = null )
	{
		$this->dbObject = $dbObjectList;
	}
	
	
	public function setRetryFlag( $flag )
	{
		$previous = $this->getRetryFlag();
		$this->retryDBOperations = (bool)($flag);
		return $this ;
	}
	
	public function getRetryFlag()
	{
		return $this->retryDBOperations;
	}
	
	
	public function setRetryTimes( $times )
	{
		if( !$this->getRetryTimes() )
		{
			trigger_error('Pls turn on RetryFlag by <b>$this->setRetryFlag(true)</b> first', E_USER_NOTICE );
			return '';
		}
		$previous  = $this->getRetryTimes();
		$this->retryTimes = intval( $times );
		return $this ;
	}
	
	public function getRetryTimes()
	{
		return $this->retryTimes;
	}
	
	public function saveObject( stdClass $object )
	{
		return $object->save();
	}
	
	
	public function saveCurrent()
	{
		if( !$this->valid() )
		{
			$this->rewind();
			return false;
		}
		
		return $this->saveObject( $this->current() );
	}
	
	
	/**
	 * save all object
	 * @param $saveCurrentOnly
	 * @return unknown_type
	 */
	public function saveAll( $saveCurrentOnly = false )
	{
		$failedList = array();
		if( $saveCurrentOnly === true )
		{
			return $this->saveCurrent();
		}
		
		$this->rewind();
		
		while( $this->valid() )
		{
			if( !$this->saveObject( $this->current() ) )
			{
				$failedList[] = $this->current();
			}
			$this->next();
		}
		
		/**
		 * Retry db operation
		 */
		if( count( $failedList ) )
		{
			foreach( $failedList as $key => $value  )
			{
				$retryTimes = $this->retryTimes ;
				while( $retryTimes-- > 0 && 1 ){
					if( $this->saveObject( $value ))
					{
						$failedList[$key] = null;
						unset( $failedList[$key] );
					}
				}
			}
		}
		
		/**
		 * store the failed list into $this->failedList, we can do another retry test in __destruction func
		 */
		
		$result =  true;
		$failedListKey = "";
		if( count( $failedList ))
		{
			$this->failedList = $failedList;
			foreach( $this->failedList as $key => $value )
			{
				$failedListKey .=  $value->key() . "\t";
			}
			$result = false;
		}
		
		trigger_error( "The following DBI Object save action failed:\n {$failedListKey}" , E_USER_NOTICE );
		return $result;
	}
	
	
	
	public function current() {
		if( $this->valid() ) {
			return $this->dbObject[$this->key];
		}
		return false;
	}
	
	public function key() {
		return $this->key ;	
	}
	
	/**
	 * if we've reach the last one ,we rewind to index 0 , and return false, 
	 * otherwise return the current obj
	 */
	public function next() {
		$this->key ++ ;
		if($this->valid())
		{
			return $this->current();
		}
		return false;
	}
	
	public function rewind() {
		return $this->seek( 0 );
	}
	
	public function valid() {
		$result = (bool)( $this->key <= ( count( $this->dbObject )- 1) );
		
		if( $result === false )
		{
			$this->rewind();
		}
		
		return $result;
	}
	
	
	
	/**
     * seek the key to the given number , and then return the previous
	 */
	
	public function seek( $key ) {
		
		$key = intval( $key );
		$previous = $this->key() ;
		
		if( $key < 0 )
		{
			$this->rewind();
			trigger_error("key for seek funciton less than zero, [rewind the key]", E_USER_NOTICE );
		}
		else if( count( $this->dbObject ) < $key )
		{
			/**
			 * we count the number from 0
			 * @var Model
			 */
			$this->key = count( $this->dbObject )  - 1 ;	
			trigger_error("key for seek funciton exceed the total, [seek to the last one]", E_USER_NOTICE );
		}
		else {
			$this->key = $key ;
		}

		return $previous ;
	}
}

?>