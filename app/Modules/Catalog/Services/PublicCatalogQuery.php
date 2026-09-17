<?php

namespace App\Modules\Catalog\Services; // Sesuaikan namespace jika foldernya berbeda

use App\Models\Canteen;
use App\Models\Menu;

class PublicCatalogQuery
{
    public function getAvailableMenus(Canteen $canteen)
    {
        // 1. Lepas global scope agar bisa mengambil dari banyak tenant
        // 2. Wajib pasang filter pengganti: hanya dari tenant di kantin ini yang aktif
        return Menu::withoutGlobalScopes()
            ->whereHas('tenant', function ($query) use ($canteen) {
                // Asumsi tabel tenant memiliki kolom canteen_id dan status
                $query->where('canteen_id', $canteen->id)
                    ->where('status', 'active');
            })
            // Tambahkan filter status menu jika ada, misal:
            // ->where('status', 'available')
            ->get();
    }
}
