<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;


use Illuminate\Database\Eloquent\{
    Factories\HasFactory,
    SoftDeletes
};
use Spatie\Activitylog\{
    Traits\LogsActivity,
    LogOptions
};

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    use LogsActivity;

   // use HasPanelShield;
    use HasRoles;


    // use HasPanelShield;
    // use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'profile_photo_path',
        'remember_token',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function boot()
    {
        parent::boot();
        // dd();
        static::saving(function ($model) {
            /*
            // remove any previous roles
            $model->syncRoles([]);
            // add new role
            $model->assignRole($model->role);

            // get all permissions for the new role
            $permissions = Role::findByName($model->role)->permissions;
            // assign the new permissions to the user
            $model->syncPermissions($permissions);

            // Check if password is present and not empty
            if (is_null($model->password)) {
                // Unset password so it won't be updated
                unset($model->password);
            }
            */

        });

    }

    /**
     * Allow user to comment
     * @return bool
     */
    public function canComment(): bool
    {
        // your conditional logic here
        return true;
    }

    /**
     * This function will make sure to log all fields in this model
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
