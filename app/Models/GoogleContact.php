<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleContact extends Model
{
    use HasFactory;

    protected $table = 'google_contacts';
    public $incrementing = false;
    protected $primaryKey = 'resourceName';
    protected $keyType = 'string';

    protected $fillable = [
        'resourceName',
        'displayName',
        'givenName',
        'familyName',
        'birthday_year',
        'birthday_month',
        'birthday_day',
        'phone_value',
        'phone_type',
        'photo_url',
    ];

    public function toGoogleArray(): array
    {
        $birthdayDate = [];
        if ($this->birthday_year !== null) {
            $birthdayDate['year'] = (int)$this->birthday_year;
        }
        if ($this->birthday_month !== null) {
            $birthdayDate['month'] = (int)$this->birthday_month;
        }
        if ($this->birthday_day !== null) {
            $birthdayDate['day'] = (int)$this->birthday_day;
        }

        $names = [
            [
                'displayName' => $this->displayName,
                'givenName' => $this->givenName ?? $this->displayName,
                'familyName' => $this->familyName ?? '',
            ]
        ];

        $res = [
            'resourceName' => $this->resourceName,
            'names' => $names,
        ];

        if (!empty($birthdayDate)) {
            $res['birthdays'] = [
                ['date' => $birthdayDate]
            ];
        }

        if ($this->phone_value) {
            $res['phoneNumbers'] = [
                [
                    'value' => $this->phone_value,
                    'type' => $this->phone_type ?? 'mobile',
                ]
            ];
        }

        if ($this->photo_url) {
            $res['photos'] = [
                [
                    'url' => $this->photo_url,
                ]
            ];
        }

        return $res;
    }
}
