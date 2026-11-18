<?php

namespace Tests\Feature;

use App\Models\Debtor;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoanPaymentRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_debtor_loans_schedules_and_payment_pivot_are_related(): void
    {
        $debtorId = DB::table('debtors')->insertGetId([
            'fname' => 'Taylor',
            'lname' => 'Example',
            'baranggay' => 'Central',
            'town' => 'Sampletown',
            'province' => 'Sampleprovince',
            'email' => 'taylor@example.test',
            'cell_no' => '09123456789',
            'reference' => 'Colleague',
            'reference_no' => '09112223334',
        ]);
        $loanId = DB::table('loans')->insertGetId([
            'debtor_id' => $debtorId,
            'type' => 'monthly',
            'principal' => 1000,
            'terms' => '3 months',
            'interest_rate' => 5,
            'start_date' => '2026-01-01',
            'end_date' => '2026-04-01',
            'status' => 'approved',
            'release_date' => '2026-01-01',
        ]);
        $scheduleId = DB::table('payment_schedules')->insertGetId([
            'loan_id' => $loanId,
            'date' => '2026-02-01',
            'payable' => 350,
            'interest' => 50,
            'balance' => 700,
        ]);
        $paymentId = DB::table('payments')->insertGetId([
            'recipt_no' => 1001,
            'paid_at' => '2026-02-01 10:00:00',
            'method' => 'cash',
            'amount' => 350,
            'Aplacador' => 'Cashier',
        ]);

        Payment::findOrFail($paymentId)->paymentSchedules()->attach($scheduleId, [
            'amount' => 350,
            'principal' => 300,
            'interest' => 50,
        ]);

        $debtor = Debtor::findOrFail($debtorId);
        $loan = Loan::findOrFail($loanId);
        $schedule = PaymentSchedule::findOrFail($scheduleId);
        $payment = Payment::findOrFail($paymentId);

        $this->assertTrue($debtor->loans()->whereKey($loanId)->exists());
        $this->assertTrue($loan->paymentSchedules()->whereKey($scheduleId)->exists());
        $this->assertTrue($schedule->payments()->whereKey($paymentId)->exists());
        $this->assertEquals(350, $payment->paymentSchedules()->firstOrFail()->pivot->amount);
    }
}
