<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToUser
{
    protected static function bootBelongsToUser()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (auth()->check()) {
                // Teachers see their admin's data; admins see their own data
                $builder->where($builder->getModel()->getTable() . '.user_id', auth()->user()->owner_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && empty($model->user_id)) {
                // Data always belongs to the admin, even when created by a teacher
                $model->user_id = auth()->user()->owner_id;
            }
        });
    }
}
