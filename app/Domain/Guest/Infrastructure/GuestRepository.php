<?php

namespace App\Domain\Guest\Infrastructure;

use App\Domain\Guest\Domain\Guest;
use App\Domain\Guest\Domain\GuestRepositoryInterface;
use App\Infrastructure\Models\Guest as ModelsGuest;
use Illuminate\Database\Eloquent\Collection;

class GuestRepository implements GuestRepositoryInterface
{
    public function store(Guest $guest): void
    {
        ModelsGuest::create([
            'first_name' => $guest->getFirstName(),
            'last_name' => $guest->getLastName(),
            'phone' => $guest->getPhone(),
            'email' => $guest->getEmail(),
            'country' => $guest->getCountry()
        ]);
    }

    public function getAll(): Collection
    {
        return ModelsGuest::all();
    }

    public function findById(int $id): ModelsGuest
    {
        return ModelsGuest::findOrFail($id);
    }
    
    public function update(int $id, Guest $guest): void
    {
        $guestModel = ModelsGuest::findOrFail($id);

        $guestModel->update([
            'first_name' => $guest->getFirstName() ?: $guestModel->first_name,
            'last_name' => $guest->getLastName() ?: $guestModel->last_name,
            'phone' => $guest->getPhone() ?: $guestModel->phone,
            'email' => $guest->getEmail() ?: $guestModel->email,
            'country' => $guest->getCountry() ?: $guestModel->country
        ]);
    }

    public function remove(int $id): void
    {
        $guest = ModelsGuest::findOrFail($id);

        ModelsGuest::destroy($guest->id);
    }
}