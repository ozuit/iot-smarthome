<?php

namespace App\Lib\Traits;

use Cocur\Slugify\Slugify;
use App\Model\Base as BaseModel;

trait CreateSlugTrait
{
    public function createSlug(BaseModel $model): string
    {
        $fields = $this->fields2Slug();
        $values = [];
        foreach ($fields as $field) {
            $values[] = strval($model->{$field});
        }
        $string = str_replace('@', 'acong', implode(' ', $values));

        return substr(str_replace('acong', '@', $this->getSlugify()->slugify($string, ' ')), 0, 255);
    }

    protected abstract function fields2Slug(): array;

    protected abstract function getSlugify(): Slugify;
}
