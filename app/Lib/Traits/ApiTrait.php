<?php

namespace App\Lib\Traits;

use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Eloquent\Builder;
use Evenement\EventEmitterTrait;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Exception\EmptyDataException;
use App\Exception\ApiLogicException;
use App\Model\Base as BaseModel;

trait ApiTrait
{
    use EventEmitterTrait;

    protected $model_name;
    
    protected $pdo;

    public function __construct(ContainerInterface $container, Manager $db)
    {
        parent::__construct($container, $db);

        $this->pdo = $db->getConnection()->getPdo();
        $this->boot();
    }

    protected $maxPerPage = 1000;

    public function deleteData(Request $request, int $id) : bool
    {
        $inTransaction = $this->pdo->inTransaction();
        $model = $this->find($id);
        if (! $model instanceof BaseModel) {
            throw new EmptyDataException(sprintf('Can\'t find model [%s] with id (%s)', $this->model_name, $id));
        }
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }
        $this->emit('deleting', [$model]);
        $bool = $model->delete();
        $this->emit('deleted', [$model]);
        if (!$inTransaction) {
            $this->pdo->commit();
        }

        return $bool;
    }

    public function putData(Request $request, array $data, int $id) : BaseModel
    {
        $inTransaction = $this->pdo->inTransaction();
        $model = $this->find($id);
        if (! $model instanceof BaseModel) {
            throw new EmptyDataException(sprintf('Can\'t find model [%s] with id (%s)', $this->model_name, $id));
        }
        $model->fill($data);
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }
        $this->emit('updating', [$model]);
        $this->emit('saving', [$model]);
        $model->save();
        $this->emit('saved', [$model]);
        $this->emit('updated', [$model]);
        if (!$inTransaction) {
            $this->pdo->commit();
        }

        return $model;
    }

    public function postData(Request $request, array $data) : BaseModel
    {
        $inTransaction = $this->pdo->inTransaction();
        $model = $this->createNew($data);
        if (! $model instanceof BaseModel) {
            throw new ApiLogicException(sprintf('Method [%s::createNew] must return a instanceof [%s]', static::class, BaseModel::class));
        }
        if (!$inTransaction) {
            $this->pdo->beginTransaction();
        }
        $this->emit('creating', [$model]);
        $this->emit('saving', [$model]);
        $model->save();
        $this->emit('saved', [$model]);
        $this->emit('created', [$model]);
        if (!$inTransaction) {
            $this->pdo->commit();
        }

        return $model;
    }

    public function getData(Request $request, int $id = null)
    {
        if ($id) {
            return $this->findModel($request, $id);
        }
        $no_pagination = boolval($request->query->get('_noPagination', '0'));
        if ($no_pagination === '1') {
            $result = $this->getWithoutPage($request);
        } else {
            $result = $this->getWithPage($request);
        }
        return $this->apiRender($result);
    }

    public function exportData(Request $request): Response
    {
        $no_pagination = boolval($request->query->get('_noPagination', '0'));
        if ($no_pagination === '1') {
            $result = $this->getWithoutPage($request);
        } else {
            $result = $this->getWithPage($request);
        }
        $lib = $this->getApiLib();

        return $lib->export($request, $result);
    }

    public function importData(Request $request): int
    {
        $inTransaction = $this->pdo->inTransaction();
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            if (!$inTransaction) {
                $this->pdo->beginTransaction();
            }

            $lib = $this->getApiLib();
            $count = $lib->import($file);

            if (!$inTransaction) {
                $this->pdo->commit();
            }
            return $count;
        }
        return 0;
    }

    protected function findModel(Request $request, int $id)
    {
        $query = $this->builderForGetOne($request);
        $result = $query->find($id);

        return $this->apiRender($result);
    }

    protected function getWithoutPage(Request $request)
    {
        $query = $this->builderForGetMany($request);
        return $query->get();
    }

    protected function getWithPage(Request $request)
    {
        $page = max(intval($request->query->get('_page')), 1);
        $perPage = min(max(intval($request->query->get('_perPage')), 10), $this->maxPerPage);
        $query = $this->builderForGetMany($request);
        return $query->paginate($perPage, ['*'], '_page', $page);
    }

    protected function builderForGetOne(Request $request)
    {
        $query = $this->queryFind($request);

        return $query;
    }

    protected function builderForGetMany(Request $request)
    {
        $orderBy = array_map(function ($item) {
            $vars = explode(':', $item);
            $column = $vars[0];
            if ($this->isSortable($column)) {
                $type = $vars[1] ?? 'asc';
                return "`$column` ".(strtolower($type) === 'desc' ? 'desc' : 'asc');
            }
        }, array_filter(explode(';', $request->query->get('_orderBy', ''))));
        $orderBy[] = "id desc";
        $orderBySql = implode(', ', array_filter($orderBy));
        $filterData = $this->getFilters($request);
        $filters = [];
        foreach ($filterData as $column => $value) {
            if ($this->isFilterable($column) && $value != '') {
                $filters[] = $this->filter($column, $value);
            }
        }
        $query = $this->queryGet($request);
        foreach ($filters as $filter) {
            if ($filter) {
                if (is_callable($filter)) {
                    call_user_func($filter, $query);
                } elseif (is_array($filter[0])) {
                    $query->where($filter);
                } else {
                    $query->where([$filter]);
                }
            }
        }
        $query->orderByRaw($orderBySql);
        return $query;
    }

    protected function getFilters(Request $request)
    {
        $raw = $request->query->get('_filter', '');
        $data = [];
        $raws = array_filter(explode(';', $raw));
        foreach ($raws as $item) {
            $items = explode(':', $item);
            if (is_array($items) && count($items) >= 2) {
                $data[array_shift($items)] = implode(':', $items);
            }
        }
        return $data;
    }

    protected function isFilterable($field)
    {
        return in_array($field, $this->getFilterableFields());
    }

    protected function getFilterableFields() : array
    {
        return [];
    }

    protected function mapFilters() : array
    {
        return [];
    }

    protected function isSortable($field) : bool
    {
        return in_array($field, $this->getOrderbyableFields());
    }

    protected function getOrderbyableFields() : array
    {
        return [];
    }

    protected function filter($field, $value)
    {
        $maps = $this->mapFilters();
        if (isset($maps[$field])) {
            $filter = $maps[$field];
            return is_callable($filter) ? call_user_func($filter, $field, $value) : sprintf($maps[$field], $value);
        }
        return [$field, '=', $value];
    }

    protected function newQuery() : Builder
    {
        return $this->query();
    }

    protected function queryGet(Request $request) : Builder
    {
        $query = $this->newQuery();
        $this->loadRelations($request, $query);

        return $query;
    }

    protected function queryFind(Request $request) : Builder
    {
        $query = $this->newQuery();
        $this->loadRelations($request, $query);

        return $query;
    }

    protected function loadRelations(Request $request, $query)
    {
        $relations = array_filter(explode(',', strval($request->query->get('_relations'))));
        $model = $this->createNew();
        foreach ($relations as $relation) {
            if (method_exists($model, $relation)) {
                $result = call_user_func([$model, $relation]);
                if ($result instanceof Relation) {
                    $query->with($relation);
                }
            }
        }
    }

    abstract protected function getApiLib();

    public function apiRender($data) : array
    {
        $lib = $this->getApiLib();

        if ($data === null) {
            throw new EmptyDataException("Không tìm thấy dữ liệu dũ liệu!");
        }

        return $lib->render($data, true);
    }

    protected function boot()
    {
        $this->on('creating', function ($model) {
            // 1
        });

        $this->on('updating', function ($model) {
            // 1
        });

        $this->on('deleting', function ($model) {
            // 1
        });

        $this->on('deleted', function ($model) {
            // 2
        });

        $this->on('saving', function ($model) {
            // 2
        });

        $this->on('saved', function ($model) {
            // 2
        });

        $this->on('created', function ($model) {
            // 3
        });

        $this->on('updated', function ($model) {
            // 3
        });

        $this->on('saving', function ($model) {
            // 2
        });
    }
}
