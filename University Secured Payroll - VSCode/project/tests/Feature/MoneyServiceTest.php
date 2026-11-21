<?php
namespace Tests\Feature;
use App\Services\Money;
use PHPUnit\Framework\TestCase;
class MoneyServiceTest extends TestCase
{
    public function test_salary_calculation_uses_exact_kobo_arithmetic(): void
    {
        $this->assertSame(['gross'=>'305000.00','net'=>'275000.00'],Money::calculate('245000.00','60000.00','30000.00'));
        $this->assertSame(101,Money::toKobo('1.01'));
        $this->assertSame('0.01',Money::fromKobo(1));
    }
    public function test_deductions_cannot_exceed_gross_salary(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::calculate('100.00','0.00','101.00');
    }
}
