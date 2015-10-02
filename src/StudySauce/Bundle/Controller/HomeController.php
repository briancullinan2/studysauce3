<?php

namespace StudySauce\Bundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HomeController
 * @package StudySauce\Bundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        list($route, $options) = self::getUserRedirect($user);
        if($route != 'home')
            return $this->redirect($this->generateUrl($route, $options));

        $showBookmark = false; // TODO: false in production
        if(empty($user->getProperty('seen_bookmark'))) {
            $showBookmark = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_bookmark', true);
            $userManager->updateUser($user);
        }

        return $this->render(
            'StudySauceBundle:Home:tab.html.php',
            [
                'showBookmark' => $showBookmark,
                'user' => $user,
            ]
        );
    }

    /**
     * @param User $user
     * @return array|string
     */
    public static function getUserRedirect(User $user)
    {
        if($user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST'))
            return ['_welcome', []];
            // TODO: split this in to separate pages
        elseif($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER'))
            return ['userlist', []];
        elseif($user->hasRole('ROLE_PARENT'))
            return ['thanks', []];
        return ['home', []];
    }
}