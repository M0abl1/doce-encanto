<?php

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public static function valid(): array
    {
        return [
            ['89,90', 8990], ['89.90', 8990], ['89', 8900], ['89,9', 8990], ['1.299,90', 129990],
            ['1.299', 129900], ['R$ 54,5', 5450], ['1299,90', 129990], ['R$1.000.000,00', 100000000],
        ];
    }

    public static function invalid(): array
    {
        return [['1.234.56'], ['12,345'], ['abc'], [''], ['-10'], ['1,2,3'], ['10.5.0'], ['1.23,45']];
    }

    #[DataProvider('valid')]
    public function test_parse_valid(string $input, int $expected): void
    {
        $this->assertSame($expected, Money::parse($input));
    }

    #[DataProvider('invalid')]
    public function test_parse_invalid(string $input): void
    {
        $this->assertNull(Money::parse($input));
    }

    public function test_format(): void
    {
        $this->assertSame('R$ 1.299,90', Money::format(129990));
        $this->assertSame('R$ 0,90', Money::format(90));
    }
}
