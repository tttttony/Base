<?php namespace Modules\Base\Entities\Slug;

use Modules\Base\Entities\BaseEntity;
use Modules\Organization\Entities\Traits\Propertiable;
use Webpatser\Uuid\Uuid;

class Slug extends BaseEntity
{
    protected $table = 'sluggables';
    protected $fillable = [
        'property_code',
        'slug',
        'sluggable_id',
        'sluggable_type',
    ];

    public function __construct(array $attributes = [])
    {
        if(! $this->getIncrementing())
        {
            static::creating(function ($model) {
                $model->incrementing = false;
                $uuidVersion = (!empty($model->uuidVersion) ? $model->uuidVersion : 4);   // defaults to 4
                $uuid = Uuid::generate($uuidVersion);
                $model->attributes[$model->getKeyName()] = $uuid->string;
            }, 0);
        }

        parent::__construct($attributes);
    }
}