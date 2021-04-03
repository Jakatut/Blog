<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class BlogPost extends Model {
    use HasFactory;

    protected $table = 'blog_post';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'summary', 'body', 'image'
    ];

    public function setImageEncoded($value) {
        $this->attributes['image'] = base64_encode($value);
    }

    public function getImageDecoded() {
        return base64_decode($this->attributes['image']);
    }
}
