<?php

namespace daophp\net\http ;


//@NOTE, refer to
//https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
class StatusCode {
	
	const S_CONTNUE 					= 100;
	const S_SWITCHING_PROTOCOL 			= 101;
	const S_PROCESSING 					= 102;
	const S_EARLY_HINTS					= 103;
	
	const S_OK							= 200;
	const S_CREATED						= 201;
	
	const S_ACCEPTED					= 202;
	const S_NON_AUTHORITATIVE_INFO		= 203; //(since HTTP/1.1)
	const S_NO_CONTENT					= 204;
	
	const S_RESET_CONTENT				= 205;
	const S_PARTIAL_CONTENT 			= 206;
	const S_MULTI_STATUS 				= 207;
	const S_ALREADY_REPORTED 			= 208;
	const S_IM_USED						= 226;
	
	
	const S_MULTI_CHOICES				= 300;				
	const S_MOVED_PERMANENTLY			= 301;
	const S_FOUND						= 302;
	
	const S_SEE_OTHER					= 303;	
	const S_NOT_MODIFIED				= 304;
	const S_USE_PROXY					= 305;
	const S_SWITCH_PROXY				= 306;
	const S_TEMPORARY_REDIRECT			= 307;
	const S_PERMANENT_REDIRECT			= 308;
	
	const S_BAD_REQUEST					= 400;
	const S_UNAUTHORIZED 				= 401;
	const S_PAYMENT_REQUIRED			= 402;
	const S_FORBIDDEN					= 403;
	const S_NOT_FOUND					= 404;
	const S_METHOD_NOT_ALLOWED 			= 405;
	const S_NOT_ACCEPTABLE 	=406;
	
	const S_PROXY_AUTHENTICATION_REQUIRED = 407;
	const S_REQUEST_TIMEOUT =408;
	const S_CONFLICT = 409;
	const S_GONE =410;
	const S_LENGTH_REQUIRED = 411;
	const S_PRECONDITION_FAILED = 412;
	const S_PAYLOAD_TOO_LARGE = 413;
	
	
	const S_URI_TOO_LONG = 414;
	const S_UNSUPPORTED_MEDIA_TYPE = 415;
	const S_RANGE_NOT_SATISFIABLE = 416;
	const S_EXPECTATION_FAILED =417;
	const S_IM_A_TEAPOT = 418;
	
	const S_MISDIRECTED_REQUEST = 421;
	const S_UNPROCESSED_ENTITY = 422;
	const S_LOCKED = 423;
	const S_FAILED_DEPENDENCY = 424;
	
	const S_UPGRADE_REQUIRED=426;
	const S_PRECONDITION_REQUIRED=428;
	const S_TOO_MANY_REQUESTS =429;
	const S_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	
	const S_UNAVAILABLE_FOR_LEGAL_REASONS = 435;
	
	const S_INTERNAL_SERVER_ERROR = 500;
	const S_NOT_IMPLEMENTAED = 501;
	const S_BAD_GATEWAY = 502;
	
	const S_SERVICE_UNAVAILABLE = 503;
	const S_GATEWAY_TIMEOUT = 504;
	
	const S_HTTP_VERSION_NOT_SUPPORTED = 506;
	const S_INSUFFICIENT_STORAGE = 507;
	const S_LOOP_DETECTED = 508;
	
	const S_NOT_EXTENDED = 510;
	const S_NETWORK_AUTHENTICATION_REQUIRED = 511;
}

