<?php
namespace GistsTest\Framework;
use PHPUnit_Framework_TestCase as BaseTestCase,
    Zend\Di\Di,
    Zend\Di\Configuration as DiConfiguration,
    Zend\Di\Exception\ClassNotFoundException,
    Zend\Di\Locator,
    SpiffyDoctrine\Service\Doctrine,
    Gists\Entity\User as UserEntity,
    Gists\Entity\Gist as GistEntity;

class TestCase extends BaseTestCase
{
    public static $config;

    /**
     * @var SpiffyDoctrine\Service\Doctrine
     */
    protected $_service;

    public function setup()
    {
        $this->setupLocator();
    }

    /**
     * Sets up the locator based on the configuration provided
     *
     * @return void
     */
    protected function setupLocator()
    {
        $di = new Di;
        $di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

        $config = new DiConfiguration(self::$config['di']);
        $config->configure($di);

        $this->setLocator($di);
    }

    /**
     * Set a service locator/DI object
     *
     * @param  Locator $locator
     * @return GistsTest\Framework\TestCase
     */
    public function setLocator(Locator $locator)
    {
        $this->locator = $locator;
        return $this;
    }

    /**
     * Get the locator object
     *
     * @return null|Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Creates a database
     */
    public function createDb()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array(
            $em->getClassMetadata('Gists\Entity\User'),
            $em->getClassMetadata('Gists\Entity\Gist'),
        );
        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        // populate
        $user = new UserEntity;
        $user->setUsername('horus');
        $em->persist($user);
        $em->flush();
    }

    /**
     * Get EntityManager.
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrineService()->getEntityManager();
    }

    /**
     * Get Doctrine service.
     *
     * @return SpiffyDoctrine\Service\Doctrine
     */
    public function getDoctrineService()
    {
        if (null === $this->_service) {
            $config = self::$config['di']['instance']['doctrine']['parameters'];
            $this->_service = new Doctrine(
                $config['conn'],
                $config['config'],
                $config['evm']
            );
        }
        return $this->_service;
    }
}
