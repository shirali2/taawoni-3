<?php

namespace App\Exports;

use App\note;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NotesExport implements FromQuery, WithHeadings, WithMapping
{
    private $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return note::with(['user.group', 'admin'])
            ->when(isset($this->filters['date_from']), function ($q) {
                return $q->whereDate('created_at', '>=', $this->filters['date_from']);
            })
            ->when(isset($this->filters['date_to']), function ($q) {
                return $q->whereDate('created_at', '<=', $this->filters['date_to']);
            });
    }

    public function headings(): array
    {
        return [
            'شناسه',
            'کد کاربر',
            'نام کاربر',
            'گروه کاربری',
            'متن یادداشت',
            'ثبت‌کننده',
            'تاریخ ثبت',
            'وضعیت',
            'زمان مشاهده',
        ];
    }

    public function map($note): array
    {
        return [
            $note->id,
            optional($note->user)->kod,
            trim(optional($note->user)->name . ' ' . optional($note->user)->name2),
            optional(optional($note->user)->group)->name,
            $note->content,
            optional($note->admin)->name,
            $note->created_at->format('Y-m-d H:i'),
            $note->status === 'approved' ? 'تایید شده' : 'در حال بررسی',
            $note->seen_by_user_at ? $note->seen_by_user_at->format('Y-m-d H:i') : 'مشاهده نشده',
        ];
    }
}
