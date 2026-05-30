<?php

namespace App\Http\Controllers\Admin;

use App\grop;
use App\user;
use App\usergrop;
use App\user_grop;
use App\SmsCampaign;
use App\setting;
use App\Services\WalletService;
use App\Services\SmsVariableParser;
use App\Jobs\SendSmsBatch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class SmsCampaignController extends Controller
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
        $this->authorize('sms.list');

        $query = SmsCampaign::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('title_type')) {
            $query->where('title_type', $request->title_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $campaigns = $query->paginate(20);

        return view('admin.sms.index', compact('campaigns'));
    }

    public function create()
    {
        $this->requireAdmin();
        $this->authorize('sms.create');

        $grops      = grop::all(['id', 'name']);
        $usergrops  = usergrop::all(['id', 'name', 'id_grop']);
        $smsStatus  = config('sms.status', 'inactive');
        $costPerSms = (float)(optional(setting::where('name', 'sms_cost_per_unit')->first())->value ?? 0);

        return view('admin.sms.create', compact('grops', 'usergrops', 'smsStatus', 'costPerSms'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $this->authorize('sms.create');

        $request->validate([
            'title_type'   => 'required|in:advertising,ceo,informational,warning,reminder,other',
            'message_text' => 'required|string|max:10000',
        ]);

        $managerId   = session('id');
        $targetUsers = $this->resolveTargetUsers($request);
        $totalUsers  = count($targetUsers);

        if ($request->action === 'send' && $totalUsers === 0) {
            return back()->withErrors(['user_ids' => 'هیچ کاربری انتخاب نشده است.'])->withInput();
        }

        $costPerSms   = (float)(optional(setting::where('name', 'sms_cost_per_unit')->first())->value ?? 0);
        $charsPerSms  = 70;
        $textLength   = mb_strlen($request->message_text);
        $smsPerPerson = max(1, (int)ceil($textLength / $charsPerSms));
        $totalSms     = $totalUsers * $smsPerPerson;
        $totalCost    = $totalSms * $costPerSms;

        if ($request->action === 'send') {
            $minCount = (int)(optional(setting::where('name', 'sms_min_send_count')->first())->value ?? 1);
            $maxCount = (int)(optional(setting::where('name', 'sms_max_send_count')->first())->value ?? 10000);

            if ($totalUsers < $minCount || $totalUsers > $maxCount) {
                return back()->withErrors(['count' => "تعداد گیرندگان باید بین {$minCount} و {$maxCount} باشد."])->withInput();
            }

            $walletService = app(WalletService::class);
            if (!$walletService->canAfford($managerId, $totalCost)) {
                $balance  = $walletService->getBalance($managerId);
                $wallet   = \App\Wallet::where('manager_id', $managerId)->first();
                $shortage = $totalCost - $balance + optional($wallet)->min_threshold;
                return back()->withErrors(['wallet' => 'موجودی کافی نیست. کسری: ' . number_format($shortage) . ' ریال'])->withInput();
            }
        }

        $campaignNumber = SmsCampaign::where('manager_id', $managerId)->max('campaign_number') + 1;

        $campaign = SmsCampaign::create([
            'manager_id'            => $managerId,
            'campaign_number'       => $campaignNumber,
            'title_type'            => $request->title_type,
            'message_text'          => $request->message_text,
            'sender_name'           => $request->sender_name,
            'target_user_ids'       => $targetUsers,
            'target_group_ids'      => $request->input('group_ids', []),
            'target_collection_ids' => $request->input('grop_ids', []),
            'include_active'        => $request->has('include_active'),
            'include_inactive'      => $request->has('include_inactive'),
            'total_users'           => $totalUsers,
            'total_sms_per_user'    => $smsPerPerson,
            'total_sms_count'       => $totalSms,
            'cost_per_sms'          => $costPerSms,
            'total_cost'            => $totalCost,
            'status'                => $request->action === 'send' ? 'pending' : 'draft',
        ]);

        if ($request->action === 'send') {
            app(WalletService::class)->debit($managerId, $totalCost, "ارسال پیامک کمپین #{$campaign->id}");

            $parser     = app(SmsVariableParser::class);
            $recipients = [];

            foreach ($targetUsers as $userId) {
                $userObj = user::withoutGlobalScopes()->find($userId);
                if (!$userObj || !$userObj->mobile) continue;

                $invoice  = $userObj->sales_invoices()->latest()->first();
                $message  = $parser->parse($request->message_text, $userObj, $invoice);

                if ($request->sender_name) {
                    $message .= "\n" . $request->sender_name;
                }
                $message .= "\n" . (function_exists('jdate') ? jdate('Y/m/d') : date('Y/m/d'));

                $recipients[] = [
                    'mobile'  => $userObj->mobile,
                    'message' => $message,
                    'user_id' => $userId,
                ];
            }

            dispatch(new SendSmsBatch($recipients, $campaign->id));
            $campaign->update(['status' => 'sending']);
        }

        $msg = $request->action === 'send'
            ? 'پیامک‌ها در صف ارسال قرار گرفتند.'
            : 'پیش‌نویس ذخیره شد.';

        return redirect()->route('admin.sms.index')->with('success', $msg);
    }

    public function draftSave(Request $request)
    {
        if (!session()->has('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $managerId    = session('id');
        $userIds      = $this->resolveTargetUsers($request);
        $textLength   = mb_strlen($request->input('message_text', ''));
        $smsPerPerson = max(1, (int)ceil($textLength / 70));
        $totalUsers   = count($userIds);

        $campaignNumber = SmsCampaign::where('manager_id', $managerId)->max('campaign_number') + 1;

        $campaign = SmsCampaign::create([
            'manager_id'            => $managerId,
            'campaign_number'       => $campaignNumber,
            'title_type'            => $request->input('title_type', 'other'),
            'message_text'          => $request->input('message_text', ''),
            'sender_name'           => $request->input('sender_name'),
            'target_user_ids'       => $userIds,
            'target_group_ids'      => $request->input('group_ids', []),
            'target_collection_ids' => $request->input('grop_ids', []),
            'include_active'        => (bool)$request->input('include_active', true),
            'include_inactive'      => (bool)$request->input('include_inactive', false),
            'total_users'           => $totalUsers,
            'total_sms_per_user'    => $smsPerPerson,
            'total_sms_count'       => $totalUsers * $smsPerPerson,
            'status'                => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'id'      => $campaign->id,
            'message' => 'پیش‌نویس ذخیره شد.',
        ]);
    }

    public function recipients(Request $request, $id)
    {
        $this->requireAdmin();
        $this->authorize('sms.list');

        $campaign = SmsCampaign::findOrFail($id);

        $query = \App\SmsLog::where('sms_campaign_id', $id)
            ->with(['user.group'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            });

        $logs         = $query->paginate(50);
        $isSuperadmin = optional(session('admin'))->type === 'admin';

        return view('admin.sms.recipients', compact('campaign', 'logs', 'isSuperadmin'));
    }

    public function exportExcel($id)
    {
        $this->requireAdmin();
        $this->authorize('sms.list');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SmsCampaignExport($id),
            'sms-campaign-' . $id . '-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function exportPdf($id)
    {
        $this->requireAdmin();
        $this->authorize('sms.list');

        $campaign     = SmsCampaign::findOrFail($id);
        $logs         = \App\SmsLog::where('sms_campaign_id', $id)->with(['user.group'])->get();
        $isSuperadmin = optional(session('admin'))->type === 'admin';

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.sms.recipients-pdf', compact('campaign', 'logs', 'isSuperadmin'));
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        return $pdf->download('sms-campaign-' . $id . '-' . now()->format('Ymd') . '.pdf');
    }

    public function deleteLog($logId)
    {
        $this->requireAdmin();
        $this->authorize('sms.list');

        \App\SmsLog::findOrFail($logId)->delete();
        return back()->with('success', 'ردیف حذف شد.');
    }

    public function bulkDeleteLogs(Request $request, $id)
    {
        $this->requireAdmin();
        $this->authorize('sms.list');

        $ids = $request->input('log_ids', []);
        if (!empty($ids)) {
            \App\SmsLog::where('sms_campaign_id', $id)->whereIn('id', $ids)->delete();
        }
        return back()->with('success', count($ids) . ' ردیف حذف شد.');
    }

    public function smsSettings()
    {
        $this->requireAdmin();

        $keys = [
            'sms_driver', 'sms_api_key', 'sms_line_number', 'sms_cost_per_unit',
            'sms_start_hour', 'sms_end_hour', 'sms_min_send_count', 'sms_max_send_count',
            'sms_interval_seconds', 'sms_notify_ticket', 'sms_notify_form', 'sms_system_status',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = optional(setting::where('name', $key)->first())->input1;
        }

        return view('admin.sms.settings', compact('settings'));
    }

    public function smsSettingsSave(Request $request)
    {
        $this->requireAdmin();

        $keys = [
            'sms_driver', 'sms_api_key', 'sms_line_number', 'sms_cost_per_unit',
            'sms_start_hour', 'sms_end_hour', 'sms_min_send_count', 'sms_max_send_count',
            'sms_interval_seconds', 'sms_notify_ticket', 'sms_notify_form', 'sms_system_status',
        ];

        foreach ($keys as $key) {
            setting::updateOrCreate(['name' => $key], ['input1' => $request->input($key)]);
        }

        return back()->with('success', 'تنظیمات پیامک ذخیره شد.');
    }

    public function destroy($id)
    {
        $this->requireAdmin();
        $this->authorize('sms.create');

        SmsCampaign::findOrFail($id)->delete();
        return back()->with('success', 'کمپین حذف شد.');
    }

    private function resolveTargetUsers(Request $request): array
    {
        $includeActive   = $request->has('include_active');
        $includeInactive = $request->has('include_inactive');

        // When called from draftSave (JSON/AJAX), check differently
        if (!$includeActive && !$includeInactive) {
            $includeActive = (bool)$request->input('include_active', true);
            $includeInactive = (bool)$request->input('include_inactive', false);
        }

        if ($request->filled('user_ids') && is_array($request->user_ids)) {
            $ids = array_values(array_filter($request->user_ids));
            return $this->filterByActiveStatus($ids, $includeActive, $includeInactive);
        }

        if ($request->filled('group_ids') && is_array($request->group_ids)) {
            $gropIds = $request->input('grop_ids', []);
            $ids = user_grop::whereIn('id_usergrop', $request->group_ids)
                ->when(!empty($gropIds), function ($q) use ($gropIds) {
                    return $q->whereIn('grop_id', $gropIds);
                })
                ->pluck('user_id')
                ->unique()
                ->values()
                ->toArray();
            return $this->filterByActiveStatus($ids, $includeActive, $includeInactive);
        }

        if ($request->filled('grop_ids') && is_array($request->grop_ids)) {
            $ids = user_grop::whereIn('grop_id', $request->grop_ids)
                ->pluck('user_id')
                ->unique()
                ->values()
                ->toArray();
            return $this->filterByActiveStatus($ids, $includeActive, $includeInactive);
        }

        return [];
    }

    private function filterByActiveStatus(array $userIds, bool $includeActive, bool $includeInactive): array
    {
        if ($includeActive && $includeInactive) {
            return $userIds;
        }

        if (!$includeActive && !$includeInactive) {
            return [];
        }

        $query = user::withoutGlobalScopes()->whereIn('id', $userIds);

        if ($includeActive && !$includeInactive) {
            $query->where('inactive', 0);
        } elseif (!$includeActive && $includeInactive) {
            $query->where('inactive', 1);
        }

        return $query->pluck('id')->toArray();
    }
}
