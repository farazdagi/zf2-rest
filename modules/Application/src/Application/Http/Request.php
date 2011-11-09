<?php

namespace Application\Http;

use Zend\Http\Request as BaseRequest,
    Zend\Stdlib\RequestDescription,
    Zend\Stdlib\Message,
    Zend\Stdlib\ParametersDescription,
    Zend\Stdlib\Parameters,
    Zend\Uri\Http as HttpUri,
    Zend\Mvc\PhpEnvironment;

class Request extends BaseRequest implements RequestDescription
{

    /**#@+
     * @const string METHOD constant names
     */
    const METHOD_PATCH = 'PATCH';
    /**#@-*/

    /**
     * Is this a PATCH method request?
     *
     * @return bool
     */
    public function isPatch()
    {
        return ($this->method === self::METHOD_PATCH);
    }

    /**
     * Get the request object
     *
     * Hack as factory breaks SRP and ISP principles. Still good enough for current purposes.
     *
     * @return Request
     */
    public static function factory()
    {
        $request = new self();

        $request->setQuery(new PhpEnvironment\GetContainer())
                ->setPost(new PhpEnvironment\PostContainer())
                ->setEnv(new Parameters($_ENV))
                ->setServer(new Parameters($_SERVER));

        if ($_COOKIE) {
            $request->headers()->addHeader(new Cookie($_COOKIE));
        }

        if ($_FILES) {
            $request->setFile(new Parameters($_FILES));
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $request->setMethod($_SERVER['REQUEST_METHOD']);
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $request->setUri($_SERVER['REQUEST_URI']);
        }

        return $request;
    }

    /**
     * Set the method for this request
     *
     * It is safe to remove this method when/if pull request is procesed
     * https://github.com/zendframework/zf2/pull/576
     *
     * @param string $method
     * @return Request
     */
    public function setMethod($method)
    {
        if (!defined('static::METHOD_'.strtoupper($method))) {
            throw new Exception\InvalidArgumentException('Invalid HTTP method passed');
        }
        $this->method = $method;
        return $this;
    }
}
