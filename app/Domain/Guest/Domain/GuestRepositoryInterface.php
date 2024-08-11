<?php

namespace App\Domain\Guest\Domain;

use App\Domain\Guest\Domain\Guest;
use App\Infrastructure\Models\Guest as ModelsGuest;
use Illuminate\Database\Eloquent\Collection;

interface GuestRepositoryInterface
{
    public function store(Guest $guest): void;

    public function getAll(): Collection;
    
    public function findById(int $id): ModelsGuest;

    public function update(int $id, Guest $guest): void;

    public function remove(int $id): void;
}