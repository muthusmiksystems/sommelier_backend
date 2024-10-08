<?php

namespace App;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use ChristianKuri\LaravelFavorite\Traits\Favoriteability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, Wallet, WalletFloat
{
    use Notifiable, HasRoles, HasWalletFloat, Impersonate, Favoriteability;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password', 'auth_token', 'phone', 'default_address_id', 'user_ip', 'dob', 'licence_no', 'state_id', 'licence_photo', 'default_address_id', 'delivery_pin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function addresses()
    {
        return $this->hasMany(\App\Address::class);
    }

    /**
     * @return mixed
     */
    public function orders()
    {
        return $this->hasMany(\App\Order::class);
    }

    /**
     * @return mixed
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }

    /**
     * @return mixed
     */
    public function delivery_guy_detail()
    {
        return $this->belongsTo(\App\DeliveryGuyDetail::class);
    }

    /**
     * @return mixed
     */
    public function delivery_collections()
    {
        return $this->hasMany(\App\DeliveryCollection::class);
    }

    /**
     * @return mixed
     */
    public function toggleActive()
    {
        $this->is_active = ! $this->is_active;

        return $this;
    }

    public function zone()
    {
        return $this->belongsTo(\App\Zone::class);
    }

    public function scopeNotRole(Builder $query, $roles, $guard = null)
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) use ($guard) {
            if ($role instanceof Role) {
                return $role;
            }

            $method = is_numeric($role) ? 'findById' : 'findByName';
            $guard = $guard ?: $this->getDefaultGuardName();

            return $this->getRoleClass()->{$method}($role, $guard);
        }, $roles);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->where(config('permission.table_names.roles').'.id', '!=', $role->id);
                }
            });
        });
    }

    public function todonote()
    {
        return $this->hasMany(\App\TodoNote::class);
    }
}
