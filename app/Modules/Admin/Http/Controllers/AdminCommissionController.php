<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Modules\Admin\Http\Requests\ScheduleCommissionRequest;
use App\Modules\Admin\Services\ChangeCommissionSchedule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class AdminCommissionController extends Controller
{
    use AuthorizesRequests;

    public function store(ScheduleCommissionRequest $request, Tenant $tenant, ChangeCommissionSchedule $service): RedirectResponse
    {
        $data = $request->validated();
        $service->handle($tenant, (float) $data['commission_rate'], Carbon::parse($data['effective_at']));

        return back()->with('status', 'Jadwal komisi disimpan.');
    }
}
