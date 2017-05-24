<?php namespace Modules\Base\Entities\Slug;

use Modules\Base\Entities\BaseEntity;
use Modules\Base\Entities\Traits\UuidModelTrait;
use Modules\Organization\Entities\Traits\Propertiable;

class UuidSlug extends BaseEntity
{
    use Propertiable,
        UuidModelTrait;
    protected $table = 'sluggables_uuid';
    protected $fillable = [
        'property_code',
        'slug',
        'sluggable_id',
        'sluggable_type',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}