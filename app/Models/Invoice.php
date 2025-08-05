<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'patient_id',
        'note_id',
        'issued_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'issued_date'   => 'date',
        'due_date'      => 'date',
        'total_amount'  => 'decimal:2',
        'paid_amount'   => 'decimal:2',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                do {
                    $number = '#' . rand(10000, 99999);
                } while (self::where('invoice_number', $number)->exists());

                $invoice->invoice_number = $number;
            }
        });
    }
}
