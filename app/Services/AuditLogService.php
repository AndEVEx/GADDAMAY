<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
        ]);
    }

    public static function logCreate(Model $model): AuditLog
    {
        return static::log('create', $model, null, $model->toArray());
    }

    public static function logUpdate(Model $model, array $oldValues): AuditLog
    {
        $changedValues = [];
        foreach ($model->getChanges() as $key => $value) {
            if ($key !== 'updated_at') {
                $changedValues[$key] = $value;
            }
        }

        return static::log('update', $model, $oldValues, $changedValues);
    }

    public static function logDelete(Model $model): AuditLog
    {
        return static::log('delete', $model, $model->toArray(), null);
    }

    public static function logOverride(Model $model, array $oldValues, array $newValues): AuditLog
    {
        return static::log('override', $model, $oldValues, $newValues);
    }
}
