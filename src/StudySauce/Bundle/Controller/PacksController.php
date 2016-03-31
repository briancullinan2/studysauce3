<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
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
    public function indexAction(Pack $pack = null)
    {
        if ($pack === 0) {
            $pack = new Pack();
        }

        return $this->render('StudySauceBundle:Packs:tab.html.php', [
            'entity' => $pack
        ]);
    }

    public function groupsAction(Group $group = null)
    {
        if ($group === 0) {
            $group = new Group();
        }

        return $this->render('StudySauceBundle:Packs:groups.html.php', [
            'entity' => $group
        ]);
    }

    public function sendNotification($message, $count, $deviceToken) {
        try {
            $body['aps'] = array(
                'alert' => $message,
                'badge' => $count
            );

            //$body['category'] = 'message';
            //$body['category'] = 'profile';
            //$body['category'] = 'dates';
            //$body['category'] = 'daily_dates';
            //$body['sender'] = 'jamesHAW';
            $body['sender'] = 'web.StudySauce';

            //Server stuff
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__ . '/' . 'com.studysauce.companyapp.pem');
            $fp = stream_socket_client(
                'ssl://gateway' . ($this->get('kernel')->getEnvironment() == 'prod' ? '' : '.sandbox') . '.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp)
                throw new Exception("Failed to connect: $err $errstr" . PHP_EOL);
            $this->get('logger')->debug('Connected to APNS' . PHP_EOL);
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            if (!$result)
                throw new Exception('Message not delivered' . PHP_EOL);
            else
                $this->get('logger')->debug('Message successfully delivered' . PHP_EOL);
            fclose($fp);
        }
        catch (Exception $e) {
            $this->get('logger')->debug($e);
        }

    }

    public function createAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Pack $newPack */
        // process pack settings
        $newPack = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', intval($request->get('id')))
            ->getQuery()
            ->getOneOrNullResult();
        if (empty($newPack)) {
            $newPack = new Pack();
            $newPack->setUser($user);
        }
        if ($user->hasRole('ROLE_ADMIN')) {
            if(!empty($request->get('logo'))) {
                $newPack->setProperty('logo', $request->get('logo'));
            }
            $groups = new ArrayCollection($orm->getRepository('StudySauceBundle:Group')->findAll());
        } else {
            /** @var File $logo */
            if(!empty($request->get('logo'))) {
                $logo = $user->getFiles()->filter(function (File $f) use ($request) {
                    return $f->getUrl() == $request->get('logo');
                })->first();
                $newPack->setProperty('logo', !empty($logo) ? $logo->getUrl() : null);
            }
            $groups = $user->getGroups();
        }
        if(!empty($request->get('keyboard'))) {
            $newPack->setProperty('keyboard', $request->get('keyboard'));
        }
        if(!empty($request->get('title'))) {
            $newPack->setTitle($request->get('title'));
        }
        if (!empty($publish = $request->get('publish'))) {
            $newPack->setProperty('schedule', new \DateTime($publish['schedule']));
            $newPack->setProperty('email', $publish['email']);
            $newPack->setProperty('alert', $publish['alert']);
        }
        foreach ($request->get('groups') ?: [] as $group) {
            /** @var Group $g */
            if (!empty($g = $groups->filter(function (Group $g) use ($group) {
                    return $group['id'] == $g->getId();})->first()) && !$newPack->hasGroup($g->getName()) && (!isset($group['remove']) || $group['remove'] != 'true')) {
                $newPack->addGroup($g);
            }
            else if (!empty($g) && $newPack->hasGroup($g->getName()) && isset($group['remove']) && $group['remove'] == 'true') {
                $newPack->removeGroup($g);
            }
        }
        // TODO: secure user access using ACLs, which admins have access to which users?
        foreach ($request->get('users') ?: [] as $u) {
            /** @var UserPack $up */
            if (!empty($up = $newPack->getUserPacks()->filter(function (UserPack $up) use ($u) {return $up->getUser()->getId() == $u['id'];})->first()) && isset($group['remove']) && $u['remove'] == 'true') {
                $up->setRemoved(true);
            }
            else if (empty($up) && (!isset($u['remove']) || $u['remove'] != 'true')) {
                $up = new UserPack();
                /** @var User $upUser */
                $upUser = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $u['id']]);
                $up->setUser($upUser);
                $upUser->addUserPack($up);
                $up->setPack($newPack);
                $newPack->addUserPack($up);
                $orm->persist($up);
            }
        }
        if(!empty($request->get('status'))) {
            $newPack->setStatus($request->get('status'));
        }
        if (empty($newPack->getId())) {
            $orm->persist($newPack);
        } else {
            $newPack->setModified(new \DateTime());
            $orm->merge($newPack);
        }
        $orm->flush();

        // process cards
        // TODO: break this up
        foreach ($request->get('cards') ?: [] as $c) {
            /** @var Card $newCard */
            $newCard = $newPack->getCards()->filter(function (Card $x) use ($c) {
                return $c['id'] == $x->getId() && !empty($x->getId());
            })->first();
            // remove cards
            if (!empty($c['remove']) && $c['remove']) {
                if (empty($newCard)) {
                    continue;
                }
                if ($newCard->getResponses()->count() == 0) {
                    foreach ($newCard->getAnswers()->toArray() as $a) {
                        /** @var Answer $a */
                        $orm->remove($a);
                        $newCard->removeAnswer($a);
                    }
                    $orm->remove($newCard);
                    $newPack->removeCard($newCard);
                } else {
                    $newCard->setDeleted(true);
                    $orm->merge($newCard);
                }
                continue;
            }
            else if (empty($newCard)) {
                $newCard = new Card();
                $newCard->setPack($newPack);
                $newPack->addCard($newCard);
            }
            $newCard->setContent($c['content']);
            if (!empty($c['type'])) {
                if ($c['type'] == 'sa exactly' || $c['type'] == 'sa contains') {
                    $newCard->setResponseType('sa');
                }
                else {
                    $newCard->setResponseType($c['type']);
                }
            }
            if (empty($newCard->getId())) {
                $orm->persist($newCard);
            } else {
                $orm->merge($newCard);
            }

            if (!isset($c['answers'])) {
                $c['answers'] = $c['correct'];
            }
            $answers = explode("\n", $c['answers']);
            $answerValues = [];
            foreach ($answers as $a) {
                if (trim($a) == '')
                    continue;
                $newAnswer = $newCard->getAnswers()->filter(function (Answer $x) use ($a) {
                    return trim(trim($a), '$^') == trim(trim($x->getValue()), '$^');
                })->first();
                if (empty($newAnswer)) {
                    $newAnswer = new Answer();
                    $newAnswer->setCard($newCard);
                    $newCard->addAnswer($newAnswer);
                }
                $answerValues[] = $newAnswer;
                $newAnswer->setContent(str_replace('|', ' or ', trim($a)));
                $newAnswer->setResponse(trim($a));
                $newAnswer->setValue(trim($a));
                if (!empty($c['correct'])) {
                    if (strtolower(trim($a)) == strtolower(trim($c['correct']))) {
                        $newAnswer->setCorrect(true);
                    }
                    if (strpos($c['type'], 'contains') > -1) {
                        $newAnswer->setCorrect(true);
                    }
                    if (strpos($c['type'], 'exactly') > -1) {
                        $newAnswer->setCorrect(true);
                        $newAnswer->setValue('^' . trim($a) . '$');
                    }
                }
                if (empty($newAnswer->getId())) {
                    $orm->persist($newAnswer);
                } else {
                    $orm->merge($newAnswer);
                }
            }

            // remove missing answers
            foreach ($newCard->getAnswers()->toArray() as $a) {
                /** @var Answer $a */
                if (!in_array($a->getValue(), array_map(function (Answer $x) {
                    return $x->getValue();
                }, $answerValues))
                ) {
                    if ($a->getResponses()->count() == 0) {
                        $newCard->removeAnswer($a);
                        $orm->remove($a);
                    } else {
                        $a->setDeleted(true);
                        $orm->merge($a);
                    }
                }
            }
        }
        $orm->flush();
        return $this->forward('AdminBundle:Admin:results', ['tables' => ['pack', 'card'], 'pack-id' => $newPack->getId(), 'headers' => false, 'edit' => true, 'expandable' => ['card' => ['preview']]]);
    }

    public function removeAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var Pack $newPack */
        $newPack = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', intval($request->get('id')))
            ->getQuery()
            ->getOneOrNullResult();
        if (!empty($newPack)) {
            // TODO: set deleted flag if there are existing responses, we don't delete here
            foreach ($newPack->getUserPacks()->toArray() as $up) {
                /** @var UserPack $up */
                $newPack->removeUserPack($up);
                $up->getUser()->removeUserPack($up);
                $orm->remove($up);
            }
            foreach ($newPack->getCards()->toArray() as $c) {
                /** @var Card $c */
                foreach ($c->getAnswers()->toArray() as $a) {
                    $c->removeAnswer($a);
                    $orm->remove($a);
                }
                foreach ($c->getResponses()->toArray() as $r) {
                    /** @var Response $r */
                    $c->removeResponse($r);
                    $r->getUser()->removeResponse($r);
                    $orm->remove($r);
                }
                $c->getPack()->removeCard($c);
                $orm->remove($c);
            }
            $orm->remove($newPack);
        }
        $orm->flush();

        return $this->forward('AdminBundle:Admin:results', ['tables' => ['pack', 'card'], 'card-id' => 0, 'expandable' => ['card' => ['preview']]]);
    }

    /**
     * @param null $user
     * @return \StudySauce\Bundle\Entity\Pack[]
     */
    public function getPacksForUser($user = null) {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        if ($user == null) {
            $user = $this->getUser();
        }

        return array_values(array_filter($orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult(), function (Pack $p) use ($user) {
            /** @var UserPack $up */
            $hasPack = $p->getUser() == $user
                || $user->getUserPacks()
                    ->filter(function (UserPack $up) use ($p) {
                        return $up->getPack()->getId() == $p->getId();
                    })
                    ->count() > 0
                || $user->getInvites()->exists(function ($_, Invite $x) use ($p) {
                    return !empty($x->getInvitee())
                    && $x->getInvitee()->getUserPacks()->filter(function (UserPack $up) use ($p) {
                        return $up->getPack()->getId() == $p->getId();
                    })
                        ->count() > 0;
                });
            $packGroups = $p->getGroups()->map(function (Group $g) {
                return $g->getId();
            })->toArray();
            $hasGroups = count(array_intersect($packGroups, $user->getGroups()
                    ->map(function (Group $g) {
                        return $g->getId();
                    })->toArray())) > 0
                || $user->getInvites()->exists(function ($_, Invite $x) use ($packGroups) {
                    return !empty($x->getInvitee())
                    && count(array_intersect($packGroups, $x->getInvitee()->getGroups()
                        ->map(function (Group $g) {
                            return $g->getId();
                        })->toArray())) > 0;
                });
            if (($p->getStatus() == 'DELETED' || $p->getStatus() == 'UNPUBLISHED' || empty($p->getStatus()))) {
                if ($hasPack) {
                    return true;
                }
                return false;
            }
            if ($p->getStatus() == 'UNLISTED' && $hasPack) {
                return true;
            }
            if ($p->getStatus() == 'GROUP' && $hasGroups) {
                // || $user->getInvitees()->exists(function ($_, Invite $i) use ($p)
                //    return !empty($i->getUser()) && $i->getUser()->hasGroup($p->getGroup()->getName());
                return true;
            }
            if ($p->getStatus() == 'PUBLIC') {
                return true;
            }
            return false;
        }));
    }

    public function getChildUsersForPack(Pack $x, User $user) {
        $packGroups = $x->getGroups()->map(function (Group $g) {
            return $g->getId();
        })->toArray();

        return array_filter(
            // also return current user and children
            array_merge([$user], $user->getInvites()
            ->filter(function (Invite $i) {
                return !empty($i->getInvitee());
            })
            ->map(function (Invite $i) {
                return $i->getInvitee();
            })->toArray()),
            function (User $u) use ($x, $packGroups) {
                return ($x->getUser() == $u && !$x->getStatus() == 'UNLISTED')
                || $u->getUserPacks()
                    ->filter(function (UserPack $up) use ($x) {
                        return !$up->getRemoved() && $up->getPack()->getId() == $x->getId();
                    })->count() > 0
                || count(array_intersect($packGroups, $u->getGroups()
                    ->map(function (Group $g) {
                        return $g->getId();
                    })->toArray())) > 0;
            });
    }

    /**
     * @param User|null $user
     * @return JsonResponse
     */
    public function listAction(User $user = null)
    {

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
        $packs = self::getPacksForUser($user);
        $response = new JsonResponse(array_map(function (Pack $x) use ($user) {

            $users = self::getChildUsersForPack($x, $user);

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
                    $up = $u->getUserPacks()->filter(function (UserPack $up) use ($x) {return $up->getPack() == $x;})->first();
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
        $up = $user->getUserPacks()->filter(function (UserPack $up) use ($request) {
            return $up->getPack()->getId() == intval($request->get('pack'));
        })->first();
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

    public function responsesAction(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (empty($user) || !$currentUser->getInvites()->exists(function ($_, Invite $x) use ($user) {
                return $x->getInvitee() == $user;
            })
        ) {
            $user = $currentUser;
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $responses = $request->get('responses') ?: [];
        if (!empty($request->get('pack')) && !empty($request->get('card')) && !empty($request->get('answer'))
            && !empty($request->get('correct')) && !empty($request->get('created'))
        ) {
            $responses[] = [
                'pack' => $request->get('pack'),
                'card' => $request->get('card'),
                'answer' => $request->get('answer'),
                'correct' => $request->get('correct'),
                'created' => $request->get('created'),
                'user' => $currentUser
            ];
        }
        /** @var [UserPack] $userPacks */
        $result = [];
        foreach ($responses as $r) {

            /** @var Pack $pack */
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
            $response->setUser($user);
            $response->setCard($card);
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
        if (!empty($request->get('pack'))) {
            $packs = $user->getPacks()->filter(function (Pack $p) use ($request) {return !$p->getDeleted() && $p->getId() == intval($request->get('pack'));});
        }
        else {
            $packs = $user->getPacks()->filter(function (Pack $p) {return !$p->getDeleted();});
        }

        $responses = array_values(array_map(function (Response $r) {
            return [
                'id' => $r->getId(),
                'card' => $r->getCard()->getId(),
                'answer' => empty($r->getAnswer()) ? 0 : $r->getAnswer()->getId(),
                'correct' => $r->getCorrect() ? 1 : 0,
                'value' => $r->getValue(),
                'created' => $r->getCreated()->format('r'),
                'user' => $r->getUser()->getId()
            ];
        }, $user->getResponses()->filter(function (Response $r) use ($user, $since, $packs) {
            return $r->getUser() == $user && !$r->getCard()->getDeleted() && $packs->contains($r->getCard()->getPack())
                && $r->getCreated() <= new \DateTime() && $r->getId() > $since;
        })->toArray()));

        $ids = array_map(function ($r) {
            /** @var Response $r */
            return empty($r) ? null : $r->getId();
        }, $result);

        return new JsonResponse(['ids' => $ids, 'responses' => $responses, 'retention' => self::getRetention($packs->first(), $user)]);
    }

    /**
     * @param Pack $pack
     * @param User $user
     * @return mixed
     */
    function getRetention(Pack $pack, User $user) {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $intervals = [1, 2, 4, 7, 14, 28, 28 * 3, 28 * 6, 7 * 52];
        // if a card hasn't been answered, return the next card
        $cards = $pack->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->toArray();
        $result = [];
        foreach($cards as $c) {
            /** @var Card $c */
            $responses = $orm->getRepository('StudySauceBundle:Response')->findBy(['card' => $c, 'user' => $user], ['created' => 'ASC']);
            /** @var \DateTime $last */
            $last = null;
            $i = 0;
            foreach($responses as $r) {
                /** @var Response $r */
                if ($r->getCorrect()) {
                    // If it is in between time intervals ignore the response
                    while ($i < count($intervals) && ($last == null || date_time_set(clone $r->getCreated(), 3, 0, 0) >= date_time_set(date_add(clone $last, new \DateInterval('P' . $intervals[$i] . 'D')), 3, 0, 0))) {
                        // shift the time interval if answers correctly in the right time frame
                        $last = $r->getCreated();
                        $i += 1;
                    }
                }
                else {
                    $i = 0;
                    $last = $r->getCreated();
                }
            }
            if ($i < 0) {
                $i = 0;
            }
            if ($i > count($intervals) - 1) {
                $i = count($intervals) - 1;
            }
            if (!empty($last)) {
                $result[$c->getId()] = [$intervals[$i], $last->format('r')];
            }
        }
        return $result;
    }
}
