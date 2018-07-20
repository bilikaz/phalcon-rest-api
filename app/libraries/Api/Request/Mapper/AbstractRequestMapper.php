<?php
declare(strict_types=1);

namespace App\Library\Api\Request\Mapper;

use Exception;

use App\Library\Api\Request,
    App\Library\Api\Filter\FilterInterface,
    App\Library\Api\Exception\IncorrectFieldsException;

abstract class AbstractRequestMapper implements RequestMapperInterface
{

    protected $fields;
    protected $entityClassName = 'App\Library\Api\Request\Mapper\Entity\BasicRequestEntity';

    public function __construct(array $fields = null, string $entityClassName = null)
    {
        if (isset($fields)) {
            $this->fields = $fields;
        }
        if (isset($entityClassName)) {
            $this->entityClassName = $entityClassName;
        }
    }

    abstract protected function validateContentType($contentType);

    abstract protected function bodyToArray($requestBody);

    public function mapRequest(Request $request)
    {
        $contentType = $request->getHeader('Content-Type');
        $this->validateContentType($contentType);

        $values = $request->get();
        $values = array_merge($values, $this->bodyToArray($request->getRawBody()));

        $className = $this->entityClassName;
        $entity = new $className();

        if (!isset($this->fields)) {
            foreach ($values as $field => $value) {
                $entity->{$field} = $value;
            }
        } else {
            $incorrectFields = [];
            foreach ($this->fields as $field => $params) {
                if (is_array($params)) {
                    if (isset($params['field'])) {
                        $requestField = $params['field'];
                    } else {
                        $requestField = $field;
                    }
                    if (isset($params['optional'])) {
                        $optional = $params['optional'];
                    } else {
                        $optional = false;
                    }
                    if (!isset($values[$requestField]) && !$optional) {
                        $incorrectFields[$requestField] = 'Is missing';
                        continue;
                    }
                    if (isset($values[$requestField])) {
                        if (isset($params['filter'])) {
                            try {
                                if ($params['filter'] instanceof FilterInterface) {
                                    $value = $params['filter']->filter($values[$requestField], $values);
                                } else {
                                    $value = $params['filter']($values[$requestField], $values);
                                }
                            } catch (Exception $e) {
                                $incorrectFields[$requestField] = $e->getMessage();
                                continue;
                            }
                            if (!isset($value)) {
                                if (!$optional) {
                                    $incorrectFields[$requestField] = 'Invalid value';
                                }
                                continue;
                            }
                        } else {
                            $value = $values[$requestField];
                        }
                        $entity->{$field} = $value;
                    }
                } else {
                    if (!isset($values[$params])) {
                        $incorrectFields[$params] = 'Is missing';
                        continue;
                    }
                    $entity->{$params} = $values[$params];
                }
            }
            if ($incorrectFields) {
                $exception = new IncorrectFieldsException();
                $exception->setFields($incorrectFields);
                throw $exception;
            }
        }
        return $entity;
    }

}
