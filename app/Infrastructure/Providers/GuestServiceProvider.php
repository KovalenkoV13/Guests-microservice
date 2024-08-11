<?php

namespace App\Infrastructure\Providers;

use App\Domain\Guest\Domain\GuestRepositoryInterface;
use App\Domain\Guest\Infrastructure\GuestRepository;
use Illuminate\Support\ServiceProvider;

class GuestServiceProvider extends ServiceProvider
{

    public array $bindings = [
        GuestRepositoryInterface::class => GuestRepository::class
    ];
    
}
