<?php

namespace App\Service;

use App\Component\ConfigurationHandler;
use App\Entity\User;
use App\Repository\UserRepositoryPDO;
use function filter_var;
use function session_start;

/**
 * Description of SessionService
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
final class SessionService {

    /**
     * 
     * @var SessionService
     */
    private static ?SessionService $sessionService = null;

    /**
     * 
     * @var User
     */
    private ?User $user = null;

    private function __construct() {
        $this->start();
    }

    /**
     * 
     * @return SessionService
     */
    public static function getInstance(): SessionService {
        if (self::$sessionService === null) {
            self::$sessionService = new SessionService();
        }
        return self::$sessionService;
    }

    /**
     * Retrieve user_id from Session
     * @return int
     */
    private function retrieve(): int {
        if (isset($_SESSION) && array_key_exists("user_id", $_SESSION)) {
            return (int) filter_var($_SESSION['user_id'], FILTER_SANITIZE_NUMBER_INT);
        }
        return 0;
    }

    /**
     * 
     * @return Amministratori
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * 
     * @return bool
     */
    public function isLoggedIn(): bool {
        return $this->isGranted("ROLE_USER");
    }

    /**
     * 
     * @param string $role
     * @return bool
     */
    public function isGranted(string $role): bool {
        return !empty($this->user) && is_array($this->getUser()->getRoles()) && in_array($role, $this->user->getRoles());
    }

    /**
     * 
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool {
        $_SESSION['user_id'] = $user->getId();
        $this->user = $user;
        return !empty($user->getId());
    }

    /**
     * 
     * @return bool
     */
    public function start(): bool {
        $maxlifetime = 60 * 60 * 24;
        $secure = false; // CheckDevProd::isDev() ? false : true;    // if you only want to receive the cookie over HTTPS
        $httponly = false; //CheckDevProd::isDev() ? false : true;   // prevent JavaScript access to session cookie
        $session_name = "BESTIABASE_SESSION";
        session_set_cookie_params($maxlifetime, '/', filter_input(INPUT_SERVER, "HTTP_HOST", FILTER_UNSAFE_RAW), $secure, $httponly);
        session_name($session_name);
        session_start();
        session_regenerate_id();
        $userId = $this->retrieve();
        if ($userId > 0) {
            $repo = new UserRepositoryPDO();
            $this->user = $repo->findOneById($userId);
        }
        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function destroy() {
        if (!isset($_SESSION)) {
            return true;
        }
        $this->user = null;
        //session_start();
        $_SESSION = [];
        unset($_SESSION);
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');

        return true;
    }

    /**
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
        return true;
    }

    /**
     * @param string $key
     * @param bool $clear
     * @return string
     */
    public function get($key, $clear = false) {
        $res = array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
        if ($clear) {
            $this->clear($key);
        }
        return $res;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function clear($key) {
        unset($_SESSION[$key]);
        return true;
    }
}
