<?php

namespace App\Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Modules\Catalog\Services\TenantOpeningHours;
use App\Modules\Ordering\Services\ResolveCustomerSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class CustomerWelcomeController extends Controller
{
    public function __construct(private ResolveCustomerSession $sessions, private TenantOpeningHours $hours) {}

    public function __invoke(Request $request, string $canteen): View
    {
        $model = Canteen::where('slug', $canteen)->where('status', 'active')->firstOrFail();
        $session = $this->sessions->current($request);
        $table = $session?->canteen_id === $model->id ? $session->diningTable : null;

        return view('ordering::welcome', ['canteen' => $model, 'table' => $table, 'tenants' => $this->hours->activeTenants($model), 'hours' => $this->hours]);
    }
}
