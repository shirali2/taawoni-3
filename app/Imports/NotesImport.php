<?php

namespace App\Imports;

use App\note;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NotesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new note([
            'user_id'      => $row['user_id'],
            'content'      => $row['content'],
            'is_mandatory' => !empty($row['is_mandatory']),
            'visibility'   => $row['visibility'],
            'admin_id'     => session('id'),
            'status'       => 'pending',
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id'    => 'required|exists:users,id',
            'content'    => 'required|string|max:5000',
            'visibility' => ['required', Rule::in(['admin_only', 'admin_and_user'])],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'user_id.required' => 'شناسه کاربر الزامی است.',
            'user_id.exists'   => 'کاربر با این شناسه یافت نشد.',
            'content.required' => 'متن یادداشت الزامی است.',
            'content.max'      => 'متن یادداشت حداکثر ۵۰۰۰ کاراکتر می‌تواند باشد.',
            'visibility.in'    => 'نوع نمایش باید admin_only یا admin_and_user باشد.',
        ];
    }
}
