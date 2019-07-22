<?php

namespace App\Service;

use Cocur\Slugify\Slugify;
use App\Lib\Traits\ApiTrait;
use App\Lib\Traits\CreateSlugTrait;

abstract class ApiService extends Base
{
    use ApiTrait;
    use CreateSlugTrait;

    protected function getSlugify(): Slugify
    {
        return $this->{Slugify::class};
    }

    protected function fields2Slug(): array
    {
        return [];
    }
}
