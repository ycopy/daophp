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
/**
 *
 * @modify
 * change to static /**
 * @author alex
 *
 * tags
 *
 * THIS IS A SINGLETON USER OBJ , IT'S Unique during the whole access
 * ALL THE INFORMATION RELATED CURRENT ACCESS SEESION OF USER IS HOLD IN THIS OBJ
 *
 * INCLUDE ID, NAME, GROUP , AND OTHER PROFILES
 * @author alex
 *
 */

namespace daophp\core;

use daophp\core\object\SingletonObject ;


final class User extends SingletonObject {

	const LOGIN = 1;
	const LOGOUT = 2;


	const USER_GROUP_MEMBER = 1;
	const USER_GROUP_ADMIN  = 2;
	const USER_GROUP_GUEST  = 3;

	const USER_ID_GUEST = -1;

	private static $userObject = null;
	public static function setUserObject( $obj ) {
		self::$userObject = $obj;
	}

	public static function getUserObject() {
		if( self::$userObject !== null ) {
			return self::$userObject;
		}

		if( (self::$userGroup != self::USER_GROUP_GUEST) && (!empty( self::$userID )) && (self::$userID > 0) ) {
			if( ( $obj = UserObject::getByPK(self::$userID) ) !== null ) {
				self::setUserObject($obj) ;
				return self::$userObject ;
			}
		} else {
			//for guest
			return null ;
			//self::setUserObject(new UserObject(DP_USER_ID_GUEST) );
		}
	}
	/**
	 * @ADD 2011-01-18,
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $userGroup = self::USER_GROUP_GUEST;
	private static $userID =  self::USER_ID_GUEST  ;

	public static function setUserGroup( $type ) {
		self::$userGroup = $type ;
	}

	public static function getUserGroup() {
		return self::$userGroup;
	}

	public static function setUserID( $id ) {
		self::$userID = $id ;
	}
	public static function getUserID() {
		return self::$userID ;
	}

	/**
	 * if update with a member, return true, otherwise return false;
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateUserInfo( $id = self::USER_ID_GUEST ) {

		if( !empty( $id ) && is_int($id) && $id !== self::USER_ID_GUEST && (($obj = UserObject::getByPK($id) ) !== null) ) {
			self::setUserObject( $obj ) ;
			self::setUserGroup( $obj->get('user_group_id') );
			self::setUserID( $obj->getPK() );
			return true;
			//init with user id
		} else {
			self::setUserGroup( self::USER_GROUP_GUEST ) ;
			self::setUserID( self::generateGuestID() ) ;
			return false;
		}
	}

	/**
	 * if success , returne true, otherwise, reutrn false.
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateLoginUserTable( $id ) {
		$loginUserObj = new LoginUserObject( array('user_id' =>$id) ) ;

		if( $loginUserObj ) {
			$loginUserObj = $loginUserObj[0] ;
			$delFlag = ( bool ) ( time() - strtotime ( $loginUserObj->get ( 'ts' ) ) < DP_LOGIN_EXPIRE_TIME);

			if( !$delFlag ) {
				$loginUserObj->delete() ;
				return false ;
			} else {
				$loginUserObj->set('ts', time() );
				return $loginUserObj->save() ;
			}
		} else {
			return false;
		}
	}

	public static function generateGuestID() {

		if( @empty( $_SERVER['REMOTE_HOST'] ) ) {
			Debug::addNotice('EMPTY REMOTE_HOST NAME: '. @$_SERVER['REMOTE_ADDR'] );
		}

		Debug::addCoreLog('GENERATE GUEST ID : ' . @$_SERVER['REMOTE_ADDR'] ) ;
		return @$_SERVER['REMOTE_ADDR'] ;
	}

	private static function updateLoginSession() {
		$sessArray = array('group' => self::getUserGroup(),'id' => self::getUserID() ) ;
		$_SESSION[DP_LUSK] = serialize( $sessArray ) ;
		//Debug::addTrace ( 'UPDATE LOGIN SESSION END: '. self::getUserID() . $_SESSION[DP_LUSK] );
	}
/*
 * init LoginTable success , return true,
 * init guest,or LoginTable failed, return false;
 */
	private static function initLoginInfo( $id = '' ) {

		$flag = false;

		if( !empty( $id )) {
			//from id
			if( self::updateLoginUserTable( $id ) ) {
				$flag = self::updateUserInfo( $id ) ;
			}
		} else {
			//from session
			if (! isset ( $_SESSION [DP_LUSK] ) || empty ( $_SESSION [DP_LUSK] )) {
				$flag = self::updateUserInfo() ;
			} else {
				$sessArray = unserialize( $_SESSION[DP_LUSK] );
				if( isset( $sessArray['id'] ) && $sessArray['group'] !== self::USER_GROUP_GUEST && self::updateLoginUserTable( $sessArray['id'] ) ) {
					$flag = self::updateUserInfo( $sessArray['id'] ) ;
				} else {
					$flag = self::updateUserInfo() ;
				}
			}
		}

		self::updateLoginSession();
		return $flag;
	}

	public static function init() {
		self::initLoginInfo();
		// pull in the user component
	}

	/*
	 * login status
	 */
	//	private static $status = self::LOGOUT;


	private static $loginCode = - 1;

	const LOGIN_IDLE 				= -1;
	const LOGIN_OK					= 0;
	const LOGIN_USER_NOT_FOUND		= 1;
	const LOGIN_USER_PASS_ERROR		= 2;
	const LOGIN_USER_NOT_ACITVE 	= 3;
	const LOGIN_SYSTEM_ERROR		= 4;

	public static function getLoginCode() {
		return self::$loginCode;
	}

	public static function login($name, $pass) {

		Debug::addTrace ( "try to init login with: " . $name . ':' . $pass );
		self::$loginCode = self::LOGIN_IDLE;

		if (! UserObject::hasAny ( array ('name' => $name ) )) {
			Debug::addError ( "WRONG USER ID IN IN LOGIN ACTION, NAME: " . $name . ' IP: ' . $_SERVER ['REMOTE_ADDR'] );
			self::$loginCode = self::LOGIN_USER_NOT_FOUND;
			return false;
		}

		$userObj = new UserObject ( array ('name' => $name ) );

		if( $userObj->get("status") == 'inactive') {
			Debug::addTrace("LOGIN FAILED, USER NOT ACTIVED: ". $userObj->get('name') );
			self::$loginCode ==  self::LOGIN_USER_NOT_ACITVE ;
			return false;
		}

		if ($userObj->get ( 'pass' ) !== $pass) {
			Debug::addTrace ( "LOGIN FAILED, PASS ERROR, expected: " . $userObj->get ( "pass" ) );
			self::$loginCode = self::LOGIN_USER_PASS_ERROR;
			return false;
		}

		$userId = $userObj->get ( 'id' );

		$rs = self::initLoginInfo( $userId );

		if (! $rs ) {
			Debug::addError ( "LOGIN ERROR, SAVE RECORD FAILED, USER ID: " . $userId );
			self::$loginCode = self::LOGIN_SYSTEM_ERROR;
			return false;
		}

		self::$loginCode = self::LOGIN_OK;
		return true;
	}

	public static function logout($id = null) {
		$rs = false;

		if( $id == null ) {
			$id = User::getUserId() ;
		}

		if ( self::online ( $id ) ) {
			$loginUserObj = new LoginUserObject ( array ("user_id" => $id ) );
			$rs = $loginUserObj->delete ();
			self::updateLoginSession ();
			$userObject = new UserObject(array('id' => $id ) );
			Debug::addTrace ( "log out for user: " . $userObject->get ( "name" ) );
		}

		return $rs;
	}

	const FORCE_TO_DEL_EXPIRE_RECORD_FROM_DB = 1;
	const NOT_TO_DEL_EXPIRE_RECORD_FROM_DB = 2;

	public static function online($id = null ) {

		return self::initLoginInfo( $id ) ;
	}

	public static function getProfile($key) {
		return self::$userObject->get ( $key );
	}

	public static function setProfile($key, $value) {
		return self::$userObject->set ( $key, $value );
	}

	public static function saveProfile($key, $value) {
		return self::$userObject->save ( $key, $value );
	}
}
?>