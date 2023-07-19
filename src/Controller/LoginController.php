<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepositoryPDO;
use App\Service\SessionService;

/**
 * Description of LoginController
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class LoginController extends AbstractController {

    public function login(): void {

        if ($this->request->isGet()) {
            $this->redirect("login_page.php");
        }
        $username = $this->request->getParamValueByKey("username", false);
        $password = $this->request->getParamValueByKey("pswd", false);
        $repo = new UserRepositoryPDO();
        $user = $repo->findOneUsernameAndPassword($username, $password);
        if ($user === null) {
            $this->redirect("login_page.php");
        }
        $session = SessionService::getInstance();
        $session->create($user);

        $this->redirect("index.php");
    }
    
    public function register(): void {
        $username = $this->request->getParamValueByKey("username", false);
        $password = $this->request->getParamValueByKey("pswd", false);
        $email = $this->request->getParamValueByKey("pswd", false);
        $repo = new UserRepositoryPDO();
        $user = new User();
        $user
                ->setEmail($email)
                ->setPassword($password)
                ->setRoles("'[\"ROLE_USER\"]'")
                ->setUsername($username);
        $repo->save($user);
    }

    public function logout() {
        $this->session->destroy();
        $this->redirect("index.php");
    }

}
