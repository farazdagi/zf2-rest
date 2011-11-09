<?php

namespace Gists\Service;
use SpiffyDoctrine\Service\Doctrine;


class Api
{
    public function __construct(Doctrine $doctrine)
    {
        var_dump($doctrine->getEntityManager());
    }
}
