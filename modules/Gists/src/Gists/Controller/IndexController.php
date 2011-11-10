<?php

namespace Gists\Controller;

use Gists\Mvc\Controller\RestfulController,
    Zend\Http\Exception\InvalidArgumentException,
    Zend\Mvc\MvcEvent,
    Zend\Http;

class IndexController extends RestfulController
{
    /**
     * Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {
        $filter = $this->getEvent()->getRouteMatch()->getParam('filter', null);
        return $this->getService()
                    ->getList($filter);
    }

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function get($id)
    {
        $property = $this->getEvent()->getRouteMatch()->getParam('property', null);
        if ($property == 'star') {
            return $this->getService()
                        ->isStarred($id);
        }
        return $this->getService()
                    ->get($id);
    }

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return mixed
     */
    public function create($data)
    {
        return $this->getService()
                    ->create($data);
    }

    /**
     * Update an existing gist property (property should be entity)
     *
     * @see Entity pattern http://developer.mindtouch.com/REST/REST_for_the_Rest_of_Us
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    public function update($id, $data)
    {
        $property = $this->getEvent()->getRouteMatch()->getParam('property');
        if ($property) {
            return $this->getService()
                        ->putProperty($id, $property, $data);
        }
        return $this->generateResponse(400, 'Bad Request');
    }

    /**
     * Update existing gist
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    public function patch($id, $data)
    {
        return $this->getService()
                    ->patch($id, $data);
    }

    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function delete($id)
    {
        $property = $this->getEvent()->getRouteMatch()->getParam('property');
        if ($property) {
            return $this->getService()
                        ->deleteProperty($id, $property);
        }
        return $this->getService()
                    ->delete($id);
    }

    /**
     * Initialize and get service
     *
     * @return Gists\Service\Api
     */
    protected function getService()
    {
        return $this
            ->getLocator()->get('api')
            ->setUserCredentials(
                $this->getRequest()->server()->get('PHP_AUTH_USER', null),
                $this->getRequest()->server()->get('PHP_AUTH_PW', null)
            );
    }

}
