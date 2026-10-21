<x-layouts.admin>
    <main class="mx-auto max-w-3xl space-y-6 p-6"><a class="text-blue-700 underline" href="{{ route('admin.tenants.index') }}">← Kembali</a><h1 class="text-2xl font-semibold">Daftarkan tenant</h1>
        @if ($errors->any())<div class="rounded bg-red-50 p-3 text-red-800">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.tenants.store') }}" class="grid gap-4 rounded border p-5 sm:grid-cols-2">@csrf
            @foreach ([['display_name','Nama tenant'],['code','Kode'],['slug','Slug'],['commission_rate','Komisi (%)'],['pic_name','Nama penanggung jawab'],['pic_email','Email penanggung jawab'],['bank_code','Kode bank'],['account_holder','Nama pemilik rekening'],['account_number','Nomor rekening']] as [$name,$label])<label class="grid gap-1 text-sm">{{ $label }}<input class="rounded border-zinc-300" name="{{ $name }}" value="{{ old($name) }}" @if($name === 'pic_email') type="email" @elseif($name === 'commission_rate') type="number" step="0.01" min="0" max="100" @else type="text" @endif required></label>@endforeach
            <div class="sm:col-span-2"><button class="rounded bg-blue-700 px-4 py-2 text-white">Simpan tenant</button></div>
        </form>
    </main>
</x-layouts.admin>
