<?php
return array(
    'di'    => array(
        'instance' => array(
            'alias' => array(
                'gists_index' => 'Gists\Controller\IndexController',
            ),
        ),
    ),
    'routes' => array(
        'gists' => array(
            'type'    => 'Zend\Mvc\Router\Http\Regex',
            'options' => array(
                'regex'    => '/gists(/(?<id>[0-9]*[^/]+)(/(?<property>[^/]+))?)?',
                'spec'     => '/%controller%/%id%/%property%',
                'defaults' => array(
                    'controller' => 'gists_index',
                ),
            ),
        ),
        'gists_starred' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route'    => '/gists/starred',
                'defaults' => array(
                    'controller' => 'gists_index',
                    'filter'     => 'starred'
                ),
            ),
        ),
    ),
);
