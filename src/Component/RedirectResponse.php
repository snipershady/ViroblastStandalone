<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Components;

use Exception;
use InvalidArgumentException;
use function ltrim;
use function header;
use function preg_match;
use function is_string;

/**
 * Description of RedirectResponse
 *
 * @author Stefano Perrini <stefano.perrini@bidoo.com> aka La Matrigna
 * @author Christian La Forgia <christian.laforgia@bidoo.com> aka Noidilaravel
 */
final class RedirectResponse {

    /**
     * @param string $url
     * @param int $statusCode
     *            default 200
     * @param array $params
     *            array associativo di parametri da passare in query string
     *            e.g. ['id' => 1] si otterrà la query string ?id=1 in append alla rotta
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function redirect($url, $statusCode = 200, $params = []) {
        if (!empty(ROOT) && !is_string(ROOT)) {
            throw new Exception("Costante ABS_ROOT non definita. Forse hai dimenticato di richiedere bootstrap.php nell'entry point");
        }
        if (empty($url)) {
            throw new InvalidArgumentException("Url non può essere vuota");
        }
        $re = "/\w+(\.php)$/";
        $reRouter ='/^r\//';
        if (!preg_match($reRouter, $url) && !preg_match($re, $url)) {
            $url .= ".php";
        }
        $url = ltrim($url, "/");

        $querystring = null;

        if (!empty($params)) {
            $querystring = http_build_query($params);
            $url = $url . "?" . $querystring;
        }



        if ($statusCode !== 200) {
            header('Location: ' . ROOT . $url, true, $statusCode);
        } else {
            header('Location: ' . ROOT . $url);
        }

        exit;
    }

}
