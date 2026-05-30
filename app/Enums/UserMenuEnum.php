<?php

namespace App\Enums;

/**
 * لیست منوها جهت اعمال دسترسی در پنل کاربر از طریق تنظمیم در هنگام تعریف مجموعه در پنل ادمین
 */
final class UserMenuEnum
{
    const Bills = 'bills'; // صورتحساب
    const Ticket = 'ticket'; // تیکت
    const Advertisment = 'advertisment'; //تبلیغات
    const Products = 'products'; // محصولات
    // const Valid_Statuses = [
    //     self::Bills,
    //     self::Ticket,
    //     self::Advertisment,
    // ];

    // public function label($str): string
    // {
    //     return match ($str) {
    //         UserMenuEnum::Bills => 'صورت حساب',
    //         UserMenuEnum::Ticket => 'تیکت',
    //         UserMenuEnum::Advertisment => 'تبلیغات',
    //         UserMenuEnum::Products => 'محصولات',
    //     };
    // }
}
