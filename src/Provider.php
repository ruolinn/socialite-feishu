<?php

namespace SocialiteProviders\FeiShu;

use Illuminate\Support\Arr;
use GuzzleHttp\RequestOptions;
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

    protected function getTokenHeaders($code)
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json; charset=utf-8',
        ];
    }

    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'app_id' => $this->clientId,
            'app_secret' => $this->clientSecret,
        ];
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://open.feishu.cn/open-apis/authen/v1/user_info',
            [
                RequestOptions::HEADERS => ['Authorization' => 'Bearer '.$token]
            ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        $user = $user['data'];

        return (new User)->setRaw($user)->map([
            'id' => $user['open_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'nickname' => $user['en_name'],
            'avatar' => $user['avatar_big'],
        ]);
    }

    protected function parseAccessToken($body)
    {
        return Arr::get($body['data'], 'access_token');
    }

    protected function parseRefreshToken($body)
    {
        return Arr::get($body['data'], 'refresh_token');
    }

    protected function parseExpiresIn($body)
    {
        return Arr::get($body['data'], 'refresh_expires_in');
    }

    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::HEADERS => $this->getTokenHeaders($code),
            RequestOptions::JSON => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }
}
