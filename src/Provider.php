<?php

namespace SocialiteProviders\FeiShu;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\InvalidStateException;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;


class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'feishu';

    protected function getAuthUrl($state)
    {
        $fields = [
            'app_id' => $this->clientId,
            'state' => $state,
            'redirect_uri' => $this->redirectUrl,
        ];

        $fields = array_merge($fields, $this->parameters);

        return 'https://open.feishu.cn/open-apis/authen/v1/index?'.http_build_query($fields);
    }

    protected function getTokenUrl()
    {
        return 'https://open.feishu.cn/open-apis/authen/v1/access_token';
    }

    protected function getUserByToken($token)
    {

    }

    protected function mapUserToObject(array $user)
    {

    }
}
