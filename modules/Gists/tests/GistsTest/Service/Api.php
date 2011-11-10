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
    {
        $gist = $this->createGist('testGetGists1', 'function foo() {}', 1); // create starred
        $gist = $this->createGist('testGetGists2', 'function bar() {}');

        $response = $this->getService()
                         ->getList();

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Ok', $response->getReasonPhrase());

        $gists = Json::decode(stripslashes($response->getBody()));
        $this->assertSame(2, count($gists));

        $this->assertSame(1, $gists[0]->id);
        $this->assertSame('/users/1', $gists[0]->user);
        $this->assertSame('testGetGists1', $gists[0]->description);
        $this->assertSame(1, $gists[0]->starred);

        $this->assertSame(2, $gists[1]->id);
        $this->assertSame('/users/1', $gists[1]->user);
        $this->assertSame('testGetGists2', $gists[1]->description);
        $this->assertSame(0, $gists[1]->starred);
    }

    /**
     * GET /gists/starred
     */
    public function testGetGistsStarred()
    {
        $gist = $this->createGist('testGetGistsStarred1', 'function bar() {}');
        $gist = $this->createGist('testGetGistsStarred2', 'function foo() {}', 1); // create starred

        $response = $this->getService()
                         ->getList('starred');

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Ok', $response->getReasonPhrase());

        $gists = Json::decode(stripslashes($response->getBody()));
        $this->assertSame(1, count($gists));

        $this->assertSame(2, $gists[0]->id);
        $this->assertSame('/users/1', $gists[0]->user);
        $this->assertSame('testGetGistsStarred2', $gists[0]->description);
        $this->assertSame(1, $gists[0]->starred);
    }

    /**
     * GET /gist/:id
     */
    public function testGetSingleGist()
    {
        $gist = $this->createGist('testGetSingleGist', 'function bar() {}');

        $response = $this->getService()
                         ->get(1);

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Ok', $response->getReasonPhrase());

        $gist = Json::decode(stripslashes($response->getBody()));
        $this->assertSame(1, $gist->id);
        $this->assertSame('/users/1', $gist->user);
        $this->assertSame('testGetSingleGist', $gist->description);
    }

    /**
     * POST /gists
     */
    public function testCreateGistFailed()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;

        $response = $this->getService()
            ->create(Json::encode($repr));

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Bad Request', $response->getReasonPhrase());
    }

    /**
     * POST /gists
     * Entity manager is closed after exception so we get new one for each testable
     */
    public function testCreateGistOk()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;
        $repr->description = 'Some desc';
        $repr->content = 'function foo() {}';

        $response = $this->getService()
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
    {
        $gist = $this->createGist('testIsGistStared1', 'function foo() {}', 1);
        $gist = $this->createGist('testIsGistStared2', 'function bar() {}');

        $response = $this->getService()
                         ->isStarred(1);

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('No Content', $response->getReasonPhrase());

        $response = $this->getService()
                         ->isStarred(2);

        $this->assertInstanceOf('\Zend\\Http\\Response', $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Not Found', $response->getReasonPhrase());
    }

    /**
     * DELETE /gists/:id
     */
    public function testDeleteGist()
    {}

    /**
     * Initialize and get service
     *
     * @return Gists\Service\Api
     */
    protected function getService()
    {
        return $this
            ->getLocator()->get('api')
            ->setUserCredentials('horus', 'mypass');
    }

    protected function createGist($description, $content, $starred = 0)
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;
        $repr->description = $description;
        $repr->content = $content;
        $repr->starred = $starred;

        $response = $this->service
            ->setUserCredentials('horus', 'mypass')
            ->create(Json::encode($repr));

        return $response->headers()->get('Location');
    }
}
