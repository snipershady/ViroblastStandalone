<?php

namespace App\Controller;

use App\Repository\UserRepositoryPDO;

/**
 * Description of UserController
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class UserController extends AbstractController {

    public function updateRole(): void {
        if (!$this->isGranted("ROLE_ADMIN")) {
            $this->redirect("index.php");
        }

        $userId = $this->request->getParamValueByKey("user_id", false);
        $action = $this->request->getParamValueByKey("action", false);

        $repo = new UserRepositoryPDO();

        $user = $repo->findOneById($userId);

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $this->session->set("error", "You cannot change role to admin");
            $this->redirect("users_page.php");
        }
        if ($user === null) {
            $this->redirect("index.php");
        }
        $role = $this->getRoleByAction($action);
        $user->setRoles($role);

        $repo->update($user);

        $this->redirect("users_page.php");
    }

    private function getRoleByAction(string $action): array {
        switch ($action) {
            case "enable":
                return ["ROLE_USER"];
            default:
            case "disable":
                return ["ROLE_DISABLED"];
        }
    }
}
