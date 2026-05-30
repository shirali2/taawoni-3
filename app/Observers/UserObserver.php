<?php

namespace App\Observers;

use App\user;
use App\note;

class UserObserver
{
    public function updated(user $user)
    {
        if ($user->wasChanged('mobile')) {
            $old = $user->getOriginal('mobile');
            $new = $user->mobile;

            note::create([
                'user_id'      => $user->id,
                'admin_id'     => null,
                'content'      => "تلفن پنل کاربری از شماره {$old} به {$new} تغییر یافت.",
                'is_mandatory' => true,
                'status'       => 'pending',
            ]);
        }
    }
}
