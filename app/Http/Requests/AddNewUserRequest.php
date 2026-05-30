<?php

namespace App\Http\Requests;

use App\setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

/**
 * اعتبارسنجی فرم ثبت کاربر
 */
class AddNewUserRequest extends FormRequest
{
    // public function  authorize()
    // {
    //     return true;   
    // }

    public function rules()
    {
        $preventKodRequired = \App\setting::where('name', 'prevent_user_nationalid_required_unique')->first();
        $kodRule = ($preventKodRequired && $preventKodRequired->input1)
            ? 'nullable|digits:10|unique:users,kod'
            : 'required|digits:10|unique:users,kod';

        return [
            'mobile' => ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile'],
            'phone'  => 'nullable|unique:users,phone',
            'hash'   => 'required|unique:users,hash',
            'kod'    => $kodRule,
        ];
    }

    public function messages()
    {
        return [
            'mobile.unique'  => 'این شماره موبایل قبلاً ثبت شده است',
            'mobile.regex'   => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد',
            'phone.unique'   => 'این شماره تلفن قبلاً ثبت شده است',
            'hash.required'  => 'کد اختصاصی الزامی است',
            'hash.unique'    => 'این کد اختصاصی قبلاً ثبت شده است',
            'kod.required'   => 'کد ملی الزامی است',
            'kod.digits'     => 'کد ملی باید دقیقاً ۱۰ رقم باشد',
            'kod.unique'     => 'این کد ملی قبلاً ثبت شده است',
        ];
    }
    // public function getCredentials()
    // {
    //     $username=$this->get('username');

    //     if($this->isEmail($username)){
    //         return [
    //             'email'=>$username,
    //             'password'=>$this->get('password')
    //         ];
    //     }

    //     return $this->only('username', 'password');
    // }

    // private function isEmail($param)
    // {
    //     $factory=$this->container->make(ValidationFactory::class);

    //     return ! $factory->make(
    //         ['username'=>$param],
    //         ['username'=>'email']
    //     )->fails();
    // }
}
