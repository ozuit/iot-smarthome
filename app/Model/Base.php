<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

abstract class Base extends Model
{
    protected $fillRaws = [];

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->fillRaws[$key] = $value;
        };

        return parent::fill($attributes);
    }

    public function getRaw(string $key)
    {
        return $this->fillRaws[$key] ?? null;
    }
}
