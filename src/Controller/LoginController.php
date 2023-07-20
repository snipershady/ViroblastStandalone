<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepositoryPDO;
use App\Service\PasswordHasher;
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
        $plainPassword = $this->request->getParamValueByKey("pswd", false);
        $repo = new UserRepositoryPDO();
        $ph = new PasswordHasher();
        $user = $repo->findOneUsernameAndPassword($username, $ph->hashPassword($plainPassword));
        
        if ($user === null) {
            $this->redirect("login_page.php");
        }
        $session = SessionService::getInstance();
        $session->create($user);

        $this->redirect("index.php");
    }

    public function register(): void {
        $username = $this->request->getParamValueByKey("username", false);
        $plainPassword = $this->request->getParamValueByKey("pswd", false);
        $email = $this->request->getParamValueByKey("pswd", false);

        $ph = new PasswordHasher();
        $repo = new UserRepositoryPDO();
        $user = new User();
        $roles = ["ROLE_DISABLED"];
        $user
                ->setEmail($email)
                ->setPassword($ph->hashPassword($plainPassword))
                ->setRoles($roles)
                ->setUsername($username);
        $repo->save($user);
        $this->redirect("index.php");
    }

    public function logout() {
        $this->session->destroy();
        $this->redirect("index.php");
    }

    private function isSamePassword(string $password, string $dbPassword): bool {
        return $password === $dbPassword && hash_equals($dbPassword, $password);
    }
}
