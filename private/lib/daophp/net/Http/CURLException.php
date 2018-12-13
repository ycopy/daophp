<?php

namespace daophp\net\http ;

class CURLException extends \Exception
{

    public function isTimeout()
    {
        return in_array($this -> getCode(), array (
                CURLTransport::CURLE_OPERATION_TIMEDOUT,
                CURLTransport::CURLE_FTP_ACCEPT_TIMEOUT
        ));
    }

    public function isHostNotResolved()
    {
        return in_array($this -> getCode(), array (
                CURLTransport::CURLE_COULDNT_CONNECT,
                CURLTransport::CURLE_COULDNT_RESOLVE_HOST,
                CURLTransport::CURLE_COULDNT_RESOLVE_PROXY
        ));
    }
}
