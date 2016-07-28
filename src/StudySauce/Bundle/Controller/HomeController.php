<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class HomeController
 * @package StudySauce\Bundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, User $user = null)
    {
        /** @var User $user */
        if(empty($user)) {
            $user = $this->getUser();
        }

        $templateVars = ['_format' => $request->get('_format')];

        // display the currently logged in user
        if(!empty($user) && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {
            $templateVars['email'] = $user->getEmail();
            $templateVars['id'] = $user->getId();
            $templateVars['first'] = $user->getFirst();
            $templateVars['last'] = $user->getLast();
            $templateVars['properties'] = array_map(function ($p) {
                if($p instanceof \DateTime) {
                    return $p->format('r');
                }
                return $p;
            }, $user->getProperties());
            $templateVars['created'] = $user->getCreated()->format('r');
            $templateVars['roles'] = implode(',', $user->getRoles());
            $templateVars['children'] = [];
            if($user->hasRole('ROLE_PARENT') || $user->hasRole('ROLE_TEACHER')) {
                foreach($user->getInvites()->toArray() as $invite) {
                    /** @var Invite $invite */
                    if(empty($invite->getInvitee())) {
                        continue;
                    }
                    $templateVars['children'][] = [
                        'id' => $invite->getInvitee()->getId(),
                        'first' => $invite->getFirst(),
                        'last' => $invite->getLast(),
                        'properties' => array_map(function ($p) {
                            if($p instanceof \DateTime) {
                                return $p->format('r');
                            }
                            return $p;
                        }, $invite->getInvitee()->getProperties()),
                        'email' => $invite->getInvitee()->getEmail(),
                        'created' => $invite->getInvitee()->getCreated()->format('r'),
                        'roles' => implode(',', $invite->getInvitee()->getRoles())
                    ];
                }
            }
        }

        $showBookmark = false; // TODO: false in production
        if(empty($user->getProperty('seen_bookmark'))) {
            $showBookmark = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_bookmark', true);
            $userManager->updateUser($user);
        }

        if(in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse($templateVars);
        }

        list($route, $options) = self::getUserRedirect($user, $this->container);
        if($route != 'home' && $route != 'results')
            return $this->redirect($this->generateUrl($route, $options));

        return $this->render('AdminBundle:Admin:home.html.php', $templateVars);
    }

    public function appLinksAction() {
        return new JsonResponse([
            "applinks"=> [
                "apps"=> [],
                "details"=> [
                    [
                        "appID"=> "3MV67NZ3PZ.com.studysauce.companyapp",
                        "paths"=> [ "*", '/' ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param User|null $user
     * @param ContainerInterface $container
     * @return array|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public static function getUserRedirect($user, ContainerInterface $container)
    {
        if($user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO'))
            return ['_welcome', []];
            // TODO: split this in to separate pages
        elseif($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER'))
            return ['userlist', []];
        elseif(empty($user->getProperty('first_time'))) {
            /** @var EntityManager $orm */
            $orm = $container->get('doctrine')->getManager();
            /** @var Pack|null $intro */
            $intro = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('pack')
                ->select('pack')
                ->where('pack.title LIKE \'%Study Sauce Introduction%\'')
                ->setMaxResults(1)
                ->getQuery()->getOneOrNullResult();

            if(empty($intro) || empty($intro->getCards()->count())) {
                return ['home', []];
            }
            return ['cards', ['card' => $intro->getCards()->first()->getId()]];
        }
        return ['home', []];
    }
}