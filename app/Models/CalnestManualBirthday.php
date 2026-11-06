<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalnestManualBirthday extends Model
{
    use HasFactory;

    protected $table = 'calnest_manual_birthdays';

    protected $fillable = [
        'user_id',
        'name',
        'date',
        'phone',
        'relationship',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toResponseArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'date' => (string) $this->date,
            'phone' => $this->phone,
            'relationship' => $this->relationship,
            'notes' => $this->notes,
        ];
    }

    public function getCleanPhone(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        return preg_replace('/[^0-9]/', '', $this->phone);
    }

    public function getWhatsAppLink(string $message): ?string
    {
        $cleanPhone = $this->getCleanPhone();
        if (!$cleanPhone) {
            return null;
        }
        return 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($message);
    }

    public function getSmsLink(string $message): ?string
    {
        $phone = $this->phone;
        if (!$phone) {
            return null;
        }
        return 'sms:' . rawurlencode($phone) . '?body=' . rawurlencode($message);
    }
}
