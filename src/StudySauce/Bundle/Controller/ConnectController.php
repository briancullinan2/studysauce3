<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StudySauce\Bundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseController;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * ConnectController
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class ConnectController extends BaseController
{
    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request The active request.
     * @param string $service Name of the resource owner to connect to.
     *
     * @throws \Exception
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if no user is authenticated
     */
    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            throw new AccessDeniedException('Cannot connect an account.');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->container->get('hwi_oauth.security.oauth_utils')->getServiceAuthUrl($request, $resourceOwner)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.' . $key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.' . $key);
        }

        /** @var $currentToken OAuthToken */
        $currentToken = $this->container->get('security.context')->getToken();
        $currentUser = $currentToken->getUser();

        if (!empty($accessToken)) {
            $userInformation = $resourceOwner->getUserInformation($accessToken);

           $this->container->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);

            if ($currentToken instanceof OAuthToken) {
                // Update user token with new details
                $this->authenticateUser($request, $currentUser, $service, $currentToken->getRawToken(), false);
            }

        }

        // TODO: Show confirmation page?
        $target = $session->get('_security.main.target_path');
        if (empty($target)) {
            list($route, $options) = HomeController::getUserRedirect($currentUser, $this->container);

            return new RedirectResponse($this->container->get('router')->generate($route, $options));
        } else {
            $session->remove('_security.main.target_path');

            return new RedirectResponse($target);
        }
    }

}
