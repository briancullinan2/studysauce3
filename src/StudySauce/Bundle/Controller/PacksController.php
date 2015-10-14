<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class PacksController extends Controller
{

    public function listAction(Request $request)
    {
        $joins = [];
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Pack')->createQueryBuilder('p');
        $packs = $qb->select('')
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
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null
            ];
        }, $packs));
    }

    public function downloadAction(Request $request)
    {
        $joins = [];
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Card')->createQueryBuilder('c');
        $packs = $qb->select('')
            ->where('c.pack=:id')
            ->setParameter('id', intval($request->get('pack')))
            ->getQuery()
            ->getResult();
        return new JsonResponse(array_map(function (Card $x) {
            return [
                'id' => $x->getId(),
                'content' => $x->getContent(),
                'response' => $x->getResponseContent(),
                'created' => $x->getCreated()->format('r'),
                'modified' => !empty($x->getModified()) ? $x->getModified()->format('r') : null
            ];
        }, $packs));
    }
}
