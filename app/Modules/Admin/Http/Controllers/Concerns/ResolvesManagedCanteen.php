<?php

namespace App\Modules\Admin\Http\Controllers\Concerns;

use App\Models\Canteen;
use App\Models\UserCanteenRole;
use Illuminate\Http\Request;

trait ResolvesManagedCanteen
{
    protected function managedCanteen(Request $request): Canteen
    {
        $id = UserCanteenRole::where('user_id', $request->user()?->id)->whereIn('role', ['owner', 'manager', 'finance'])->value('canteen_id');
        abort_if($id === null, 403);

        return Canteen::findOrFail($id);
    }
}
