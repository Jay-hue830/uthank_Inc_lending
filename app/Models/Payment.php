<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function paymentSchedules()
    {
        return $this->belongsToMany(PaymentSchedule::class, 'paid_payment_schedules')
            ->using(PaidPaymentSchedule::class)
            ->withPivot(['amount', 'principal', 'interest'])
            ->withTimestamps();
    }
}
