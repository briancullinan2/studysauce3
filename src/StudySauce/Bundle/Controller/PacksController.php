<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
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


/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class PacksController extends Controller
{
    public function indexAction(Pack $pack = null)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $total = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')->select('COUNT(DISTINCT p.id)')
            ->andWhere('p.status != \'DELETED\'')
            ->getQuery()
            ->getSingleScalarResult();
        $packs = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->andWhere('p.status != \'DELETED\'')
            ->getQuery()
            ->getResult();

        // get the groups for use in dropdown
        /** @var User $user */
        $user = $this->getUser();
        if ($user->hasRole('ROLE_ADMIN')) {
            $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();
        } else {
            $groups = $user->getGroups()->toArray();
        }

        return $this->render('StudySauceBundle:Packs:tab.html.php', [
            'packs' => $packs,
            'total' => $total,
            'groups' => $groups,
            'pack' => $pack
        ]);
    }

    public function createAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Pack $newPack */
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
            $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();
        } else {
            $groups = $user->getGroups()->toArray();
        }
        $groups = array_filter($groups, function (Group $g) use ($request) {
            return $g->getId() == intval($request->get('group'));
        });
        $newPack->setTitle($request->get('title'));
        $group = array_pop($groups);
        $newPack->setGroup(!empty($group) ? $group : null);
        $newPack->setStatus($request->get('status'));
        if (empty($newPack->getId())) {
            $orm->persist($newPack);
        } else {
            $newPack->setModified(new \DateTime());
            $orm->merge($newPack);
        }

        foreach ($request->get('cards') as $c) {
            /** @var Card $newCard */
            $newCard = $newPack->getCards()->filter(function (Card $x) use ($c) {
                return $c['id'] == $x->getId() && !empty($x->getId());
            })->first();
            if (empty($newCard)) {
                $newCard = new Card();
                $newCard->setPack($newPack);
                $newPack->addCard($newCard);
            } // remove cards
            elseif (!empty($c['remove']) && $c['remove'] == 'true') {
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

            if (empty($c['content'])) {
                continue;
            }
            $newCard->setContent($c['content']);
            $newCard->setResponseContent($c['response']);
            if (!empty($c['type'])) {
                $newCard->setResponseType($c['type']);
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
                    return trim(trim($a), '$^') == $x->getValue();
                })->first();
                if (empty($newAnswer)) {
                    $newAnswer = new Answer();
                    $newAnswer->setCard($newCard);
                    $newCard->addAnswer($newAnswer);
                }
                $answerValues[] = $newAnswer;
                $newAnswer->setContent(trim($a));
                $newAnswer->setResponse(trim($a));
                $newAnswer->setValue(trim($a));
                if (!empty($c['correct'])) {
                    if (strtolower(trim($a)) == strtolower(trim($c['correct']))) {
                        $newAnswer->setCorrect(true);
                    }
                    if ($c['correct'] == 'contains') {
                        $newAnswer->setCorrect(true);
                    }
                    if ($c['correct'] == 'exactly') {
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

        return $this->forward('AdminBundle:Admin:results', ['tables' => ['pack', 'card'], 'search' => 'pack.id:' . $newPack->getId()]);
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

        return $this->forward('AdminBundle:Admin:results', ['tables' => ['pack', 'card']]);
    }

    /**
     * @return Pack[]
     */
    private function getPacksForUser() {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return array_values(array_filter($orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult(), function (Pack $p) use ($currentUser) {
            /** @var UserPack $up */
            $hasPack = $p->getUser() == $currentUser
                || $currentUser->getUserPacks()
                    ->filter(function (UserPack $up) use ($p) {
                        return $up->getPack()->getId() == $p->getId();
                    })
                    ->count() > 0
                || $currentUser->getInvites()->exists(function ($_, Invite $x) use ($p) {
                    return !empty($x->getInvitee())
                    && $x->getInvitee()->getUserPacks()->filter(function (UserPack $up) use ($p) {
                        return $up->getPack()->getId() == $p->getId();
                    })
                        ->count() > 0;
                });
            $packGroups = $p->getGroups()->map(function (Group $g) {
                return $g->getId();
            })->toArray();
            $hasGroups = count(array_intersect($packGroups, $currentUser->getGroups()
                    ->map(function (Group $g) {
                        return $g->getId();
                    })->toArray())) > 0
                || $currentUser->getInvites()->exists(function ($_, Invite $x) use ($packGroups) {
                    return !empty($x->getInvitee())
                    && count(array_intersect($packGroups, $x->getInvitee()->getGroups()
                        ->map(function (Group $g) {
                            return $g->getId();
                        })->toArray())) > 0;
                });
            if (($p->getStatus() == 'DELETED' || $p->getStatus() == 'UNPUBLISHED') && $hasPack) {
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

    public function listAction(User $user = null)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        /** @var QueryBuilder $qb */
        $packs = self::getPacksForUser();
        $response = new JsonResponse(array_map(function (Pack $x) use ($currentUser) {

            if ($x->getStatus() == 'DELETED' || $x->getStatus() == 'UNPUBLISHED') {
                return [
                    'id' => $x->getId(),
                    'deleted' => true
                ];
            }
            $packGroups = $x->getGroups()->map(function (Group $g) {
                return $g->getId();
            })->toArray();
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
                    return [
                        'id' => $u->getId()
                    ];
                }, array_filter(array_merge([$currentUser], $currentUser->getInvites()
                    ->filter(function (Invite $i) {
                        return !empty($i->getInvitee());
                    })
                    ->map(function (Invite $i) {
                        return $i->getInvitee();
                    })->toArray()),
                    function (User $u) use ($x, $packGroups) {
                        return $x->getUser() == $u || $u->getUserPacks()
                            ->filter(function (UserPack $up) use ($x) {
                                return $up->getPack()->getId() == $x->getId();
                            })->count() > 0
                        || count(array_intersect($packGroups, $u->getGroups()
                            ->map(function (Group $g) {
                                return $g->getId();
                            })->toArray())) > 0;
                    })))
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
                'responses' => array_values(array_map(function (Response $r) {
                    return [
                        'id' => $r->getId(),
                        'card' => $r->getCard()->getId(),
                        'answer' => empty($r->getAnswer()) ? 0 : $r->getAnswer()->getId(),
                        'correct' => $r->getCorrect() ? 1 : 0,
                        'value' => $r->getValue(),
                        'created' => $r->getCreated()->format('r'),
                        'user' => $r->getUser()->getId()
                    ];
                }, $x->getResponses()->filter(function (Response $r) use ($user) {
                    return $r->getUser() == $user && $r->getCreated() <= new \DateTime();
                })->toArray()))
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
        $packs = array_filter($user->getPacks()->toArray(), function (Pack $p) {return !$p->getDeleted();});

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
            return $r->getUser() == $user && !$r->getCard()->getDeleted() && in_array($r->getCard()->getPack(), $packs)
                && $r->getCreated() <= new \DateTime() && $r->getId() > $since;
        })->toArray()));

        $ids = array_map(function ($r) {
            /** @var Response $r */
            return empty($r) ? null : $r->getId();
        }, $result);

        return new JsonResponse(['ids' => $ids, 'responses' => $responses]);
    }
}
