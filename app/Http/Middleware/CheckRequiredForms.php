<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Controller;

class CheckRequiredForms
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // روت‌هایی که نباید ریدایرکت شوند (برای جلوگیری از لوپ در زمان انتخاب گروه)
        if ($request->is('user/grop')) {
            return $next($request);
        }

        if (session()->has('id_user') && session()->has('grop_user')) {
            try {
                // دریافت آخرین فرم اجباری پر نشده
                $requiredForm = Controller::_user__get_id_last_form_required_in_menus(session('id_grop_user'));

                if ($requiredForm) {
                    $targetUrl = $requiredForm['url'];
                    $formHash = $requiredForm['hash'];

                    // روت‌هایی که کاربر مجاز است در آن‌ها باشد (صفحه فرم و متد ذخیره)
                    $saveUrlPattern = 'user/form/' . $formHash;

                    // چک کردن اینکه آیا در مسیر مجاز هستیم یا خیر
                    if (!$request->is(ltrim($targetUrl, '/')) && !$request->is($saveUrlPattern)) {
                        // سشن برای استفاده در نمایش منوها (قفل کردن)
                        session(['pending_required_form' => true]);
                        return redirect()->to($targetUrl);
                    }
                } else {
                    session()->forget('pending_required_form');
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('CheckRequiredForms error: ' . $e->getMessage(), [
                    'url' => $request->fullUrl(),
                    'db'  => config('database.connections.mysql.database'),
                ]);
            }
        }

        return $next($request);
    }

}
