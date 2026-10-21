<?php

namespace App\Modules\Ordering\Services;

use App\Models\CustomerSession;
use App\Support\Tokens\OpaqueToken;
use Illuminate\Http\Request;

class ResolveCustomerSession
{
    public const COOKIE = 'customer_session';

    public function current(Request $request): ?CustomerSession
    {
        $token = $request->cookie(self::COOKIE);
        if (! is_string($token) || $token === '') {
            return null;
        }
        $session = CustomerSession::where('session_token_hash', OpaqueToken::hash($token))->with('diningTable')->first();

        return $session?->isActive() ? $session : null;
    }
}
