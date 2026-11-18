<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PaidPaymentSchedule extends Pivot
{
    protected $table = 'paid_payment_schedules';

    public $incrementing = true;

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }
}
