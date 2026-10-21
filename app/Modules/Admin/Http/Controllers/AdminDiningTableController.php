<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Modules\Admin\Http\Controllers\Concerns\ResolvesManagedCanteen;
use App\Modules\Admin\Services\AuditLogger;
use App\Modules\Admin\Services\QrCodeSvg;
use App\Modules\Admin\Services\QrTokenService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class AdminDiningTableController extends Controller
{
    use AuthorizesRequests, ResolvesManagedCanteen;

    public function __construct(private QrTokenService $qr, private AuditLogger $audit, private QrCodeSvg $qrCode) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', DiningTable::class);
        $canteen = $this->managedCanteen($request);
        $tables = DiningTable::where('canteen_id', $canteen->id)->with('activeToken')->orderBy('code')->get();

        return view('admin::tables.index', compact('canteen', 'tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', DiningTable::class);
        $canteen = $this->managedCanteen($request);
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:30', Rule::unique('dining_tables')->where(fn ($q) => $q->where('canteen_id', $canteen->id))],
            'label' => ['required', 'string', 'max:60'], 'zone' => ['nullable', 'string', 'max:60'],
        ]);
        DiningTable::create([...$data, 'canteen_id' => $canteen->id, 'status' => 'active']);

        return to_route('admin.tables.index')->with('status', 'Meja berhasil ditambahkan.');
    }

    public function rotate(DiningTable $table): RedirectResponse
    {
        $this->authorize('rotateQr', $table);
        $plain = $this->qr->rotate($table);

        return to_route('admin.tables.qr', $table)->with('qr_plain', $plain);
    }

    public function qr(DiningTable $table): View
    {
        $this->authorize('rotateQr', $table);
        $plain = session('qr_plain');
        $url = is_string($plain) ? route('customer.scan', ['token' => $plain]) : null;
        $svg = $url ? $this->qrCode->render($url) : null;

        return view('admin::tables.qr', compact('table', 'url', 'svg'));
    }

    public function status(Request $request, DiningTable $table): RedirectResponse
    {
        $this->authorize('update', $table);
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'inactive'])]]);
        DB::transaction(function () use ($table, $data): void {
            $before = ['status' => $table->status];
            $table->update($data);
            $this->audit->record('dining_table', $table->id, $data['status'] === 'active' ? 'activated' : 'deactivated', $before, $data, null, $table->canteen_id);
        });

        return to_route('admin.tables.index')->with('status', 'Status meja diperbarui.');
    }
}
