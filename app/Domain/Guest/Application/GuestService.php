<?php

namespace App\Domain\Guest\Application;

use App\Domain\Guest\Domain\Guest;
use App\Domain\Guest\Domain\GuestRepositoryInterface;
use App\Infrastructure\Models\Guest as ModelsGuest;
use Illuminate\Database\Eloquent\Collection;
use libphonenumber\PhoneNumberUtil;

class GuestService
{
    private $guestRepository;

    public function __construct(GuestRepositoryInterface $guestRepository)
    {  
        $this->guestRepository = $guestRepository;
    }

    public function createGuest(array $guestData)
    {
        if (!isset($guestData['country'])) {
            $guestData['country'] = $this->getCountryByPhoneCode($guestData['phone']);
        }

        $guest = new Guest($guestData);

        $this->guestRepository->store($guest);

        return $guest;
    }

    public function getAllGuests(): Collection
    {
        return $this->guestRepository->getAll();
    }

    public function getGuestById(int $id): ModelsGuest
    {
        return $this->guestRepository->findById($id);
    }

    public function updateGuestInfo(int $id, array $guestData): Guest
    {
        if (isset($guestData['phone']) && !isset($guestData['country'])) {
            $guestData['country'] = $this->getCountryByPhoneCode($guestData['phone']);
        }

        $guest = new Guest($guestData);

        $this->guestRepository->update($id, $guest);

        return $guest;
    }

    public function removeGuest(int $id): void
    {
        $this->guestRepository->remove($id);
    }

    private function getCountryByPhoneCode(string $phone): string
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $numberProto = $phoneNumberUtil->parse($phone, null);
        $regionCode = $phoneNumberUtil->getRegionCodeForNumber($numberProto);
    
        return \Locale::getDisplayRegion('-' . $regionCode, 'en');
    }
}