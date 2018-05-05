<?php

namespace App\Entity;

use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, Serializable {

	public $id;
	public $login;
	public $email;
	public $public;
	public $password;
	public $data;
	public $roles;
	public $articles;
	public $comments;

	function getUsername() {
		return $this->login;
	}

	function getPassword() {
		return $this->password;
	}

	function getRoles() {
		return $this->roles;
	}

	function getSalt() {
		return null;
	}

	function eraseCredentials() {
		return null;
	}

	function serialize() {
		return serialize(array(
			"id" => $this->id,
			"login" => $this->login,
			"email" => $this->email,
			"public" => $this->public,
			"password" => $this->password,
			"data" => $this->data
		));
	}

	function unserialize($serialized) {
		$unserialized = unserialize($serialized, [ "allowed_classes" => false ]);
		$this->id = $unserialized["id"];
		$this->login = $unserialized["login"];
		$this->email = $unserialized["email"];
		$this->public = $unserialized["public"];
		$this->password = $unserialized["password"];
		$this->data = $unserialized["data"];
	}

}
