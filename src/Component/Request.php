<?php

namespace App\Components;

use function filter_input;
use function filter_input_array;
use function filter_var;
use function is_int;
use function is_numeric;

/**
 * Description of Request
 * Provide information about Request. All data returned are safe and sanitized.
 *
 * @author Stefano Perrini <stefano.perrini@bidoo.com> aka La Matrigna
 * @author Damiano Improta <damiano.improta@bidoo.com> aka Drizella
 * @author Simone Mosi <simone.mosi@bidoo.com> aka Frozen
 */
final class Request {

    /**
     * Request Method: GET, POST
     * @var string
     */
    private $method;

    /**
     * Input type as defined in filter.php
     * @see filter.php
     * @var string
     */
    private $inputtype;

    /**
     * Array of params
     * @var array
     */
    private $params = null;

    /**
     * HTTP_HOST
     * @var string
     */
    private $httphost;

    /**
     * REQUEST_URI
     * @var string
     */
    private $requesturi;

    public function __construct() {
        $this->method = filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING);
        $this->inputtype = (int) $this->isGet();
        $this->httphost = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        $this->requesturi = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);
    }

    /**
     * Return Request Method
     * @return string
     *         GET|POST|PUT|DELETE
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Verify if request method is POST
     * @return bool
     */
    public function isPost() {
        return $this->method === "POST";
    }

    /**
     * Verify if request method is GET
     * @return bool
     */
    public function isGet() {
        return $this->method === "GET";
    }

    /**
     * Return 1 if GET 0 if POST as constants defined in "filter.php" INPUT_POST, INPUT_GET
     * @see filter.php
     * @return int
     */
    public function getInputtype() {
        return $this->inputtype;
    }

    /**
     * Return contents of the Host: header from the current request, if there is one.
     * @example localhost
     * @return string
     */
    public function getHttphost() {
        return $this->httphost;
    }

    /**
     * The URI which was given in order to access this page; for instance, '/index.html'. 
     * @example /administration/changepassword.php
     * @return string
     */
    public function getRequesturi() {
        return $this->requesturi;
    }

    /**
     * @param bool $checkNumeric default true
     * Retrieve from Request params received via POST or GET then sanitize and set type for each parameter.
     * @return array
     */
    public function getParams($checkNumeric = true) {
        if ($this->params === null) {
            $this->params = $this->sanitizeParams($checkNumeric);
        }
        return $this->params;
    }

    /**
     * Return value from GET or POST, by a key
     * @param string $key
     * @param bool $checkNumeric default true
     * @return string|int|float
     *         false if key is not in array Request (GET or POST)
     *         null if key exists but no value was sent
     */
    public function getParamValueByKey($key, $checkNumeric = true) {
        $value = filter_input($this->inputtype, $key, FILTER_DEFAULT);
        $arrayData = filter_input_array($this->inputtype);

        if ($arrayData === null || !array_key_exists($key, $arrayData)) {
            return false;
        }

        if ($value === null || $value === false) {
            return null;
        }

        if (is_numeric($value) && $checkNumeric) {
            return $this->getSanitizedNumber($value);
        }

        return $this->getSanitizedString($value);
    }

    /**
     * @return string <p>JSON format</p>
     */
    public function getRawValues() {
        return file_get_contents('php://input');
    }
    
    /**
     * @return array
     */
    public function getRawValuesArray() {
        return json_decode($this->getRawValues(), true, JSON_NUMERIC_CHECK);
    }
    
    /**
     * 
     * @return string[]
     */
    public function getHeaders(){
        return getallheaders();
    }

    /**
     * @param bool $checkNumeric default true
     * @return array
     * Sanitize all INPUT params from REQUST ( GET or POST method )
     */
    private function sanitizeParams($checkNumeric = true) {
        $arrayData = filter_input_array($this->inputtype);

        return $arrayData !== null ? $this->iterateArray($arrayData,$checkNumeric) : [];
    }

    /**
     * Call recursively an ARRAY to go trough all levels
     * @param array $arrayData
     * @param bool $checkNumeric default true
     * @return array
     */
    private function iterateArray($arrayData,$checkNumeric = true) {
        $localArray = [];
        foreach ($arrayData as $key => $value) {
            if (is_array($value)) {
                $localArray[$key] = $this->iterateArray($value,$checkNumeric);
            } else {
                $localArray = $this->setParams($localArray, $key, $value,$checkNumeric);
            }
        }
        return $localArray;
    }

    /**
     * 
     * @param array $localArray
     * @param string $key
     * @param string $value
     * @param bool $checkNumeric default true
     * @return array
     */
    private function setParams($localArray, $key, $value,$checkNumeric = true) {
        if (is_numeric($value) && $checkNumeric) {
            $localArray[$key] = $this->getSanitizedNumber($value);
        } else {
            $localArray[$key] = $this->getSanitizedString($value);
        }
        return $localArray;
    }

    /**
     * Return sanitized number from Request (GET or POST Method)
     * @param string $value
     *        must be "numeric"
     * @return int|float
     */
    private function getSanitizedNumber($value) {
        $numericvalue = $value + 0;
        if (is_int($numericvalue)) {
            return $this->getSanitizedIntValue($numericvalue);
        } else {
            return $this->getSanitizedFloatValue($numericvalue);
        }
    }

    /**
     * Return sanitized string from Request (GET or POST Method)
     * @param string $value
     * @return int
     */
    private function getSanitizedIntValue($value) {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Return sanitized string from Request (GET or POST Method)
     * @param string $value
     * @return float
     */
    private function getSanitizedFloatValue($value) {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Return sanitized string from Request (GET or POST Method)
     * @param string $value
     * @return string
     */
    private function getSanitizedString($value) {
        return trim(filter_var($value, FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE));
    }

}
