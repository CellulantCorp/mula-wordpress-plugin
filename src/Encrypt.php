<?php

namespace Mula;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Encrypt {

    private $log;

    public function __construct() {
        $this->log = new Logger('name');
        $this->log->pushHandler(new StreamHandler(dirname(__FILE__) . '/../mula.log', Logger::WARNING));
    }

    public function encrypt($payload) {
        $method = 'AES-256-CBC';
        $key = hash('sha256', get_option(Config::SECRET_KEY_TEXT_INPUT));
        $iv = substr(hash('sha256', get_option(Config::IV_KEY_TEXT_INPUT)), 0, 16);

        $this->log->debug(
            " ** Before encrypting request ** \n" .
            " secret key: **********" .
            " iv key: **********" .
            " params: " . json_encode($payload) . "\n\n");

        $encrypted = openssl_encrypt(json_encode($payload, true), $method, $key, 0, $iv);

        $result = array(
            'params' => base64_encode($encrypted),
            'accessKey' => get_option(Config::ACCESS_KEY_TEXT_INPUT),
            'countryCode' => get_option(Config::COUNTRY_CODE_SELECT_INPUT)
        );

        $this->log->debug(
            " ** after encryption of request ** \n" .
            " result: " . json_encode($result) . "\n\n" );

        return $result;
    }
}