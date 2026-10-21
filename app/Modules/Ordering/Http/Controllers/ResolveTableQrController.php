<?php

namespace App\Modules\Ordering\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Services\TenantOpeningHours;
use App\Modules\Ordering\Services\ResolveTableScan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTableQrController extends Controller
{
    public function __construct(private ResolveTableScan $resolver, private TenantOpeningHours $hours) {}

    public function __invoke(Request $request, string $token): RedirectResponse|Response
    {
        $result = $this->resolver->resolve($token);
        if ($result === null) {
            return response()->view('ordering::scan-invalid', [], 404);
        }
        if ($result['status'] === 'closed') {
            return response()->view('ordering::canteen-closed', [
                'canteen' => $result['canteen'], 'table' => $result['table'],
                'tenants' => $this->hours->activeTenants($result['canteen']), 'hours' => $this->hours,
            ]);
        }
        $cookie = cookie('customer_session', $result['plain'], 240, '/', null, $request->isSecure() || app()->isProduction(), true, false, 'lax');

        return redirect()->route('customer.welcome', ['canteen' => $result['canteen']->slug])->withCookie($cookie);
    }
}
