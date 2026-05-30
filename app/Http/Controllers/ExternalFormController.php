<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExternalFormToken;

class ExternalFormController extends Controller
{
    // GET /external/form/{token}
    public function showPassword($token)
    {
        $tokenRecord = ExternalFormToken::where('token', $token)->firstOrFail();
        if (!$tokenRecord->isValid()) {
            return view('external.error', ['message' => 'این لینک دیگر معتبر نیست.']);
        }
        return view('external.password', compact('token'));
    }

    // POST /external/form/{token}/verify
    public function verifyPassword(Request $request, $token)
    {
        $tokenRecord = ExternalFormToken::where('token', $token)->firstOrFail();
        if (!$tokenRecord->isValid()) {
            return back()->withErrors(['password' => 'این لینک معتبر نیست.']);
        }
        if (!password_verify($request->password, $tokenRecord->password_hash)) {
            return back()->withErrors(['password' => 'رمز عبور اشتباه است.']);
        }
        session(['external_verified_' . $token => true]);
        return redirect("/external/form/{$token}/search");
    }

    // GET /external/form/{token}/search
    public function search(Request $request, $token)
    {
        $this->verifySession($token);

        $tokenRecord = ExternalFormToken::with('form')->where('token', $token)->firstOrFail();
        if (!$tokenRecord->isValid()) {
            return view('external.error', ['message' => 'این لینک منقضی شده است.']);
        }

        $form       = $tokenRecord->form;
        $form_fild  = json_decode($form->fild, true);
        $user       = null;
        $existingData   = [];

        if ($request->filled('q')) {
            $q    = trim($request->q);
            $user = \App\user::where('id', $q)
                ->orWhere('kod', $q)
                ->orWhere('name', 'LIKE', "%{$q}%")
                ->orWhere('name2', 'LIKE', "%{$q}%")
                ->first();

            if ($user) {
                $userForm = \App\user_form::where('id_user', $user->id)
                    ->where('id_form', $form->id)
                    ->first();
                if ($userForm) {
                    $rawData = json_decode($userForm->form, true);
                    if (is_array($rawData)) {
                        foreach ($rawData as $entry) {
                            if (isset($entry['name_itme'])) {
                                $fieldName = $entry['name_itme'];
                                $existingData[$fieldName] = $entry[$fieldName] ?? '';
                            }
                        }
                    }
                }
            }
        }

        return view('external.search', compact(
            'token', 'tokenRecord', 'form', 'form_fild',
            'user', 'existingData'
        ));
    }

    // POST /external/form/{token}/submit
    public function submit(Request $request, $token)
    {
        $this->verifySession($token);

        $tokenRecord = ExternalFormToken::with('form')->where('token', $token)->firstOrFail();
        if (!$tokenRecord->isValid()) {
            return view('external.error', ['message' => 'این لینک منقضی شده است.']);
        }

        $user = \App\user::findOrFail($request->user_id);
        $form = $tokenRecord->form;

        $form_fild = json_decode($form->fild, true);
        $formData  = [];

        foreach ($form_fild as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'];

            if ($fieldType == 5) {
                $formData[] = [
                    'type'      => $fieldType,
                    'name_itme' => $fieldName,
                    $fieldName  => $request->has($fieldName) ? 1 : 0,
                ];
            } elseif ($request->has($fieldName) && $request->input($fieldName) !== null) {
                $formData[] = [
                    'type'      => $fieldType,
                    'name_itme' => $fieldName,
                    $fieldName  => $request->input($fieldName),
                ];
            }
        }

        \App\user_form::updateOrCreate(
            ['id_user' => $user->id, 'id_form' => $form->id],
            [
                'form'              => json_encode($formData),
                'filled_by'         => 'external',
                'external_token_id' => $tokenRecord->id,
            ]
        );

        return redirect("/external/form/{$token}/search")->with('success', 'اطلاعات با موفقیت ثبت شد.');
    }

    private function verifySession($token)
    {
        if (!session('external_verified_' . $token)) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect("/external/form/{$token}")
            );
        }
    }
}
