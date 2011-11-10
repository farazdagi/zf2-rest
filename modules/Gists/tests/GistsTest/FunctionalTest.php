<?php
namespace GistsTest;

use Zend\Json\Json;


class BundleTest extends \PHPUnit_Framework_TestCase
{
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
    public function testCreateGist()
    {
        // try to create gist w/o providing required field
        $repr = new \StdClass;

        $result = $this
            ->getCurl()
            ->post('/gists', Json::encode($repr));

        $this->assertSame('HTTP/1.1 400 Bad Request', $result['status']);

        // now add required data and retry
        $repr->description = 'Some desc';
        $repr->content = 'function foo() {}';

        $result = $this
            ->getCurl()
            ->post('/gists', Json::encode($repr));

        $this->assertSame('HTTP/1.1 201 Created', $result['status']);
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

    protected function getCurl()
    {
        return new Curl();
    }
}

class Curl
{
    private $baseUri = 'http://api.zfbook.com';
    private $user = 'horus:mypass';

    public function post($uri, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->baseUri . $uri);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        $headers = substr($response, 0, $info['header_size']);
        $body = '';
        if ($info['download_content_length']) {
            $body = substr($response, -$info['download_content_length']);
        }
        $status = strtok($headers, "\r\n");

        return array(
            'headers'   => $headers,
            'body'      => $body,
            'status'    => $status,
        );
    }
}
