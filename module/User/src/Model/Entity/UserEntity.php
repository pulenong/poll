<?php

declare(strict_types=1);

namespace User\Model\Entity;

class UserEntity
{
    private int|string $user_id;
    private string $username;
    private string $email;
    private string $password;
    private string $picture;
    private string $birthdate;
    private int|string $role_id;
    private int|string $enabled;
    private string $created;
    private string $updated;
    #user_roles table columns
    private string $role;

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int|string $userId): UserEntity
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): UserEntity
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserEntity
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserEntity
    {
        $this->password = $password;
        return $this;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): UserEntity
    {
        $this->picture = $picture;
        return $this;
    }

    public function getBirthdate(): string
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): UserEntity
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getRoleId(): int
    {
        return $this->role_id;
    }

    public function setRoleId(int|string $roleId): UserEntity
    {
        $this->role_id = $roleId;
        return $this;
    }

    public function getEnabled(): int
    {
        return $this->enabled;
    }

    public function setEnabled(int|string $enabled): UserEntity
    {
        $this->enabled = $enabled;
        return $this;
    }
    
    public function getCreated(): string
    {
        return $this->created;
    }

    public function setCreated(string $created): UserEntity
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): string
    {
        return $this->updated;
    }

    public function setUpdated(string $updated): UserEntity
    {
        $this->updated = $updated;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): UserEntity
    {
        $this->role = $role;
        return $this;
    }
}
