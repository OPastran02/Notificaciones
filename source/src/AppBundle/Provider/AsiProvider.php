<?php


namespace AppBundle\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AsiProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private $url;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options);
        parent::__construct($collaborators);

        if(array_key_exists('ADUrl',$options)){
            $this->url= $options['ADUrl'];
        }
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->url.'/oidc/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->url.'/oidc/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->url.'/oidc/userinfo';
        // You don't have one. You might consider throwing an exception here so
        // that, when this is called, you get an error and can code your
        // application to ensure that nothing calls this.
        //
        // Note that $this->getResourceOwner() is the most likely culprit for
        // calling this. Just don't call getResourceOwner() in your code.
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        /*var_dump($response);
        var_dump("<br><br><br><br><br>");
        var_dump($data);
        exit();*/
        // Write code here that checks the response for errors and throws
        // an exception if you find any.
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        /*var_dump($token);
        var_dump("<br><br><br><br><br>");
        var_dump($response);
        exit();*/
        // Leave empty. You can't use this, since you don't have a clear
        // resource owner details URL. You might consider throwing an
        // exception from here, as well. See note on
        // getResourceOwnerDetailsUrl() above.
    }

}