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
        try {
            $response = parent::execute($e);
            return $response;
        } catch (\DomainException $ex) {
            if ('Invalid HTTP method!' !== $ex->getMessage()) {
                throw $ex;
            }
        }

        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();

        // custom RESTful methods
        switch (strtolower($request->getMethod())) {
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
            default:
                throw new \DomainException('Invalid HTTP method!');
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        $e->setResult($return);
        return $return;
    }
}
