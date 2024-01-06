<?php

namespace App\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, "user_id", "id");
    }
}
