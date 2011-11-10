<?php

namespace Gists\Service;
use SpiffyDoctrine\Service\Doctrine;
use Zend\Json\Json,
    Zend\Http,
    Gists\Entity\Gist as GistEntity,
    Gists\Entity\User as UserEntity;



class Api
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Username and password is provided with each API call (HTTP Auth used)
     */
    protected $username;
    protected $password;

    public function __construct(Doctrine $doctrine)
    {
        $this->em = $doctrine->getEntityManager();
    }

    public function setUserCredentials($username, $password)
    {
        // no validation, or auth checking is done
        // but since we have necessary variables doing so will be trivial
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    public function create($data)
    {
        $repr = Json::decode($data);

        $user = $this->getUserEntity(); // get user repository of calling user
        if ($user) {
            $gist = new GistEntity();
            $gist->setDescription(isset($repr->description)?$repr->description:null);
            $gist->setContent(isset($repr->content)?$repr->content:null);
            $user->addGist($gist);
            try {
                $this->em->flush();
                return $this->generateResponse(201, 'Created', null, array(
                    'Location' => sprintf('/gists/%d', $gist->getId())
                ));
            } catch (\Exception $e) {
                // log, process error, re-throw
            }
        }

        return $this->generateResponse(400, 'Bad Request');
    }

    protected function getUserEntity()
    {
        return $this->em
            ->getRepository('Gists\Entity\User')
            ->findOneBy(array('username' => $this->username));
    }

    protected function generateResponse($statusCode, $reason, $content = null, $headers = array())
    {
        $response = new \Zend\Http\PhpEnvironment\Response;
        $response->setStatusCode($statusCode);
        $response->setReasonPhrase($reason);

        $headers['Content-type'] = 'application/json';

        if ($content) {
            $response->setContent($content);
        }
        if ($headers) {
            $response->headers()->addHeaders($headers);
        }
        return $response;
    }
}
