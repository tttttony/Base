<?php namespace Modules\Base\Entities\Traits;

use Modules\Base\Entities\Slug\Slug;
use Modules\Base\Entities\Slug\UuidSlug;
use Modules\Base\Exceptions\GeneralException;

trait Sluggable
{
    protected $sluggable = true;
    private $sluggable_slug;
    private $sluggable_holding;

    protected function setSlug(string $slug) {
        return $this->sluggable_slug = $slug;
    }

    public function getSlug() {
        if(env('SITE_CODE')) {
            return $this->getSlugByProperty(env('SITE_CODE'));
        }

        $properties = $this->properties->pluck('code')->all();
        $slugs = [];
        foreach($properties as $property) {
            $slugs[$property] = $this->getSlugByProperty($property);
        }
        return $slugs;
    }

    public function lookUpBySlug($slug) {
        if(env('SITE_CODE')) {
            $search = [
                ['slug', $slug],
                ['sluggable_type', $this->getMorphClass()],
                ['property_code', env('SITE_CODE')]
            ];
        }
        else {
            $search = [
                ['slug', $slug],
                ['sluggable_type', $this->getMorphClass()]
            ];
        }

        if (
            !empty($check = Slug::where($search)->get())
            or !empty($check = UuidSlug::where($search)->get())
        ) {
            return $check;
        }
        return false;
    }

    protected function getSlugByProperty($property) {
        $search = [ ['sluggable_id', $this->getKey()], ['sluggable_type', $this->getMorphClass()], ['property_code', $property] ];
        if (
            !empty($check = Slug::where($search)->first())
            or !empty($check = UuidSlug::where($search)->first())
        ) {
            return $check;
        }
        return '';
    }

    protected function createSlugForProperty($property) {
        if(empty($this->getSlugByProperty($property))) {
            $data = [
                'slug' => $this->generateSlug($property),
                'sluggable_id' => $this->getKey(),
                'sluggable_type' => $this->getMorphClass(),
                'property_code' => $property,
            ];
            if ($this->getIncrementing()) {
                Slug::create($data);
            } else {
                UuidSlug::create($data);
            }
        }
    }

    protected function generateSlug($property) {
        if(! empty($this->sluggable_holding)) {
            $slug = $this->sluggable_holding;
        }
        else if(! empty($this->sluggable_slug)) {
            $slug = $this->sluggable_slug;
        }
        else if( array_key_exists('name', $this->getAttributes()) ) {
            $slug = str_slug($this->name);
        }
        else if( array_key_exists('title', $this->getAttributes()) ) {
            $slug = str_slug($this->title);
        }

        if(empty($slug)) {
            throw new GeneralException("Sluggable objects must set a slug or have a title or name attribute.");
        }

        return $this->getUsableSlugFrom($slug, $property);
    }

    protected function checkSlugAvailability($slug, $property) {
        $search = [ ['slug', $slug], ['property_code', $property] ];
        if(
            ! empty($check = Slug::where($search)->first())
        or ! empty($check = UuidSlug::where($search)->first())
        ) {
            return false;
        }
        return true;
    }

    protected function getUsableSlugFrom($slug, $property, $x = 1) {
        if($this->checkSlugAvailability($slug, $property)) {
            return $slug;
        }
        elseif($this->checkSlugAvailability($slug.'-'.$x, $property)) {
            return $slug.'-'.$x;
        }

        $x++;
        return $this->getUsableSlugFrom($slug, $property, $x);
    }

    protected static function bootSluggable() {
        static::saved(function ($model) {
            if(
                $properties = $model->properties->pluck('code')->all() /* intentional assignment */
            and ! empty($properties)
            ) {
                foreach ($properties as $property) {
                    $model->createSlugForProperty($property);
                }
            }
        });
    }
}
