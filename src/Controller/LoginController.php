<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SessionService;

/**
 * Description of LoginController
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class LoginController extends AbstractController {

    public function login(): void {
        var_dump($this->request->getParams());
        if ($this->request->isGet()) {
            $this->redirect("login_page.php");
        }

        $user = new User();
        $user
                ->setId(1)
                ->setRoles(["ROLE_USER"])
                ->setUsername("shady")
                ->setEmail("perrini.stefano@gmail.com");
        $session = SessionService::getInstance();
        $session->create($user);

        $this->redirect("index.php");
    }

    public function logout() {
        $this->session->destroy();
        $this->redirect("index.php");
    }

}
