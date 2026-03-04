<?php

namespace App\Listeners;

use App\Models\Korisnik;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetProfileVerified
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        //
        $korisnik = $event->user;

        if ($korisnik->statuskorisnikaid != 1) {
            $korisnik->statuskorisnikaid = 1;
            $korisnik->save();
        }
    }
}
