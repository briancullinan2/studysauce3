<?php

namespace StudySauce\Bundle\Controller;

use Admin\Bundle\Controller\AdminController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Command\CronSauceCommand;
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;


/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class PacksController extends Controller
{
    public function indexAction(Request $request, Pack $pack = null)
    {
        if ($request->get('_route') === 'packs_new') {
            $pack = new Pack();
        }

        return $this->render('AdminBundle:Admin:packs.html.php', ['entity' => $pack]);
    }

    public function cardAction(Card $card)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        if(strpos($card->getPack()->getTitle(), 'Study Sauce Introduction') !== false && empty($user->getProperty('first_time'))) {
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('first_time', true);
            $up = new UserPack();
            $up->setUser($user);
            $user->addUserPack($up);
            $up->setPack($card->getPack());
            $card->getPack()->addUserPack($up);
            $orm->persist($up);
            $userManager->updateUser($user);
        }

        return $this->render('AdminBundle:Admin:cards.html.php', ['card' => $card]);
    }

    public function resultAction(Pack $pack)
    {
        return $this->render('AdminBundle:Admin:result.html.php', ['pack' => $pack]);
    }

    public function groupAction(Group $group)
    {
        return $this->render('AdminBundle:Admin:packs.html.php', ['entity' => $group]);
    }

    public function answerAction(Card $answer)
    {
        return $this->redirect($this->generateUrl('cards', ['card' => $answer->getId()]));
    }

    public function groupsAction(Request $request, Group $group = null)
    {
        if ($request->get('_route') === 'groups_new') {
            $group = new Group();
        }

        return $this->render('AdminBundle:Admin:groups.html.php', ['entity' => $group]);
    }

    public function createAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $searchRequest = unserialize($this->get('cache')->fetch($request->get('requestKey')) ?: 'a:0:{};');

        // process pack settings
        list($newPack) = AdminController::standardSave($request, $this->container);
        if($newPack instanceof Pack) {
            $up = $newPack->getUserPack($this->getUser());
            if(empty($up)) {
                $up = new UserPack();
                $up->setUser($this->getUser());
                $this->getUser()->addUserPack($up);
                $up->setPack($newPack);
                $newPack->addUserPack($up);
                $orm->persist($up);
            }
            if(empty($newPack->getModified())) {
                $newPack->setUser($this->getUser());
            }
            $newPack->setModified(new \DateTime());
            $orm->merge($newPack);
            $orm->flush();
        }

        $allGroups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        // TODO: this is almost a copy of AdminController:groupSave and should be generalized
        // TODO: ? with a fitting child recursion level property on saving, or the UI should just be away of
        // TODO: ? groups at all times and notifies the user how many groups will be removed ?
        $packRequest = $request->get('pack');
        if(empty($packRequest['id'])) {
            $packRequest['id'] = $newPack->getId();
        }

        if(isset($packRequest['groups'])) {
            if(!isset($packRequest['groups'][0])) {
                $packRequest['groups'] = [$packRequest['groups']];
            }
            $subGroups = array_map(function ($g) {return $g['id'];}, $packRequest['groups']);
            $added = true;
            while($added) {
                $added = false;
                foreach($allGroups as $subGroup) {
                    /** @var Group $subGroup */
                    if(!empty($subGroup->getParent())
                        && in_array($subGroup->getParent()->getId(), $subGroups)
                        && !in_array($subGroup->getId(), $subGroups)) {
                        $subGroups[count($subGroups)] = $subGroup->getId();

                        $entity = AdminController::applyFields(AdminController::$allTables['pack']->name, 'pack', ['groups'], array_merge($packRequest, ['groups' => array_merge($packRequest['groups'][0], ['id' => $subGroup->getId()])]), $orm);

                        if(empty($entity->getId())) {
                            $orm->persist($entity);
                        }
                        else {
                            $orm->merge($entity);
                        }
                        $orm->flush();
                        $added = true;
                    }
                }
            }
        }
        // TODO: saving returns the same things you sent to it, so this could go away because the tab with re-render and then refresh
        // TODO: forward to index which only sets up queries needed for page.
        if (!empty($request->get('pack')) && is_array($request->get('pack'))) {
            if (isset($request->get('pack')['id']) && empty($request->get('pack')['id'])) {
                /** @var Pack $newPack */
                $searchRequest['edit'] = false;
                $searchRequest['read-only'] = ['pack'];
                $searchRequest['new'] = false;
                $searchRequest['pack-id'] = $newPack->getId();
                $searchRequest['requestKey'] = null;
            }
        }
        if (!empty($request->get('card')) && empty($searchRequest['pack-id'])) {
            if(!empty($newPack->getId())) {
                $p = $newPack instanceof Card ? $newPack->getPack() : $newPack;
                if($p->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count() > 0) {
                    $searchRequest['new'] = false;
                    $searchRequest['count-card'] = 0;
                }
                else {
                    $searchRequest['new'] = true;
                    $searchRequest['count-card'] = 5;
                }
                $searchRequest['edit'] = false;
                $searchRequest['pack-id'] = $p->getId();
                $searchRequest['requestKey'] = null;
            }
        }

        return $this->forward('AdminBundle:Admin:results', $searchRequest);
    }

    // TODO: use results API instead
    /**
     * @param User|null $user
     * @return JsonResponse
     */
    public function listAction(User $user = null)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        if(!$this->getUser()->hasRole('ROLE_ADMIN') || $user == null) {
            $user = $this->getUser();
        }
        else {
            // select the parent account so the rest of this works right
            /** @var Invite $parents */
            $parents = $user->getInvitees()->filter(function (Invite $i) { return $i->getUser()->hasRole('ROLE_PARENT');})->first();
            if (!empty($parents)) {
                $user = $parents->getUser();
            }
        }

        /** @var QueryBuilder $qb */
        $joins = [];
        $packs = AdminController::firewallCollection('pack', $orm->getRepository('StudySauceBundle:Pack')
            ->createQueryBuilder('pack'), $joins, $user)->getQuery()
            ->getResult();
        $response = new JsonResponse(array_map(function (Pack $x) use ($user) {

            $users = $x->getChildUsers($user);

            if ($x->getStatus() == 'DELETED' || $x->getStatus() == 'UNPUBLISHED' || empty($x->getStatus()) || count($users) == 0 || $x->getProperty('schedule') > new \DateTime()) {
                return [
                    'id' => $x->getId(),
                    'deleted' => true
                ];
            }
            // pack should automatically download if user is in group
            return [
                'id' => $x->getId(),
                'logo' => $x->getLogo(),
                'title' => $x->getTitle(),
                'creator' => $x->getCreator(),
                'properties' => $x->getProperties(),
                'created' => $x->getCreated()->format('r'),
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null,
                'count' => $x->getCards()->filter(function (Card $c) {
                    return !$c->getDeleted();
                })->count(),
                'users' => array_values(array_map(function (User $u) use ($x) {
                    /** @var UserPack $up */
                    $up = $u->getUserPack($x);
                    return [
                        'id' => $u->getId(),
                        'created' => empty($up) || empty($up->getCreated()) ? null : $up->getCreated()->format('r')
                    ];
                }, $users))
            ];
        }, $packs));
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    public function downloadAction(Request $request, User $user = null)
    {
        /** @var User $user */
        $currentUser = $this->getUser();
        if (empty($user) || !$currentUser->getInvites()->exists(function ($_, Invite $x) use ($user) {
                return $x->getInvitee() == $user;
            })
        ) {
            $user = $currentUser;
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var Pack $pack */
        $pack = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->select('p')
            ->where('p.id=:id')
            ->setParameter('id', intval($request->get('pack')))
            ->getQuery()
            ->getOneOrNullResult();
        if (empty($pack)) {
            throw new NotFoundHttpException("Pack not found");
        }
        /** @var QueryBuilder $qb */
        $cards = $orm->getRepository('StudySauceBundle:Card')->createQueryBuilder('c')
            ->select('c,a')
            ->leftJoin('c.answers', 'a')
            ->where('c.pack=:id')
            ->andWhere('c.deleted = 0 OR c.deleted IS NULL')
            ->setParameter('id', intval($request->get('pack')))
            ->getQuery()
            ->getResult();
        /** @var UserPack $up */
        $up = $user->getUserPackById(intval($request->get('pack')));
        $isNew = false;
        if (empty($up)) {
            $up = new UserPack();
            $up->setUser($user);
            $up->setPack($pack);
            $isNew = true;
        }
        $up->setDownloaded(new \DateTime());
        if ($isNew) {
            $orm->persist($up);
        } else {
            $orm->merge($up);
        }
        $orm->flush();
        return new JsonResponse(array_map(function (Card $x) use ($user) {
            return [
                'id' => $x->getId(),
                'content' => $x->getContent(),
                'response' => $x->getResponseContent(),
                'response_type' => $x->getResponseType(),
                'created' => $x->getCreated()->format('r'),
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null,
                'answers' => array_values(array_map(function (Answer $a) {
                    return [
                        'id' => $a->getId(),
                        'value' => $a->getValue(),
                        'content' => $a->getContent(),
                        'correct' => $a->getCorrect(),
                        'created' => $a->getCreated()->format('r'),
                        'modified' => !empty($a->getModified()) ? $a->getModified()->format('r') : null
                    ];
                }, $x->getAnswers()->filter(function (Answer $a) {
                    return !$a->getDeleted();
                })->toArray())),
                'responses' => []
            ];
        }, $cards));
    }

    // TODO: use standard save API

    public function responsesAction(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (empty($user) || (!$currentUser->getInvites()->exists(function ($_, Invite $x) use ($user) {
                return $x->getInvitee() == $user;
            }) && !$currentUser->hasRole('ROLE_ADMIN')))
        {
            $user = $currentUser;
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $responses = $request->get('responses') ?: [];
        if (!empty($request->get('card'))
            && !empty($request->get('correct')) && !empty($request->get('created'))
        ) {
            $responses[] = [
                'card' => $request->get('card'),
                'value' => $request->get('value'),
                'answer' => $request->get('answer'),
                'correct' => $request->get('correct'),
                'created' => $request->get('created'),
                'user' => $currentUser
            ];
        }
        /** @var [UserPack] $userPacks */
        $result = [];
        $changedPacks = [];
        foreach ($responses as $r) {

            /** @var Card $card */
            $card = $orm->getRepository('StudySauceBundle:Card')->createQueryBuilder('c')
                ->select('c')
                ->where('c.id=:id')
                ->setParameter('id', intval($r['card']))
                ->getQuery()
                ->getOneOrNullResult();
            if (empty($card)) {
                $result[] = null;
                continue;
            }

            $response = new Response();
            $response->setValue(isset($r['value']) ? $r['value'] : '');
            $response->setUser($user);
            $user->addResponse($response);
            $response->setCard($card);
            $changedPacks[] = $card->getPack()->getId();
            $card->addResponse($response);
            $response->setCreated(date_timezone_set(new \DateTime($r['created']), new \DateTimeZone(date_default_timezone_get())));
            $response->setCorrect($r['correct'] == '1' || $r['correct'] == 'true');
            if (!empty($r['answer'])
                && !empty($a = $card->getAnswers()->filter(function (Answer $a) use ($r) {
                    return $a->getId() == intval($r['answer']);
                })->first())
            ) {
                $response->setAnswer($a);
            }
            $orm->persist($response);
            $result[] = $response;
        }
        $orm->flush();

        $since = 0;
        if (!empty($request->get('since'))) {
            $since = intval($request->get('since'));
        }
        // only sync responses for specific pack
        $joins = [];
        $packs = AdminController::firewallCollection('pack', $orm->getRepository('StudySauceBundle:Pack')
            ->createQueryBuilder('pack'), $joins, $currentUser)->getQuery()
            ->getResult();
        $packs = array_filter($packs, function (Pack $x) {return !($x->getStatus() == 'DELETED' || $x->getStatus() == 'UNPUBLISHED' || empty($x->getStatus()) || $x->getProperty('schedule') > new \DateTime());});
        if (!empty($request->get('pack'))) {
            $up = $user->getUserPackById($request->get('pack'));
            $refresh = in_array($request->get('pack'), $changedPacks);
            $retention = $up->getRetention($refresh);
            $orm->merge($up);
            $orm->flush();
        }
        else {
            $packs = array_values(array_filter($packs, function (Pack $p) use ($user, $currentUser, $request) {return in_array($user, $p->getChildUsers($currentUser));}));
            $retention = array_values(array_map(function (Pack $p) use ($user, $changedPacks) {
                $forceRefresh = in_array($p->getId(), $changedPacks);
                return ['id' => $p->getId(), 'retention' => $user->getUserPack($p)->getRetention($forceRefresh)];
            }, $packs));
        }

        $ids = array_map(function ($r) {
            /** @var Response $r */
            return empty($r) ? null : $r->getId();
        }, $result);

        return new JsonResponse(['ids' => $ids, 'retention' => $retention]);
    }

}
