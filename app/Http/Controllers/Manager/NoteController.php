<?php

namespace App\Http\Controllers\Manager;

use App\manager;
use App\note;
use App\user;
use App\grop;
use App\usergrop;
use App\user_grop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    private function requireManager()
    {
        if (!session()->has('manager') || !session()->has('id_manager')) {
            abort(redirect('/manager/panel'));
        }
        if (!\App\Http\Controllers\managerController::managerAccessLevel(79)) {
            abort(redirect('/manager'));
        }
    }

    private function getManagerRecord(): ?manager
    {
        return manager::where('id_user', session('id_manager'))->first();
    }

    private function groupUserIds(): array
    {
        $mgr = $this->getManagerRecord();
        if (!$mgr) {
            return [];
        }

        return user_grop::where('grop_id', $mgr->id_grop)
            ->pluck('user_id')
            ->toArray();
    }

    public function index(Request $request)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();

        $pendingQuery = note::with(['user'])
            ->whereIn('user_id', $userIds)
            ->where('status', 'pending')
            ->where('is_archived', false);

        $unviewedQuery = note::with(['user'])
            ->whereIn('user_id', $userIds)
            ->whereNull('seen_by_user_at')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->where('visibility', 'admin_and_user');

        $viewedQuery = note::with(['user'])
            ->whereIn('user_id', $userIds)
            ->whereNotNull('seen_by_user_at')
            ->where('is_archived', false);

        // Task 3: archive query scoped to manager's group
        $archivedQuery = note::with(['user'])
            ->whereIn('user_id', $userIds)
            ->where('is_archived', true);

        // Apply common filters to all queries
        $this->applyFilters($pendingQuery, $request, $userIds);
        $this->applyFilters($unviewedQuery, $request, $userIds);
        $this->applyFilters($viewedQuery, $request, $userIds);
        $this->applyFilters($archivedQuery, $request, $userIds);

        // Task 1: preserve active tab after filter submission
        $activeTab = $request->input('active_tab',
            $request->has('pending_page') ? 'pending'
            : ($request->has('viewed_page') ? 'viewed'
            : ($request->has('archived_page') ? 'archived' : 'unviewed'))
        );

        $mgr = $this->getManagerRecord();
        $usergrops = usergrop::where('id_grop', $mgr ? $mgr->id_grop : 0)->get(['id', 'name']);

        return view('manager.notes.index', [
            'pendingNotes'  => $pendingQuery->latest()->paginate(20, ['*'], 'pending_page'),
            'unviewedNotes' => $unviewedQuery->latest()->paginate(20, ['*'], 'unviewed_page'),
            'viewedNotes'   => $viewedQuery->latest()->paginate(20, ['*'], 'viewed_page'),
            'archivedNotes' => $archivedQuery->latest()->paginate(20, ['*'], 'archived_page'),
            'activeTab'     => $activeTab,
            'usergrops'     => $usergrops,
        ]);
    }

    private function applyFilters($query, Request $request, array $groupUserIds)
    {
        if ($request->filled('user_code')) {
            $val = $request->user_code;
            $filteredIds = user::withoutGlobalScopes()
                ->whereIn('id', $groupUserIds)
                ->where(function ($q) use ($val) {
                    $q->where('id', $val)->orWhere('hash', $val);
                })
                ->pluck('id')
                ->toArray();
            $query->whereIn('user_id', $filteredIds);
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
        if ($request->filled('usergrop_id')) {
            $ugId = (int) $request->usergrop_id;
            $filteredIds = user_grop::where('id_usergrop', $ugId)
                ->whereIn('user_id', $groupUserIds)
                ->pluck('user_id')
                ->toArray();
            $query->whereIn('user_id', $filteredIds);
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }
    }

    public function create()
    {
        $this->requireManager();

        $mgr = $this->getManagerRecord();
        if (!$mgr) {
            return back()->with('error', 'اطلاعات مجموعه مدیر یافت نشد.');
        }

        $usergrops = usergrop::where('id_grop', $mgr->id_grop)->get(['id', 'name']);
        $grop      = grop::find($mgr->id_grop);

        return view('manager.notes.create', compact('usergrops', 'grop'));
    }

    public function store(Request $request)
    {
        $this->requireManager();

        $request->validate(['content' => 'required|string|max:5000']);

        $userIds = $this->resolveTargetUsers($request);

        if (empty($userIds)) {
            return back()->withErrors(['target' => 'هیچ کاربری انتخاب نشده است.'])->withInput();
        }

        foreach ($userIds as $userId) {
            note::create([
                'user_id'       => $userId,
                'admin_id'      => null,
                'content'       => $request->content,
                'is_mandatory'  => filter_var($request->input('is_mandatory', false), FILTER_VALIDATE_BOOLEAN),
                'reminder_date' => $request->reminder_date ?: null,
                'status'        => 'pending',
                'visibility'    => $request->input('visibility', 'admin_only'),
            ]);
        }

        return redirect()->route('manager.notes.index')->with('success', 'یادداشت با موفقیت ثبت شد و در انتظار تایید است.');
    }

    public function userInfo(Request $request)
    {
        $this->requireManager();

        $groupUserIds = $this->groupUserIds();
        $identifier   = $request->input('id');

        $user = user::withoutGlobalScopes()
            ->whereIn('id', $groupUserIds)
            ->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)->orWhere('hash', $identifier);
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
            'usergrop'    => optional(optional($pivot)->usergrop)->name,
        ]);
    }

    private function resolveTargetUsers(Request $request): array
    {
        $groupUserIds = $this->groupUserIds();

        if ($request->filled('user_ids') && is_array($request->user_ids)) {
            $ids = array_values(array_filter($request->user_ids));
            return array_values(array_intersect(array_map('intval', $ids), $groupUserIds));
        }

        if ($request->filled('group_ids') && is_array($request->group_ids)) {
            return user_grop::whereIn('id_usergrop', $request->group_ids)
                ->whereIn('user_id', $groupUserIds)
                ->pluck('user_id')
                ->unique()
                ->values()
                ->toArray();
        }

        return $groupUserIds;
    }

    public function approve($id)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();
        $note    = note::whereIn('user_id', $userIds)->findOrFail($id);
        $note->update(['status' => 'approved']);

        return redirect()->route('manager.notes.index', ['active_tab' => 'unviewed'])->with('success', 'یادداشت تایید و فعال شد.');
    }

    public function toggleVisibility($id)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();
        $note    = note::whereIn('user_id', $userIds)->findOrFail($id);
        $note->update([
            'visibility' => $note->visibility === 'admin_and_user' ? 'admin_only' : 'admin_and_user',
        ]);

        return back()->with('success', 'نوع نمایش تغییر کرد.');
    }

    // Task 3: toggle archive for manager's group notes
    public function toggleArchive($id)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();
        $note    = note::whereIn('user_id', $userIds)->findOrFail($id);
        $note->update(['is_archived' => !$note->is_archived]);

        return back()->with('success', 'وضعیت بایگانی تغییر کرد.');
    }

    public function resetViewed($id)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();
        $note    = note::whereIn('user_id', $userIds)->findOrFail($id);
        $note->update(['seen_by_user_at' => null]);

        return back()->with('success', 'وضعیت مشاهده پیام بازنشانی شد. کاربر مجدداً این پیام را خواهد دید.');
    }

    public function destroy($id)
    {
        $this->requireManager();

        $userIds = $this->groupUserIds();
        note::whereIn('user_id', $userIds)->findOrFail($id)->delete();

        return back()->with('success', 'پیام حذف شد.');
    }

    public function bulkApprove(Request $request)
    {
        $this->requireManager();

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return back()->with('error', 'هیچ یادداشتی انتخاب نشده است.');
        }

        $userIds = $this->groupUserIds();
        note::whereIn('user_id', $userIds)->whereIn('id', $ids)->where('status', 'pending')->update(['status' => 'approved']);
        return redirect()->route('manager.notes.index', ['active_tab' => 'unviewed'])->with('success', count($ids) . ' یادداشت تایید شد.');
    }

    public function bulkDestroy(Request $request)
    {
        $this->requireManager();

        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if (empty($ids)) {
            return back()->with('error', 'هیچ یادداشتی انتخاب نشده است.');
        }

        $userIds = $this->groupUserIds();
        $deleted = note::whereIn('user_id', $userIds)->whereIn('id', $ids)->delete();
        
        return back()->with('success', $deleted . ' یادداشت با موفقیت حذف شد.');
    }
    public function edit($id)
    {
        $this->requireManager();
        $userIds = $this->groupUserIds();
        $note = note::whereIn('user_id', $userIds)->findOrFail($id);

        if ($note->visibility === 'admin_and_user' && $note->seen_by_user_at) {
            return back()->with('error', 'این یادداشت توسط کاربر مشاهده شده و قابل ویرایش نیست.');
        }

        return view('manager.notes.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $this->requireManager();
        $userIds = $this->groupUserIds();
        $note = note::whereIn('user_id', $userIds)->findOrFail($id);

        if ($note->visibility === 'admin_and_user' && $note->seen_by_user_at) {
            return back()->with('error', 'این یادداشت توسط کاربر مشاهده شده و قابل ویرایش نیست.');
        }

        $request->validate([
            'content' => 'required|string',
            'is_mandatory' => 'nullable|boolean',
            'reminder_date' => 'nullable|date',
            'visibility' => 'nullable|in:admin_only,admin_and_user'
        ]);

        $note->content = $request->input('content');
        $note->is_mandatory = $request->has('is_mandatory');
        $note->reminder_date = $request->input('reminder_date');
        if ($request->has('visibility')) {
            $note->visibility = $request->input('visibility');
        }

        $note->save();

        return redirect()->route('manager.notes.index')->with('success', 'یادداشت با موفقیت ویرایش شد.');
    }
}

