<?php

namespace App\Lib\Api;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\AbstractPaginator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Router;
use App\Lib\Office\Excel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\Base;

abstract class BaseApi
{
    protected $route;

    protected $request;

    protected $routes;

    protected $include_variables = [];

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get('http.request');
        $this->routes = $container->get(Router::class);

        if (!$this->method_exists('transform')) {
            throw new Exception(sprintf('Protected method [transform] is not exists in class [%s]', static::class));
        }
    }

    protected function get(string $key)
    {
        return $this->container->get($key);
    }

    protected function method_exists($name)
    {
        $reflection = null;
        if (method_exists($this, $name)) {
            $reflection = new \ReflectionMethod($this, $name);
            if (!$reflection->isPrivate()) {
                return true;
            }
        }
        return false;
    }

    protected function dateFormat($value, string $format)
    {
        if (is_string($value)) {
            $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        if ($value instanceof \DateTime) {
            return $value->format($format);
        }
        return null;
    }

    public function item($model, array $relations = [])
    {
        $item = $this->transform($model);
        foreach ($relations as $relation) {
            $func = 'include' . implode('', array_map('ucfirst', explode('_', $relation)));
            if ($this->method_exists($func)) {
                $item[$relation] = call_user_func([$this, $func], $model);
            }
        }

        return $item;
    }

    public function items(Collection $collection, array $relations = [])
    {
        $result = [];
        foreach ($collection as $model) {
            $item = $this->item($model, $relations);
            try {
                if ($this->route) {
                    $item['_self'] = $this->routes->generate($this->route, ['id' => $model->id]);
                }
            } catch (\Exception $e) {
            }
            if (count($this->include_variables) > 0) {
                $item['_relations'] = $this->include_variables;
            }
            $result[] = $item;
        }
        return $result;
    }

    protected function meta(AbstractPaginator $data)
    {
        $links = [];
        if ($previous = $data->previousPageUrl()) {
            $links['previous'] = $previous;
        }
        if ($next = $data->nextPageUrl()) {
            $links['next'] = $next;
        }
        return [
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'links' => $links,
            ],
        ];
    }
    
    public function render($data, bool $is_deep = false) :? array
    {
        if ($is_deep) {
            $relations = array_filter(explode(',', strval($this->request->get('_relations'))));
        } else {
            $relations = [];
        }
        if ($data instanceof Model) {
            return $this->renderModel($data, $relations);
        } elseif ($data instanceof Collection) {
            return $this->renderCollection($data, $relations);
        } elseif ($data instanceof AbstractPaginator) {
            return $this->renderCollectionWithPagination($data, $relations);
        }
        return null;
    }

    protected function renderModel(Model $model, array $relations)
    {
        return [
            'data' => $this->item($model, $relations),
            'meta' => [],
        ];
    }

    protected function renderCollection(Collection $collection, array $relations)
    {
        return [
            'data' => $this->items($collection, $relations),
            'meta' => [],
        ];
    }

    protected function renderCollectionWithPagination(AbstractPaginator $data, array $relations)
    {
        $data->appends($this->request->query->all());
        return [
            'data' => $this->items($data->getCollection(), $relations),
            'meta' => $this->meta($data),
        ];
    }

    protected function getHeaderExport(): array
    {
        return [];
    }

    protected function getMapExport(): array
    {
        return [];
    }

    protected function getCenterColumnsExport(): array
    {
        return [];
    }

    protected function getTitleExport(Request $request): string
    {
        return 'Export - ' . date('d/m/Y');
    }

    protected function makeImportModel(array $row)
    {
    }

    public function import(UploadedFile $file): int
    {
        $count = 0;
        $rows = $this->get(Excel::class)->import($file);
        foreach ($rows as $row) {
            if (is_array($row)) {
                $model = $this->makeImportModel($row);
                if ($model instanceof Base) {
                    if ($model->save()) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    public function export(Request $request, $data): Response
    {
        $collection = [];
        if ($data instanceof AbstractPaginator) {
            $collection = $data->getCollection();
        } elseif ($data instanceof Collection) {
            $collection = $data;
        }
        $headers = $this->getHeaderExport();
        $maps = $this->getMapExport();
        $i = 0;
        $result = [array_values($headers)];
        $fields = array_keys($headers);
        foreach ($collection as $model) {
            $i++;
            $row = [];
            foreach ($fields as $field) {
                if ($field === 'LOOPINDEX') {
                    $row[] = $i;
                } else {
                    $map = $maps[$field] ?? null;
                    if (is_callable($map)) {
                        $row[] = call_user_func($map, $model);
                    } else {
                        $row[] = $model->{$field};
                    }
                }
            }
            $result[] = $row;
        }

        $filename = 'export-' . date('ymd-his');
        $title = $this->getTitleExport($request);
        $center_columns = $this->getCenterColumnsExport();

        $content = $this->get(Excel::class)->export($result, $title, $center_columns);

        return new Response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=$filename.xlsx",
        ]);
    }
}
