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
        $filter = $this
            ->getEvent()
            ->getRouteMatch()
            ->getParam('filter');
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
