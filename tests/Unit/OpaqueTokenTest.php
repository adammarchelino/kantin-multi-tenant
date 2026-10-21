<?php

namespace Tests\Unit;

use App\Support\Tokens\OpaqueToken;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OpaqueTokenTest extends TestCase
{
    public function test_token_is_random_url_safe_and_hashes_to_32_bytes(): void
    {
        $first = OpaqueToken::issue();
        $second = OpaqueToken::issue();
        $this->assertNotSame($first['plain'], $second['plain']);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $first['plain']);
        $this->assertSame(32, strlen($first['hash']));
        $this->assertSame($first['hash'], OpaqueToken::hash($first['plain']));
    }

    public function test_rejects_less_than_128_bits_of_entropy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        OpaqueToken::issue(15);
    }
}
