<?php

namespace App\Controller;

use App\Component\Request;
use App\Entity\User;
use App\Service\SessionService;

/**
 * Description of AbstractController
 *
 * @author Stefano Perrini <stefano.perrini@bidoo.com.com> aka La Matrigna
 */
abstract class AbstractController {

    protected ?User $user;
    protected Request $request;
    protected SessionService $session;
    protected string $httpMethod;

    public function __construct() {
        $this->session = SessionService::getInstance();
        $this->user = $this->session->getUser();
        $this->httpMethod = "GET";
        $this->request = new Request();
    }

    protected function isLoggedIn() {
        return $this->user !== null;
    }

    /**
     * Set allowed HTTP Method. Default <b>"GET"</b>, if set will be overwritten
     * allowed method: <b>"GET"</b>, <b>"POST"</b>,  <b>"get"</b>, <b>"post"</b>
     * @param string $httpMethod
     * @return $this
     */
    protected function setHttpMethod(string $httpMethodString): self {
        $httpMethod = strtoupper($httpMethodString);
        if ($httpMethod !== "GET" && $httpMethod !== "POST") {
            $this->json(['is_valid' => false, 'msg' => "Wrong parameter. Accepted POST or GET"]);
        }
        $this->httpMethod = $httpMethod;
        return $this;
    }

    /**
     * Check if http method is allowed
     * @return $this
     */
    protected function checkHttpMethod(): self {
        if ($this->httpMethod !== $this->request->getMethod()) {
            $this->json(['is_valid' => false, 'msg' => "BAD http method"]);
        }
        return $this;
    }

    /**
     * 
     */
    protected function redirect($namepage) {
        header("Location: $namepage");
        exit;
    }

}
