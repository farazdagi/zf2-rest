<?php
namespace GistsTest\Service;

use Zend\Json\Json;
use GistsTest\Framework\TestCase;


class ApiTest extends TestCase
{
    /**
     * @var Gists\Service\Api
     */
    protected $service;

    public function setUp()
    {
        parent::setup();    // manners
        $this->createDb();  // pdo_sqlite test db
        $this->service = $this->getLocator()->get('api');
    }

    public function tearDown()
    {
        $this->service = null;
    }

    /**
     * GET /gists
     */
    public function testGetGists()
    {}

    /**
     * GET /gists/starred
     */
    public function testGetGistsStarred()
    {}

    /**
     * GET /gist/:id
     */
    public function testGetSingleGist()
    {}

    /**
     * POST /gists
     * @group cur
     */
    public function testCreateGistFailed()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;

        $response = $this->service
            ->setUserCredentials('horus', 'mypass')
            ->create(Json::encode($repr));

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Bad Request', $response->getReasonPhrase());
    }

    /**
     * POST /gists
     * Entity manager is closed after exception so we get new one for each testable
     * @group cur
     */
    public function testCreateGistOk()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;
        $repr->description = 'Some desc';
        $repr->content = 'function foo() {}';

        $response = $this->service
            ->setUserCredentials('horus', 'mypass')
            ->create(Json::encode($repr));

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Created', $response->getReasonPhrase());
    }

    /**
     * PATCH /gists/:id
     */
    public function testEditGist()
    {}

    /**
     * PUT /gists/:id/star
     * DELETE /gists/:id/star
     */
    public function testStarUnstarGist()
    {}

    /**
     * GET /gists/:id/star
     */
    public function testIsGistStared()
    {}

    /**
     * DELETE /gists/:id
     */
    public function testDeleteGist()
    {}

}
