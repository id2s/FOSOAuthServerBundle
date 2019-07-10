<?php

declare(strict_types=1);

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\Controller;

use FOS\OAuthServerBundle\Event\TokenEvent;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController
{
    /**
     * @var OAuth2
     */
    protected $server;

    /**
     * @param OAuth2 $server
     */
    public function __construct(OAuth2 $server)
    {
        $this->server = $server;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(EventDispatcherInterface $eventDispatcher, Request $request)
    {
        try {
            $response = $this->server->grantAccessToken($request);

            $event = $this->eventDispatcher->dispatch(
                TokenEvent::TOKEN_SUCCESS,
                new TokenEvent($request->request->all()['username'])
            );

            return $response;
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }
}
