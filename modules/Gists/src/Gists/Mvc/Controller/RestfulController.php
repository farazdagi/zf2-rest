<?php

namespace Gists\Mvc\Controller;

use Zend\Mvc\Controller\RestfulController as BaseRestfulController,
    Zend\Http\Exception\InvalidArgumentException,
    Zend\Mvc\MvcEvent;

abstract class RestfulController extends BaseRestfulController
{
    /**
     * Update existing gist
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    abstract public function patch($id, $data);

    public function execute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new \DomainException('Missing route matches; unsure how to retrieve action');
        }

        $request = $e->getRequest();
        $action  = $routeMatch->getParam('action', false);
        if ($action) {
            // Handle arbitrary methods, ending in Action
            $method = static::getMethodFromAction($action);
            if (!method_exists($this, $method)) {
                $method = 'notFoundAction';
            }
            $return = $this->$method();
        } else {
            // RESTful methods
            switch (strtolower($request->getMethod())) {
                case 'get':
                    if (null !== $id = $routeMatch->getParam('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    if (null !== $id = $request->query()->get('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    $return = $this->getList();
                    break;
                case 'post':
                    $return = $this->create($request->getRawPostData());
                    break;
                case 'patch':
                    if (null === $id = $routeMatch->getParam('id')) {
                        if (!($id = $request->query()->get('id', false))) {
                            throw new \DomainException('Missing identifier');
                        }
                    }
                    $content = $request->getContent();
                    parse_str($content, $parsedParams);
                    $return = $this->patch($id, $parsedParams);
                    break;
                case 'put':
                    if (null === $id = $routeMatch->getParam('id')) {
                        if (!($id = $request->query()->get('id', false))) {
                            throw new \DomainException('Missing identifier');
                        }
                    }
                    $content = $request->getContent();
                    parse_str($content, $parsedParams);
                    $return = $this->update($id, $parsedParams);
                    break;
                case 'delete':
                    if (null === $id = $routeMatch->getParam('id')) {
                        if (!($id = $request->query()->get('id', false))) {
                            throw new \DomainException('Missing identifier');
                        }
                    }
                    $return = $this->delete($id);
                    break;
                default:
                    throw new \DomainException('Invalid HTTP method!');
            }
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        $e->setResult($return);
        return $return;
    }
}
