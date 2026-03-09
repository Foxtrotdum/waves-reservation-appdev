<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        AuditLog::log(
            $model->getTable(),
            'create',
            $model->getKey(),
            null,
            $model->toArray()
        );
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        AuditLog::log(
            $model->getTable(),
            'update',
            $model->getKey(),
            $model->getOriginal(),
            $model->toArray()
        );
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        AuditLog::log(
            $model->getTable(),
            'delete',
            $model->getKey(),
            $model->getOriginal(),
            null
        );
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        AuditLog::log(
            $model->getTable(),
            'restore',
            $model->getKey(),
            null,
            $model->toArray()
        );
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        AuditLog::log(
            $model->getTable(),
            'force_delete',
            $model->getKey(),
            $model->getOriginal(),
            null
        );
    }
}
