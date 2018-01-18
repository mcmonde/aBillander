<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;

class CustomerUser extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * Configure guard.
     *
     */
    protected $guard = 'customer';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 
//        'home_page', 'is_admin', 
        'active', 'language_id'
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
     * Validation rules
     * 
     */
    public static $rules = array(
        'email'       => array('required', 'email'),
        'password'    => array('required', 'min:2', 'max:32'),
        'language_id' => 'exists:languages,id',
    );


    /**
     * Protect Password on Model update
     * 
     */
    /**
       Laravel's trait does hash the password for you, you need to remove this :
    public function setPasswordAttribute($value)
    {
        if( !empty($value)) 
        {
            $this->attributes['password'] = Hash::make($value);
        }

    }
     */

    /**
     * Handy methods
     * 
     */
    public function getFullName()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    
    public function stockmovements()
    {
        return $this->hasMany('App\StockMovement');
    }
    
    public function currencyconversionrates()
    {
        return $this->hasMany('App\CurrencyConversionRate');
    }
}
