<?php

namespace Iprbooks\Ebs\Sdk\Managers;

use Exception;
use Iprbooks\Ebs\Sdk\Client;
use Iprbooks\Ebs\Sdk\Core\Response;
use Iprbooks\Ebs\Sdk\Models\User;

class IntegrationManager extends Response
{

    /**
     * Конструктор IntegrationManager
     * @param $client
     * @return IntegrationManager
     * @throws Exception
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
        if (!$client) {
            throw new Exception('client is not init');
        }
        return $this;
    }

    /**
     * Возвращает ссылку на активацию ключа и авторизацию данного пользователя.
     * @param int $userId
     * @param null $publicationId
     * @return mixed
     */
    public function generateToken($userId, $publicationId = null)
    {
        $apiMethod = '/2.0/security/generateToken/{id}';
        $apiMethod = str_replace('{id}', $userId, $apiMethod);
        $params = array('publication_id' => $publicationId);

        $this->response = $this->getClient()->makeRequest($apiMethod, $params);
        $this->data = $this->response['data'];
        return $this->data;
    }

    /**
     * @param $email
     * @param $fullname
     * @param int $userType
     * @param null $publicationId
     * @param $isOpenMethod
     * @return mixed
     */
    public function generateAutoAuthUrl($email, $fullname, $userType = User::OTHER, $publicationId = null, $isOpenMethod = false)
    {
        $apiMethod = '/2.0/security/generateAutoAuthUrl';

        $params = array(
            'email' => $email,
            'fullname' => $fullname,
            'user_type' => $userType,
            'publication_id' => $publicationId,
            'open_method' => $isOpenMethod ? 'iframe' : ''
        );

        $this->response = $this->getClient()->makeRequest($apiMethod, $params);
        $this->data = $this->response['data'];
        return $this->data;
    }

}