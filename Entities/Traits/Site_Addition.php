<?php namespace Modules\Base\Entities\Traits;

trait Site_Addition
{
    protected $data;
    protected $site_class;

    public function onSiteCreate() {
        $this->site_class = '\Sites\\'.$this->site_code.'\Entities\\'.ucfirst(strtolower($this->site_code)).class_basename($this);
        $this->with[] = 'siteSpecificData';
    }

    public function createSsdRecord() {
        //TODO: This can be removed, it should handle everything on save()
        $this->save();
    }

    public function siteSpecificData()
    {
        return $this->hasOne($this->site_class);
    }

    public function checkData()
    {
        if(is_callable([$this, 'siteSpecificData']) and !isset($this->siteSpecificData) and !isset($this->data))
            $this->data = new $this->site_class;
    }

    public function shouldUse($key, $must_be_fillable = false)
    {
        return (
            $key != 'siteSpecificData'
            and (
                $must_be_fillable
				and $this->dataToUse()->isFillable($key)
			)
			and $key !== 'id'
        );
    }

    public function dataToUse()
    {
        $this->checkData();
        return (!isset($this->siteSpecificData))? $this->data: $this->siteSpecificData;
    }

    public function setDefaults() {
        $foreign_key = $this->siteSpecificData()->getForeignKeyName();
        $this->dataToUse()->setAttribute($foreign_key, $this->{$this->getKeyName()});
    }

    public function __get($key)
    {
        if($this->shouldUse($key))
            return (empty($this->dataToUse()->$key) && !empty(parent::__get($key)))? parent::__get($key): $this->dataToUse()->$key;

        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if($this->shouldUse($key, true))
            return $this->dataToUse()->$key = $value;

        return parent::__set($key, $value);
    }

    public function __isset($key)
    {
        if($this->shouldUse($key))
            return $this->dataToUse()->__isset($key);

        return parent::__isset($key);
    }

    public function __unset($key)
    {
        if($this->shouldUse($key))
            return $this->dataToUse()->__unset($key);

        return parent::__unset($key);
    }

    public function __call($method, $parameters)
    {
        if($this->shouldUse($method, true))
            return $this->dataToUse()->__call($method, $parameters);

        return parent::__call($method, $parameters);
    }

    public function setAttribute($key, $value)
    {
        if($this->shouldUse($key))
            return $this->dataToUse()->setAttribute($key, $value);

        return parent::setAttribute($key, $value);
    }

    public function save(array $options = [])
    {
        $save = parent::save($options);
        $this->setDefaults();
        $this->dataToUse()->save($options);
        return $save;
    }
}
