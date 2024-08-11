<?php

namespace App\Domain\Guest\Domain;

class Guest
{
    private ?string $firstName;
    private ?string $lastName;
    private ?string $phone;
    private ?string $email;
    private ?string $country;

    public function __construct(array $attributes)
    {
        $this->firstName = (isset($attributes['first_name'])) ? $attributes['first_name'] : null;
        $this->lastName = (isset($attributes['last_name'])) ? $attributes['last_name'] : null;
        $this->phone = (isset($attributes['phone'])) ? $attributes['phone'] : null;
        $this->email = (isset($attributes['email'])) ? $attributes['email'] : null;
        $this->country = (isset($attributes['country'])) ? $attributes['country'] : null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }
}