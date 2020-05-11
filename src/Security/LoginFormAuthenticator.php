<?php
namespace App\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use App\Repository\UserRepository;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
   public function supports(Request $request)
    {


    }
    public function getCredentials(Request $request)
    {

        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password')


        ];

    }
    public function getUser($credentials, UserProviderInterface $userProvider)
    {


    }
    public function checkCredentials($credentials, UserInterface $user)
    {


    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }
    public function start(Request $request, AuthenticationException $authException = null)
    {

    }
    public function supportsRememberMe()
    {
    // todo
    }
    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('dashboard');
    }
}