<?php namespace Modules\Base\Entities\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Base\Entities\Slug\Slug;
use Modules\Base\Entities\Slug\UuidSlug;

class BasePresenter extends Presenter
{
    public function slug() {
        //TODO: check is trait sluggable is on entity before trying this.
        if(isset($this->entity->slug)) {
            return $this->entity->slug;
        }

        if (
            method_exists($this->entity, 'getSlug')
            and ($this->entity->getSlug() instanceof Slug or $this->entity->getSlug() instanceof UuidSlug)
        ) {
            return $this->entity->getSlug()->slug;
        }
        return false;
    }
}