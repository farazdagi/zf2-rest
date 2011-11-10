<?php
namespace GistsTest;

use Zend\Json\Json;


class FunctionalTest extends Framework\TestCase
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

    /**
     * GET /gists
     */
    public function testGetGists()
    {
        // test not found
        $result = $this
            ->getCurl()
            ->request('GET', '/gists');
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);

        $gist = $this->createGist('testGetSingleGist1', 'function foo() {}', 1); // create starred
        $gist = $this->createGist('testGetSingleGist2', 'function bar() {}');

        $result = $this
            ->getCurl()
            ->request('GET', '/gists');

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gists = Json::decode(stripslashes($result['body']));
        $this->assertSame(2, count($gists));

        $this->assertSame(1, $gists[0]->id);
        $this->assertSame('/users/1', $gists[0]->user);
        $this->assertSame('testGetSingleGist1', $gists[0]->description);
        $this->assertSame(1, $gists[0]->starred);

        $this->assertSame(2, $gists[1]->id);
        $this->assertSame('/users/1', $gists[1]->user);
        $this->assertSame('testGetSingleGist2', $gists[1]->description);
        $this->assertSame(0, $gists[1]->starred);
    }

    /**
     * GET /gists/starred
     */
    public function testGetGistsStarred()
    {
        // test not found
        $result = $this
            ->getCurl()
            ->request('GET', '/gists/starred');
        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);

        $gist = $this->createGist('testGetSingleGist1', 'function foo() {}');
        $gist = $this->createGist('testGetSingleGist2', 'function bar() {}', 1); // create starred

        $result = $this
            ->getCurl()
            ->request('GET', '/gists/starred');

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gists = Json::decode(stripslashes($result['body']));
        $this->assertSame(1, count($gists));

        $this->assertSame(2, $gists[0]->id);
        $this->assertSame('/users/1', $gists[0]->user);
        $this->assertSame('testGetSingleGist2', $gists[0]->description);
        $this->assertSame(1, $gists[0]->starred);
    }

    /**
     * GET /gist/:id
     */
    public function testGetSingleGist()
    {
        // test not found
        $result = $this
            ->getCurl()
            ->request('GET', '/gists/' . 42);

        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);

        $gist = $this->createGist('testGetSingleGist', 'function bar() {}');
        $result = $this
            ->getCurl()
            ->request('GET', $gist->value);

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gist = Json::decode(stripslashes($result['body']));
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

        $result = $this
            ->getCurl()
            ->request('POST', '/gists', Json::encode($repr));

        $this->assertSame('HTTP/1.1 400 Bad Request', $result['status']);
    }

    /**
     * POST /gists
     */
    public function testCreateGistOK()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;
        $repr->description = 'Some desc';
        $repr->content = 'function foo() {}';

        $result = $this
            ->getCurl()
            ->request('POST', '/gists', Json::encode($repr));

        $this->assertSame('HTTP/1.1 201 Created', $result['status']);
        $this->assertTrue(isset($result['headers']['Location']));
        $this->assertSame('/gists/1', $result['headers']['Location']);
    }

    /**
     * PATCH /gists/:id
     */
    public function testEditGist()
    {
        $gist = $this->createGist('testEditGist', 'function foo() {}', 1);

        $result = $this
            ->getCurl()
            ->request('GET', '/gists/1');

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gist = Json::decode(stripslashes($result['body']));
        $this->assertSame(1, $gist->id);
        $this->assertSame('/users/1', $gist->user);
        $this->assertSame('testEditGist', $gist->description);

        // edit
        $repr = new \StdClass;
        $repr->description = 'testEditGistUpdated';
        $result = $this
            ->getCurl()
            ->request('PATCH', '/gists/1', Json::encode($repr));

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gist = Json::decode(stripslashes($result['body']));
        $this->assertSame(1, $gist->id);
        $this->assertSame('/users/1', $gist->user);
        $this->assertSame('testEditGistUpdated', $gist->description);

        // check if edited
        $result = $this
            ->getCurl()
            ->request('GET', '/gists/1');

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gist = Json::decode(stripslashes($result['body']));
        $this->assertSame(1, $gist->id);
        $this->assertSame('/users/1', $gist->user);
        $this->assertSame('testEditGistUpdated', $gist->description);
    }

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
        $gist1 = $this->createGist('testIsGistStared1', 'function foo() {}', 1);
        $gist2 = $this->createGist('testIsGistStared2', 'function bar() {}');

        $result = $this
            ->getCurl()
            ->request('GET', $gist1->value . '/star');

        $this->assertSame('HTTP/1.1 204 No Content', $result['status']);

        $result = $this
            ->getCurl()
            ->request('GET', $gist2->value . '/star');

        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);
    }

    /**
     * DELETE /gists/:id
     */
    public function testDeleteGistNonExistent()
    {
        // delete no-exitent gist
        $result = $this
            ->getCurl()
            ->request('DELETE', '/gists/42');

        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);
    }

    /**
     * DELETE /gists/:id
     */
    public function testDeleteGist()
    {
        // create gist
        $gist = $this->createGist('testDeleteGist', 'function bar() {}');

        // ensure that gist exists
        $result = $this
            ->getCurl()
            ->request('GET', $gist->value);

        $this->assertSame('HTTP/1.1 200 OK', $result['status']);

        $gist = Json::decode(stripslashes($result['body']));
        $this->assertSame(1, $gist->id);
        $this->assertSame('/users/1', $gist->user);
        $this->assertSame('testDeleteGist', $gist->description);

        // delete
        $result = $this
            ->getCurl()
            ->request('DELETE', $gist->url);

        $this->assertSame('HTTP/1.1 204 No Content', $result['status']);

        // ensure that gist was delete
        $result = $this
            ->getCurl()
            ->request('GET', $gist->url);

        $this->assertSame('HTTP/1.1 404 Not Found', $result['status']);
    }

    protected function getCurl()
    {
        return new Curl();
    }

    protected function createGist($description, $content, $starred = 0)
    {
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

class Curl
{
    private $baseUri = 'http://api.zfbook.com';
    private $user = 'horus:mypass';

    public function request($method, $uri, $data = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->baseUri . $uri);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Length: ' . strlen($data),
                'Content-Type: application/json'
            ));
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        $header = substr($response, 0, $info['header_size']);
        $body = '';
        if ($info['download_content_length']) {
            $body = substr($response, -$info['download_content_length']);
        }
        $status = strtok($header, "\r\n");

        $headers = array();
        foreach (explode("\r\n", $header) as $header) {
            if (trim($header) && strpos($header, ':') !== false) {
                list($key, $value) = explode(':', $header);
                $headers[trim($key)] = trim($value);
            }
        }
        return array(
            'headers'   => $headers,
            'body'      => $body,
            'status'    => $status,
        );
    }
}
