<?php

namespace App\Service;

class UtilService
{
    protected $areas = [
        1 => 'Tp Hồ Chí Minh',
        2 => 'Hà Nội',
    ];

    public function getArea(): array
    {
        $result = [];
        foreach($this->areas as $key => $area) {
            $result[] = [
                'id' => $key,
                'name' => $area
            ];
        }

        return $result;
    }

    public function getAreaById($id): string
    {
        if ($id) {
            return $this->areas[$id];
        }

        return '';
    }
}
