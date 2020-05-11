<?php

namespace App\Entity;

use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, Serializable
{
    public $id;
    public $login;
    public $email;
    public $public;
    public $password;
    public $data;
    public $roles;
    public $articles;
    public $comments;

    public function getUsername()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function serialize()
    {
        return serialize(array(
            "id" => $this->id,
            "login" => $this->login,
            "email" => $this->email,
            "public" => $this->public,
            "password" => $this->password,
            "data" => $this->data
        ));
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized, [ "allowed_classes" => false ]);
        $this->id = $unserialized["id"];
        $this->login = $unserialized["login"];
        $this->email = $unserialized["email"];
        $this->public = $unserialized["public"];
        $this->password = $unserialized["password"];
        $this->data = $unserialized["data"];
    }
}
