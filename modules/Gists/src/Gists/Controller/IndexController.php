<?php

namespace Gists\Controller;

use Gists\Mvc\Controller\RestfulController,
    Zend\Http\Exception\InvalidArgumentException,
    Zend\Mvc\MvcEvent;

class IndexController extends RestfulController
{
    /**
     * Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {
        if ($filter = $this->getEvent()->getRouteMatch()->getParam('filter')) {
            return 'GET gists/' . $filter;
        }
        return 'GET gists';
    }

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function get($id)
    {
        if ($property = $this->getEvent()->getRouteMatch()->getParam('property')) {
            return 'GET gists/:' . $id . '/:' . $property;
        }
        return 'GET gists/:' . $id;
    }

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return mixed
     */
    public function create($data)
    {
        return 'POST /gists';
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
        if ($property = $this->getEvent()->getRouteMatch()->getParam('property')) {
            return 'PUT gists/:' . $id . '/:' . $property;
        }
        throw new \DomainException('Missing entity');
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
        return 'PATCH gists/:' . $id;
    }

    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function delete($id)
    {
        if ($property = $this->getEvent()->getRouteMatch()->getParam('property')) {
            return 'DELETE gists/:' . $id . '/:' . $property;
        }
        return 'DELETE gists/:' . $id;
    }

}
