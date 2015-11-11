<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
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
    public function indexAction(Pack $pack = null) {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $total = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')->select('COUNT(DISTINCT p.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $packs = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
        // get the groups for use in dropdown
        $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        return $this->render('StudySauceBundle:Packs:tab.html.php', [
            'packs' => $packs,
            'total' => $total,
            'groups' => $groups,
            'pack' => $pack
        ]);
    }

    public function createAction(Request $request) {
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
        if(empty($newPack)) {
            $newPack = new Pack();
            $newPack->setUser($user);
        }
        $newPack->setTitle($request->get('title'));
        if(empty($newPack->getId())) {
            $orm->persist($newPack);
        }
        else {
            $newPack->setModified(new \DateTime());
            $orm->merge($newPack);
        }

        foreach($request->get('cards') as $c) {
            if (empty($c['content'])) {
                continue;
            }
            $newCard = $newPack->getCards()->filter(function (Card $x) use ($c) {return $c['id'] == $x->getId() && !empty($x->getId());})->first();
            if(empty($newCard)) {
                $newCard = new Card();
                $newCard->setPack($newPack);
                $newPack->addCard($newCard);
            }
            $newCard->setContent($c['content']);
            $newCard->setResponseContent($c['response']);
            if(!empty($c['type'])) {
                $newCard->setResponseType($c['type']);
            }

            if(empty($c['answers'])) {
                $c['answers'] = $c['correct'];
            }
            $answers = explode("\n", $c['answers']);
            $answerValues = [];
            foreach($answers as $a) {
                if(empty(trim($a)))
                    continue;
                $newAnswer = $newCard->getAnswers()->filter(function (Answer $x) use ($a) {return trim($a) == $x->getValue();})->first();
                if(empty($newAnswer)) {
                    $newAnswer = new Answer();
                    $newAnswer->setCard($newCard);
                    $newCard->addAnswer($newAnswer);
                }
                $newAnswer->setContent(trim($a));
                $newAnswer->setResponse(trim($a));
                $newAnswer->setValue(trim($a));
                $answerValues[] = trim($a);
                if(!empty($c['correct'])) {
                    if(strtolower(trim($a)) == strtolower(trim($c['correct']))) {
                        $newAnswer->setCorrect(true);
                    }
                    if($c['correct'] == 'contains') {
                        $newAnswer->setValue('%' . trim($a) . '%');
                    }
                    if($c['correct'] == 'exactly') {
                        $newAnswer->setValue('"' . trim($a) . '"');
                    }
                }
                if(empty($newAnswer->getId())) {
                    $orm->persist($newAnswer);
                }
                else {
                    $orm->merge($newAnswer);
                }
            }

            // remove missing answers
            foreach($newCard->getAnswers()->toArray() as $a) {
                /** @var Answer $a */
                if(!in_array($a->getValue(), $answerValues)) {
                    $a->setDeleted(true);
                }
            }
            if(empty($newCard->getId())) {
                $orm->persist($newCard);
            }
            else {
                $orm->merge($newCard);
            }
        }
        $orm->flush();

        return $this->forward('StudySauceBundle:Packs:index', ['pack' => $newPack->getId(), '_format' => 'tab']);
    }

    public function removeAction(Request $request) {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var Pack $newPack */
        $newPack = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', intval($request->get('id')))
            ->getQuery()
            ->getOneOrNullResult();
        if(!empty($newPack)) {
            foreach($newPack->getCards()->toArray() as $c) {
                /** @var Card $c */
                foreach($c->getAnswers()->toArray() as $a) {
                    $c->removeAnswer($a);
                    $orm->remove($a);
                }
                $c->getPack()->removeCard($c);
                $orm->remove($c);
            }
            $orm->remove($newPack);
        }
        $orm->flush();

        return $this->forward('StudySauceBundle:Packs:index', ['_format' => 'tab']);
    }

    public function listAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var QueryBuilder $qb */
        $packs = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult();
        return new JsonResponse(array_map(function (Pack $x) {
            $group = $x->getGroup();

            $logo = !empty($group)
                ? $group->getLogo()->getUrl()
                : (!empty($x->getUser()) && !empty($x->getUser()->getPhoto())
                    ? $x->getUser()->getPhoto()->getUrl()
                    : '');
            return [
                'id' => $x->getId(),
                'logo' => $logo,
                'title' => $x->getTitle(),
                'creator' => $x->getCreator(),
                'created' => $x->getCreated()->format('r'),
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null,
                'count' => $x->getCards()->count()
            ];
        }, $packs));
    }

    public function downloadAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var Pack $pack */
        $pack = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p')
            ->select('p')
            ->where('p.id=:id')
            ->setParameter('id', intval($request->get('pack')))
            ->getQuery()
            ->getOneOrNullResult();
        if(empty($pack)) {
            throw new NotFoundHttpException("Pack not found");
        }
        /** @var QueryBuilder $qb */
        $cards = $orm->getRepository('StudySauceBundle:Card')->createQueryBuilder('c')
            ->select('c,a')
            ->leftJoin('c.answers', 'a')
            ->where('c.pack=:id')
            ->setParameter('id', intval($request->get('pack')))
            ->getQuery()
            ->getResult();
        /** @var UserPack $up */
        $up = $user->getUserPacks()->filter(function (UserPack $up) use ($request) {return $up->getPack()->getId() == intval($request->get('pack'));})->first();
        $isNew = false;
        if(empty($up)) {
            $up = new UserPack();
            $up->setUser($user);
            $up->setPack($pack);
            $isNew = true;
        }
        $up->setDownloaded(new \DateTime());
        if($isNew) {
            $orm->persist($up);
        }
        else {
            $orm->merge($up);
        }
        $orm->flush();
        return new JsonResponse(array_map(function (Card $x) {
            return [
                'id' => $x->getId(),
                'content' => $x->getContent(),
                'response' => $x->getResponseContent(),
                'response_type' => $x->getResponseType(),
                'created' => $x->getCreated()->format('r'),
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null,
                'answers' => array_map(function (Answer $a) {
                    return [
                        'id' => $a->getId(),
                        'value' => $a->getValue(),
                        'correct' => $a->getCorrect(),
                        'created' => $a->getCreated()->format('r'),
                        'modified' => !empty($a->getModified()) ? $a->getModified()->format('r') : null
                    ];
                }, $x->getAnswers()->toArray())
            ];
        }, $cards));
    }

    public function responsesAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var UserPack $userPack */
        $userPack = $orm->getRepository('StudySauceBundle:UserPack')->createQueryBuilder('up')
            ->select('up')
            ->where('up.pack=:pack')
            ->andWhere('up.user=:user')
            ->setParameter('pack', intval($request->get('pack')))
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();
        if(empty($userPack)) {
            throw new PreconditionFailedHttpException("No user-pack association.");
        }

        /** @var Card $card */
        $card = $userPack->getPack()->getCards()->filter(function (Card $c) use ($request) {return $c->getId() == $request->get('card');})->first();
        if(empty($card)) {
            throw new NotFoundHttpException("Card not found.");
        }
        $response = new Response();
        $response->setUser($user);
        $response->setCard($card);
        $response->setCorrect($request->get('correct') == '1' || $request->get('correct') == 'true');
        if(!empty($request->get('answer')) && !empty($a = $card->getAnswers()->filter(function (Answer $a) use ($request) {
                return $a->getId() == $request->get('answer');})->first())) {
            $response->setAnswer($a);
        }
        $orm->persist($response);
        $orm->flush();

        return new JsonResponse(true);
    }
}
