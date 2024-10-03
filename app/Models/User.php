<?php

namespace division\Models;

use division\Exceptions\InvalidEnumException;
use division\Models\Enums\Role;

class User {
	private int $id;

	private string $login;


	private Role $role;

	private string $password;

    private ?string $image;

	public function getId(): int {
		return $this->id;
	}

	public function getLogin(): string {
		return $this->login;
	}

	public function setLogin(string $login): void {
		$this->login = $login;
	}


	public function getRole(): Role {
		return $this->role;
	}

	public function setRole(Role $role): void {
		$this->role = $role;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $password): void {
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$this->password = $hash;
	}

	public function hydrate(array $data): void {
		if(array_key_exists('id',$data)){
			$this->id = $data['id'];
		}

		if(array_key_exists('login',$data)){
			$this->login = $data['login'];
		}

		if(array_key_exists('role',$data)){
			$role = Role::tryFrom($data['role']);
			if($role !== null){
				$this->role = $role;
			} else{
				throw new InvalidEnumException(Role::class, $data['role']);
			}
		}

		if(array_key_exists('password',$data)){
			$this->password = $data['password'];
		}

        if(array_key_exists('image',$data)){
            $this->image = $data['image'];
        }
	}

    public function getImage(): ?string
    {
        return $this->image;
    }
}
