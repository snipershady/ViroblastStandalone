<?php

namespace App\Entity;

/**
 * Description of User
 *
 * @author Stefano Perrini <perrini.stefano@gmail.com> aka La Matrigna
 */
class User {

    private int $id;
    private string $username;
    private string $email;
    private string $password = "";
    private array $roles = [];

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array {
        return $this->roles;
    }
    
    public function getRolesHumanReadable(): string {
        return str_replace("ROLE_", "", implode(",", $this->roles));
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

}
