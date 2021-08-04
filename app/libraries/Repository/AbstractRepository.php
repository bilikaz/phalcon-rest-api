<?php
declare(strict_types=1);

namespace App\Library\Repository;

use Phalcon\Di\InjectionAwareInterface,
    Phalcon\Di\DiInterface,
    Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

use App\Library\Api\Exception\ApiException,
    App\Library\Api\Exception\EntityNotFoundException;

abstract class AbstractRepository implements InjectionAwareInterface
{
    const MODEL = '';
    const ALIAS = '';
    const ID = '';
    const EXCEPTION = '';
    protected $cache;
    protected $cacheKeys = []; //list of keys that are marked as expired on create / update / delete
    protected static $cached = [];
    protected static $repositories = [];
    private $di;

    public function setDI(DiInterface $di): void
    {
        $this->di = $di;
    }

    public function getDI(): DiInterface
    {
        return $this->di;
    }

    public function __get($name)
    {
        if (substr($name, -10) === 'Repository') {
            if (!isset(self::$repositories[$name])) {
                self::$repositories[$name] = $this->getDI()->getShared($name);
            }
            return self::$repositories[$name];
        }
        return null;
    }

    protected function throwException($exception)
    {
        if ($exception instanceof ApiException) {
            throw $exception;
        } elseif (!is_string($exception)) {
            throw new EntityNotFoundException(static::EXCEPTION);
        }
        throw new EntityNotFoundException($exception);
    }

    private function getCache()
    {
        if (!isset($this->cache)) {
            $this->cache = $this->getDI()->getShared('cache');
        }
        return $this->cache;
    }

    private function getCacheExpiration($tags)
    {
        $expiration = 0;
        foreach ($tags as $tag) {
            $value = $this->getCache()->get($tag . '_expiration');
            if ($value && $value > $expiration) {
                $expiration = $value;
            }
        }
        return $expiration;
    }

    protected function setCacheExpired($tags)
    {
        $time = time();
        foreach ($tags as $tag) {
            $this->getCache()->set($tag . '_expiration', $time);
        }
    }

    protected function getFromCache($key, $function, $tags = [])
    {
        if (isset(self::$cached[$key])) {
            return self::$cached[$key];
        }
        $start = time();
        $timestamp = $this->getCache()->get($key . '_timestamp');
        $data = $this->getCache()->get($key);
        $expiration = $this->getCacheExpiration($tags);
        if ($data && $timestamp > $expiration) {
            return $data;
        }
        $data = $function();
        $expiration = $this->getCacheExpiration($tags);
        //prevent cashing old data
        if ($start > $expiration) {
            $this->getCache()->set($key . '_timestamp', $start);
            $this->getCache()->set($key, $data);
        }
        self::$cached[$key] = $data;
        return $data;
    }

    public function isInstance($model, $exception = false)
    {
        $className = static::MODEL;
        if (!($model instanceof $className)) {
            if ($exception) {
                throw new Exception\RepositoryException('Bad instance');
            }
            return false;
        }
        return true;
    }

    public function newModel()
    {
        $className = static::MODEL;
        return new $className;
    }

    private function processWhere($query, $fields)
    {
        foreach ($fields as $field => $value) {
            if (is_null($value)) {
                $query->andWhere(static::ALIAS . '.' . $field . ' IS NULL');
            } elseif (is_array($value)) {
                $query->andWhere(static::ALIAS . '.' . $field . ' IN ({' . $field . ':array})', [$field => $value]);
            } else {
                $query->andWhere(static::ALIAS . '.' . $field . ' = :' . $field . ':', [$field => $value]);
            }
        }
    }

    protected function query($alias = null)
    {
        if (!$alias) {
            $alias = static::ALIAS;
        }
        return $this->getDI()->getShared('modelsManager')->createBuilder()
            ->addFrom(static::MODEL, $alias);
    }

    public function isUniqueFieldValue($field, $value)
    {
        return $this->getByField($field, $value) ? false : true;
    }

    public function lockById($id, $exception)
    {
        $query = $this->query()
            ->forUpdate(true)
            ->limit(1);
        $fields = [];
        if (is_array(static::ID)) {
            foreach (static::ID as $field) {
                $fields[$field] = $id[$field];
            }
        } else {
            $fields[static::ID] = $id;
        }
        $this->processWhere($query, $fields);
        return $this->getResult($query, $exception);
    }

    protected function lockByField($field, $value, $exception)
    {
        $query = $this->query()
            ->forUpdate(true)
            ->limit(1);
        $this->processWhere($query, [$field => $value]);
        return $this->getResult($query, $exception);
    }

    protected function lockByFields($fields, $exception)
    {
        $query = $this->query()
            ->forUpdate(true)
            ->limit(1);
        $this->processWhere($query, $fields);
        return $this->getResult($query, $exception);
    }

    public function getById($id, $exception)
    {
        $query = $this->query()
            ->limit(1);
        $fields = [];
        if (is_array(static::ID)) {
            foreach (static::ID as $field) {
                $fields[$field] = $id[$field];
            }
        } else {
            $fields[static::ID] = $id;
        }
        $this->processWhere($query, $fields);
        return $this->getResult($query, $exception);
    }

    protected function getByField($field, $value, $exception)
    {
        $query = $this->query()
            ->limit(1);
        $this->processWhere($query, [$field => $value]);
        return $this->getResult($query, $exception);
    }

    protected function getByFields($fields, $exception)
    {
        $query = $this->query()
            ->limit(1);
        $this->processWhere($query, $fields);
        return $this->getResult($query, $exception);
    }

    protected function getPairs($query, $index, $value, $exception)
    {
        $result = [];
        $data = $query->getQuery()->execute();
        foreach ($data as $row) {
            $result[$row->{$index}] = $row->{$value};
        }
        if (!$result && $exception) {
            $this->throwException($exception);
        }
        return $result;
    }

    protected function getAll($exception)
    {
        $query = $this->query();
        return $this->getResults($query, $exception);
    }

    protected function getAllByFields($fields, $exception)
    {
        $query = $this->query();
        $this->processWhere($query, $fields);
        return $this->getResults($query, $exception);
    }

    protected function lockAllByField($field, $value, $exception)
    {
        $query = $this->query()
            ->forUpdate(true);
        $this->processWhere($query, [$field => $value]);
        return $this->getResults($query, $exception);
    }

    protected function lockAllByFields($fields, $exception)
    {
        $query = $this->query()
            ->forUpdate(true);
        $this->processWhere($query, $fields);
        return $this->getResults($query, $exception);
    }

    public function createFromArray($data)
    {
        $model = $this->newModel();
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
        if (!$model->create()) {
            throw new Exception\RepositoryException('Failed to create', $model);
        }
        if ($this->cacheKeys) {
            $this->setCacheExpired($this->cacheKeys);
        }
        return $model;
    }

    public function saveFromArray($data)
    {
        $model = $this->newModel();
        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }
        if (!$model->save()) {
            throw new Exception\RepositoryException('Failed to save', $model);
        }
        if ($this->cacheKeys) {
            $this->setCacheExpired($this->cacheKeys);
        }
        return $model;
    }

    public function create($model, $data = [])
    {
        $this->isInstance($model, true);
        if ($data) {
            foreach ($data as $key => $value) {
                $model->{$key} = $value;
            }
        }
        if (!$model->create()) {
            throw new Exception\RepositoryException('Failed to create', $model);
        }
        if ($this->cacheKeys) {
            $this->setCacheExpired($this->cacheKeys);
        }
        return $model;
    }

    public function save($model, $data = [])
    {
        $this->isInstance($model, true);
        if ($data) {
            //we have to unset ID fields that another record then intended won't be updated
            if (is_array(static::ID)) {
                foreach (static::ID as $field) {
                    if (isset($data[$field])) {
                        if (!isset($model->{$field})) {
                            $model->{$field} = $data[$field];
                        }
                        unset($data[$field]);
                    }
                }
            } else {
                if (isset($data[static::ID])) {
                    if (!isset($model->{static::ID})) {
                        $model->{static::ID} = $data[static::ID];
                    }
                    unset($data[static::ID]);
                }
            }

        }
        if (!$model->save()) {
            throw new Exception\RepositoryException('Failed to save', $model);
        }
        if ($this->cacheKeys) {
            $this->setCacheExpired($this->cacheKeys);
        }
        return $model;
    }

    public function delete($model)
    {
        $this->isInstance($model, true);
        if (!$model->delete()) {
            throw new Exception\RepositoryException('Failed to delete', $model);
        }
        if ($this->cacheKeys) {
            $this->setCacheExpired($this->cacheKeys);
        }
    }

    protected function getResultsByFields($query, array $fields, $exception)
    {
        $result = [];
        $data = $query->getQuery()->execute();
        foreach ($fields as $key => $field) {
            if (!is_array($field)) {
                $fields[$key] = explode('.', $field);
            }
        }
        foreach ($data as $row) {
            $keys = [];
            foreach ($fields as $field) {
                $key = $row;
                foreach ($field as $part) {
                    $key = $key->{$part};
                }
                if (is_null($key)) {
                    $key = 'null';
                }
                $keys[] = $key;
            }
            $tmp = &$result;
            foreach ($keys as $key) {
                if (is_null($tmp)) {
                    $tmp = [];
                }
                $tmp = &$tmp[$key];
            }
            $tmp = $row;
        }
        if (!$result && $exception) {
            $this->throwException($exception);
        }
        return $result;
    }

    protected function getResultsByField($query, $field, $exception)
    {
        return $this->getResultsByFields($query, [$field], $exception);
    }

    protected function getResults($query, $exception)
    {
        $result = [];
        $data = $query->getQuery()->execute();
        foreach ($data as $row) {
            $result[] = $row;
        }
        if (!$result && $exception) {
            $this->throwException($exception);
        }
        return $result;
    }

    protected function getResult($query, $exception)
    {
        $query->limit(1);
        $row = $query->getQuery()->execute()->getFirst();
        if (!$row && $exception) {
            $this->throwException($exception);
        }
        return $row;
    }

    protected function getPageForRequest($query, array $conditions = null, $request, array $order, int $limit)
    {
        if (isset($request->filters)) {
            $filters = $request->filters;
            if (is_object($filters)) {
                if (method_exists($filters, 'toArray')) {
                    $filters = $filters->toArray();
                } else {
                    $filters = (array) $filters;
                }
            }
        } else {
            $filters = [];
        }

        if (isset($request->order)) {
            $order = $request->order;
            if (is_object($order)) {
                if (method_exists($order, 'toArray')) {
                    $order = $order->toArray();
                } else {
                    $order = (array) $order;
                }
            }
        }

        if (isset($request->limit)) {
            $limit = (int) $request->limit;
        }
        if (isset($request->page)) {
            $page = (int) $request->page;
        } else {
            $page = 1;
        }

        return $this->getPage($query, $conditions, $filters, $order, $limit, $page);
    }

    protected function getPage($query, array $conditions = null, array $filters = null, array $order, int $limit, int $page)
    {
        $id = 0;
        if ($filters && $conditions) {
            foreach ($filters as $field => $value) {
                if (!isset($conditions[$field])) {
                    continue;
                }
                $id++;
                $key = 'filter_value_' . $id;
                $condition = $conditions[$field];
                if (is_array($condition)) {
                    if (isset($condition[$value])) {
                        $condition = $condition[$value];
                    } elseif (isset($condition['default'])) {
                        $condition = $condition['default'];
                    } else {
                        continue;
                    }
                }
                if (preg_match('/{value(|[^\}]+)?}/', $condition, $tmp)) {
                    if ($tmp[1]) {
                        $functions = explode('|', substr($tmp[1], 1));
                        foreach ($functions as $function) {
                            if (strpos($function, ':') !== false) {
                                list($function, $params) = explode(':', $function);
                                $params = array_merge([$value], explode(',', $params));
                            } else {
                                $params = [$value];
                            }
                            $value = $this->{$function}(...$params);
                        }
                    }
                    if (is_null($value) || (is_array($value) && empty($value))) {
                        $query->andWhere('1 = 0');
                    } elseif (is_array($value)) {
                        $condition = str_replace($tmp[0], '{' . $key . ':array}', $condition);
                        $query->andWhere($condition, [$key => $value]);
                    } else {
                        $condition = str_replace($tmp[0], ':' . $key . ':', $condition);
                        $query->andWhere($condition, [$key => $value]);
                    }
                } else {
                    $query->andWhere($condition);
                }
            }
        }

        $orderBy = preg_replace('/[^a-z0-9_\.]+/', '', $order['by']);
        $orderDirection = strtolower($order['direction']) == 'asc' ? 'ASC' : 'DESC';
        $query->orderBy($orderBy . ' ' . $orderDirection);

        if ($limit == 0 || $limit > 1000) {
            $limit = 1000;
        }

        if ($page == 0) {
            $page = 1;
        }

        return (new PaginatorQueryBuilder([
            'builder' => $query,
            'limit' => $limit,
            'page' => $page,
        ]))->getPaginate();
    }

}
