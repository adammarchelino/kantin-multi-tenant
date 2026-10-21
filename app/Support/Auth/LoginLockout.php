<?php

namespace App\Support\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class LoginLockout
{
    public const MAX_FAILURES = 5;

    public const FAILURE_WINDOW_SECONDS = 600;

    public const LOCK_SECONDS = 900;

    private function key(string $email): string
    {
        return 'auth:login:'.hash('sha256', Str::lower(trim($email)));
    }

    public function locked(string $email): bool
    {
        return Cache::has($this->key($email).':locked');
    }

    public function fail(string $email): void
    {
        $key = $this->key($email);
        Cache::add($key.':failures', 0, self::FAILURE_WINDOW_SECONDS);
        $count = (int) Cache::increment($key.':failures');
        if ($count > self::MAX_FAILURES) {
            Cache::put($key.':locked', true, self::LOCK_SECONDS);
        }
    }

    public function clear(string $email): void
    {
        Cache::forget($this->key($email).':failures');
        Cache::forget($this->key($email).':locked');
    }
}
