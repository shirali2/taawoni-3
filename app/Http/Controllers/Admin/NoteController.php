<?php

namespace App\Http\Controllers\Admin;

use App\grop;
use App\note;
use App\user;
use App\usergrop;
use App\user_grop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NotesExport;
use App\Imports\NotesImport;

class NoteController extends Controller
{
    private function requireAdmin()
    {
        if (!session()->has('admin')) {
            abort(redirect('/admin'));
        }
    }

    public function index(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.list');

        $pendingQuery   = note::with(['user.group', 'admin'])->pending()->notArchived();
        $unviewedQuery  = note::with(['user.group', 'admin'])->approved()->notArchived()->whereNull('seen_by_user_at');
        $viewedQuery    = note::with(['user.group', 'admin'])->approved()->notArchived()->whereNotNull('seen_by_user_at');
        $archivedQuery  = note::with(['user.group', 'admin'])->where('is_archived', true);

        $this->applyFilters($pendingQuery, $request);
        $this->applyFilters($unviewedQuery, $request);
        $this->applyFilters($viewedQuery, $request);
        $this->applyFilters($archivedQuery, $request);

        // Task 1: preserve active tab after filter submission
        $activeTab = $request->input('active_tab',
            $request->has('archived_page') ? 'archived'
            : ($request->has('viewed_page') ? 'viewed'
            : ($request->has('unviewed_page') ? 'unviewed' : 'pending'))
        );

        $grops = grop::orderBy('name')->get(['id', 'name']);

        return view('admin.notes.index', [
            'pendingNotes'  => $pendingQuery->latest()->paginate(20, ['*'], 'pending_page'),
            'unviewedNotes' => $unviewedQuery->latest()->paginate(20, ['*'], 'unviewed_page'),
            'viewedNotes'   => $viewedQuery->latest()->paginate(20, ['*'], 'viewed_page'),
            'archivedNotes' => $archivedQuery->latest()->paginate(20, ['*'], 'archived_page'),
            'activeTab'     => $activeTab,
            'grops'         => $grops,
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('user_code')) {
            $val = $request->user_code;
            $query->whereHas('user', function ($u) use ($val) {
                $u->where('id', $val)->orWhere('hash', $val);
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('has_reminder')) {
            $query->whereNotNull('reminder_date');
        }
        if ($request->filled('grop_ids') && is_array($request->grop_ids)) {
            $ids = array_filter(array_map('intval', $request->grop_ids));
            if (!empty($ids)) {
                $query->whereHas('user.user_grops', function ($q) use ($ids) {
                    $q->whereIn('grop_id', $ids);
                });
            }
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }
    }

    public function create()
    {
        $this->requireAdmin();
        $this->authorize('notes.create');

        $grops    = grop::all(['id', 'name']);
        $usergrops = usergrop::all(['id', 'name', 'id_grop']);
        return view('admin.notes.create', compact('grops', 'usergrops'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.create');

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $userIds = $this->resolveTargetUsers($request);

        if (empty($userIds)) {
            return back()->withErrors(['user_ids' => 'هیچ کاربری انتخاب نشده است.'])->withInput();
        }

        foreach ($userIds as $userId) {
            note::create([
                'user_id'       => $userId,
                'admin_id'      => session('id'),
                'content'       => $request->content,
                'is_mandatory'  => filter_var($request->input('is_mandatory', false), FILTER_VALIDATE_BOOLEAN),
                'reminder_date' => $request->reminder_date ?: null,
                'status'        => 'pending',
                'visibility'    => $request->input('visibility', 'admin_only'),
            ]);
        }

        return redirect()->route('admin.notes.index')->with('success', 'یادداشت با موفقیت ثبت شد.');
    }

    public function edit($id)
    {
        $this->requireAdmin();
        $this->authorize('notes.edit');

        $note = note::findOrFail($id);

        if ($note->visibility === 'admin_and_user' && $note->seen_by_user_at) {
            return back()->with('error', 'این یادداشت توسط کاربر مشاهده شده و قابل ویرایش نیست.');
        }

        return view('admin.notes.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $this->requireAdmin();
        $this->authorize('notes.edit');

        $note = note::findOrFail($id);

        if ($note->visibility === 'admin_and_user' && $note->seen_by_user_at) {
            return back()->with('error', 'این یادداشت توسط کاربر مشاهده شده و قابل ویرایش نیست.');
        }

        $request->validate(['content' => 'required|string|max:5000']);

        $note->update([
            'content'       => $request->content,
            'is_mandatory'  => filter_var($request->input('is_mandatory', false), FILTER_VALIDATE_BOOLEAN),
            'reminder_date' => $request->reminder_date ?: null,
        ]);

        return redirect()->route('admin.notes.index')->with('success', 'یادداشت ویرایش شد.');
    }

    public function userInfo(Request $request)
    {
        if (!session()->has('admin')) {
            return response()->json(['found' => false], 401);
        }

        $identifier = $request->input('id');

        $user = user::withoutGlobalScopes()
            ->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('hash', $identifier)
                  ->orWhere('mobile', $identifier);
            })
            ->with(['user_grops' => function ($q) {
                $q->with(['grop', 'usergrop']);
            }])
            ->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        $pivot = $user->user_grops->first();

        return response()->json([
            'found'       => true,
            'name'        => trim($user->name . ' ' . $user->name2),
            'id'          => $user->id,
            'hash'        => $user->hash ?? '',
            'lawyer_name' => $user->lawyer_name ?? '',
            'mobile'      => $user->mobile ?? '',
            'group'       => optional(optional($pivot)->grop)->name,
            'usergrop'    => optional(optional($pivot)->usergrop)->name,
        ]);
    }

    public function approve($id)
    {
        $this->requireAdmin();
        $this->authorize('notes.approve');

        note::findOrFail($id)->update(['status' => 'approved']);
        return redirect()->route('admin.notes.index', ['active_tab' => 'unviewed'])->with('success', 'یادداشت تایید شد.');
    }

    public function approveAll()
    {
        $this->requireAdmin();
        $this->authorize('notes.approve_all');

        note::pending()->notArchived()->update(['status' => 'approved']);
        return redirect()->route('admin.notes.index', ['active_tab' => 'unviewed'])->with('success', 'همه یادداشت‌های در انتظار تایید شدند.');
    }

    public function toggleArchive($id)
    {
        $this->requireAdmin();
        $this->authorize('notes.toggle_archive');

        $note = note::findOrFail($id);
        $note->update(['is_archived' => !$note->is_archived]);
        return back()->with('success', 'وضعیت بایگانی تغییر کرد.');
    }

    public function toggleVisibility($id)
    {
        $this->requireAdmin();
        $this->authorize('notes.toggle_visibility');

        $note = note::findOrFail($id);
        $note->update([
            'visibility' => $note->visibility === 'admin_and_user' ? 'admin_only' : 'admin_and_user',
        ]);
        return back()->with('success', 'نوع نمایش تغییر کرد.');
    }

    public function destroy($id)
    {
        $this->requireAdmin();
        $this->authorize('notes.delete');

        note::findOrFail($id)->delete();
        return back()->with('success', 'یادداشت حذف شد.');
    }

    public function bulkApprove(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.approve');

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return back()->with('error', 'هیچ یادداشتی انتخاب نشده است.');
        }

        note::whereIn('id', $ids)->pending()->update(['status' => 'approved']);
        return redirect()->route('admin.notes.index', ['active_tab' => 'unviewed'])->with('success', count($ids) . ' یادداشت تایید شد.');
    }

    // Task 6: bulk delete notes
    public function bulkDestroy(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.delete');

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return back()->with('error', 'هیچ یادداشتی انتخاب نشده است.');
        }

        note::whereIn('id', $ids)->delete();
        return back()->with('success', count($ids) . ' یادداشت حذف شد.');
    }

    // Task 7: per-user note report (HTML printable)
    public function userReport(Request $request, $userId)
    {
        $this->requireAdmin();
        $this->authorize('notes.report_export');

        $targetUser = user::withoutGlobalScopes()->findOrFail($userId);

        $publicNotes = note::with(['admin'])
            ->where('user_id', $userId)
            ->where('visibility', 'admin_and_user')
            ->notArchived()
            ->latest()
            ->get();

        $privateNotes = note::with(['admin'])
            ->where('user_id', $userId)
            ->where('visibility', 'admin_only')
            ->notArchived()
            ->latest()
            ->get();

        return view('admin.notes.user_report', compact('targetUser', 'publicNotes', 'privateNotes'));
    }

    // Task 7: per-user note report (PDF download)
    public function userReportPdf(Request $request, $userId)
    {
        $this->requireAdmin();
        $this->authorize('notes.report_export');

        $targetUser = user::withoutGlobalScopes()->findOrFail($userId);

        $publicNotes = note::with(['admin'])
            ->where('user_id', $userId)
            ->where('visibility', 'admin_and_user')
            ->notArchived()
            ->latest()
            ->get();

        $privateNotes = note::with(['admin'])
            ->where('user_id', $userId)
            ->where('visibility', 'admin_only')
            ->notArchived()
            ->latest()
            ->get();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.notes.user_report', compact('targetUser', 'publicNotes', 'privateNotes'));
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        return $pdf->download('notes-user-' . $userId . '-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.report_export');

        $filters = $request->only(['date_from', 'date_to']);
        return Excel::download(new NotesExport($filters), 'notes-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.report_export');

        $notes = note::with(['user.group', 'admin'])->latest()->get();
        $pdf   = app('dompdf.wrapper');
        $pdf->loadView('admin.notes.pdf', compact('notes'));
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        return $pdf->download('notes-' . now()->format('Ymd') . '.pdf');
    }

    public function importForm()
    {
        $this->requireAdmin();
        $this->authorize('notes.create');

        return view('admin.notes.import');
    }

    public function importExcel(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('notes.create');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new NotesImport, $request->file('file'));
            return redirect()->route('admin.notes.index')->with('success', 'یادداشت‌ها با موفقیت ایمپورت شدند.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors   = [];
            foreach ($failures as $failure) {
                $errors[] = 'ردیف ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->withErrors(['import' => $errors]);
        }
    }

    private function resolveTargetUsers(Request $request): array
    {
        if ($request->filled('user_ids') && is_array($request->user_ids)) {
            return array_values(array_filter($request->user_ids));
        }

        if ($request->filled('group_ids') && is_array($request->group_ids)) {
            $gropIds = $request->input('grop_ids', []);
            return user_grop::whereIn('id_usergrop', $request->group_ids)
                ->when(!empty($gropIds), function ($q) use ($gropIds) {
                    return $q->whereIn('grop_id', $gropIds);
                })
                ->pluck('user_id')
                ->unique()
                ->values()
                ->toArray();
        }

        if ($request->filled('grop_ids') && is_array($request->grop_ids)) {
            return user_grop::whereIn('grop_id', $request->grop_ids)
                ->pluck('user_id')
                ->unique()
                ->values()
                ->toArray();
        }

        return [];
    }
}
