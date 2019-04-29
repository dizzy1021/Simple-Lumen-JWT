<?php
namespace App\Models;
use Webpatser\Uuid\Uuid;
trait Uuids
{
    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $rand = str_random('8').uniqid();
            $model->{$model->getKeyName()} = Uuid::generate(5,$rand, Uuid::NS_DNS)->string;
        });
    }
}