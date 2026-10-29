<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPagePermission extends Model
{
    protected $fillable = ['user_id', 'resource', 'can_view', 'can_edit'];

    protected $casts = [
        'can_view' => 'boolean',
        'can_edit' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All navigable resource slugs with their display labels.
     */
    public static function allResources(): array
    {
        return [
            'branches'           => 'Branches',
            'companies'          => 'Companies',
            'compliances'        => 'Compliances',
            'compliance-types'   => 'Compliance Types',
            'compliance-records' => 'Compliance Records',
            'users'              => 'Users',
        ];
    }

    /**
     * Check if a user can view a given resource.
     * Defaults to true if no explicit record exists.
     */
    public static function canView(int $userId, string $resource): bool
    {
        $perm = static::where('user_id', $userId)->where('resource', $resource)->first();
        return $perm ? (bool) $perm->can_view : true;
    }

    /**
     * Check if a user can edit (create/update/delete) a given resource.
     * Defaults to true if no explicit record exists.
     * Edit is only meaningful when can_view is also true.
     */
    public static function canEdit(int $userId, string $resource): bool
    {
        $perm = static::where('user_id', $userId)->where('resource', $resource)->first();
        if (! $perm) return true;
        // Can only edit if also allowed to view
        return (bool) $perm->can_view && (bool) $perm->can_edit;
    }
}
