<?php

namespace Application\Http\PhpEnvironment;

use Zend\Http\PhpEnvironment\Request as BaseRequest;

class Request extends BaseRequest
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
