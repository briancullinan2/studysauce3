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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class BuyController extends Controller
{

    const AUTHORIZENET_API_LOGIN_ID = "698Cy7dL8U";
    const AUTHORIZENET_TRANSACTION_KEY = "6AWm5h4nSu472Z52";
    const AUTHORIZENET_SANDBOX = true;

    public static $defaultOptions = [
        'monthly' => ['price' => 9.99, 'reoccurs' => 1, 'description' => '$9.99/mo'],
        'yearly' => ['price' => 99, 'reoccurs' => 12, 'description' => '$99/year <sup class="premium">Recommended</sup>']
    ];

    public function storeAction() {
        return $this->render('AdminBundle:Admin:store.html.php');
    }

    public function cartAction() {
        return $this->render('AdminBundle:Admin:cart.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkoutAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkout')
            : null;

        if(!$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {
            $first = $user->getFirst();
            $last = $user->getLast();
            $email = $user->getEmail();
            $studentfirst = '';
            $studentlast = '';
            $studentemail = '';
        }
        else
        {
            $first = '';
            $last = '';
            $email = '';
            $studentfirst = '';
            $studentlast = '';
            $studentemail = '';
        }
        /** @var Invite $invite */
        if(!empty($request->getSession()->get('invite')))
        {
            $invite = $orm->getRepository('StudySauceBundle:Invite')->findOneBy(['code' => $request->getSession()->get('group')]);
        }

        /** @var Invite $invite */
        if(!empty($invite))
        {
            $first = $invite->getFirst();
            $last = $invite->getLast();
            $email = $invite->getEmail();
        }

        // TODO: set by invite dialogs when invited anonymously


        // check for coupon
        $coupon = $this->getCoupon($request);
        $option = $request->get('option');

        return $this->render('AdminBundle:Admin:checkout.html.php', [
                'email' => $email,
                'first' => $first,
                'last' => $last,
                'studentemail' => $studentemail,
                'studentfirst' => $studentfirst,
                'studentlast' => $studentlast,
                'coupon' => $coupon,
                'option' => $option,
                'csrf_token' => $csrfToken
            ]);
    }

    /**
     * @param Request $request
     * @return Coupon[]
     */
    private function getCoupon(Request $request)
    {
        if(!empty($request)) {
            $codes = explode(',', $request->get('coupon'));
            if($request->getSession()->has('coupon')) {
                $codes = explode(',', $request->getSession()->get('coupon'));
            }
        }
        if(!empty($codes) && $codes[0] == '') {
            array_splice($codes, 0, 1);
        }
        if(empty($codes))
            return null;

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $allCodes = $orm->getRepository('StudySauceBundle:Coupon')->createQueryBuilder('c');
        $result = [];
        foreach($allCodes as $i => $c) {
            /** @var Coupon $c */
            foreach($codes as $code) {
                if(strtolower(substr($code, 0, strlen($c->getName()))) == strtolower($c->getName())) {
                    // one use coupons should match exactly
                    if($c->getMaxUses() <= 1 && strtolower($code) == strtolower($c->getName()))
                        return $c;

                    // ensure code exists in random value
                    for ($i = 0; $i < $c->getMaxUses(); $i++) {
                        $compareCode = $c->getName() . substr(md5($c->getSeed() . $i), 0, 6);
                        if (strtolower($code) == strtolower($compareCode)) {
                            $result[] = $c;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function applyCouponAction(Request $request)
    {
        $coupon = $this->getCoupon($request);
        if(!empty($coupon)) {
            if(!empty($request->get('remove'))) {
                $request->getSession()->remove('coupon');
            }
            else {
                $request->getSession()->set('coupon', implode(',', array_map(function (Coupon $c) {return $c->getName();}, $coupon)));
            }

            return $this->redirect($this->generateUrl('checkout'));
        }
        throw new NotFoundHttpException('Coupon not found.');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function payAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        $option = $request->get('option');

        // apply coupon if it exists
        $coupon = $this->getCoupon($request);
        $price = 0;

        // create a new payment entity
        $payment = new Payment();
        foreach($coupon as $c) {
            $payment->addCoupon($c);
            foreach($c->getOptions() as $i => $o) {
                $price += $o['price'];
            }
        }
        $payment->setAmount($price);
        $payment->setFirst($request->get('first'));
        $payment->setLast($request->get('last'));
        $payment->setProduct($option);

        try {
            $sale = new \AuthorizeNetAIM(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
            $sale->setField('amount', $price);
            $sale->setField('card_num', $request->get('number'));
            $sale->setField('exp_date', $request->get('month') . '/' . $request->get('year'));
            $sale->setField('first_name', $request->get('first'));
            $sale->setField('last_name', $request->get('last'));
            $sale->setField(
                'address',
                $request->get('street1') .
                (empty(trim($request->get('street2'))) ? '' : ("\n" . $request->get('street2')))
            );
            $sale->setField('city', $request->get('city'));
            $sale->setField('zip', $request->get('zip'));
            $sale->setField('state', $request->get('state'));
            $sale->setField('country', $request->get('country'));
            $sale->setField('card_code', $request->get('ccv'));
            $sale->setField('recurring_billing', true);
            if($this->container->getParameter('authorize_test_mode'))
                $sale->setField('test_request', true);
            else
                $sale->setField('test_request', false);
            $sale->setField('duplicate_window', 120);
            $sale->setSandbox(false);
            $aimResponse = $sale->authorizeAndCapture();
            if ($aimResponse->approved) {
                $payment->setPayment($aimResponse->transaction_id);
            } else {
                $error = $aimResponse->response_reason_text;
            }

        } catch(\AuthorizeNetException $ex) {
            throw new BadRequestHttpException($ex->getMessage(), $ex);
        }

        $user = $this->getUser();
        $payment->setUser($user);
        $user->addPayment($payment);
        $payment->setEmail($user->getEmail());

        if (isset($error)) {
            $orm->persist($payment);
            $orm->flush();
            throw new BadRequestHttpException($error);
        }

        // successful payment!

        // find or create user from checkout form
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->findAndCreateUser($request);
        $payment->setUser($user);
        $user->addPayment($payment);
        $payment->setEmail($user->getEmail());

        // update paid status
        $user->addRole('ROLE_PAID');
        // set group for coupon is necessary
        // TODO: set pack for selected user
        if(!empty($coupon) && !empty($coupon->getGroup()) && !$user->hasGroup($coupon->getGroup()->getName())) {
            $user->addGroup($coupon->getGroup());
        }
        $userManager->updateUser($user);

        // redirect parents and partners to thank you page
        if($user->hasRole('ROLE_PARENT') || $user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER')) {
            $response = $this->redirect($this->generateUrl('thanks', ['_format' => 'funnel']));
        }
        // redirect to user area
        else {
            list($route, $options) = HomeController::getUserRedirect($user);
            $response = $this->redirect($this->generateUrl($route, $options));
        }

        // send receipt
        $address = $request->get('street1') .
            (empty(trim($request->get('street2'))) ? '' : ("<br />" . $request->get('street2'))) . '<br />' .
            $request->get('city') . ' ' . $request->get('state') . '<br />' .
            $request->get('zip');

        $emails = new EmailsController();
        $emails->setContainer($this->container);
        $emails->invoiceAction($user, $payment, $address);

        $loginManager = $this->get('fos_user.security.login_manager');
        $loginManager->loginUser('main', $user, $response);
        return $response;
    }

    /**
     * @param Request $request
     * @return \StudySauce\Bundle\Entity\User
     */
    private function findAndCreateUser(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();

        // create a mock invite
        if(!empty($request->get('invite')) && !empty($request->get('invite')['first']) &&
            !empty($request->get('invite')['last']) && !empty($request->get('invite')['email']))
        {
            /** @var User $inviteUser */
            $inviteUser = $userManager->findUserByEmail($request->get('invite')['email']);
            /** @var Invite $invite */
            $invite = new Invite();
            $invite->setUser($user); // might be guest here
            $invite->setFirst($request->get('invite')['first']);
            $invite->setLast($request->get('invite')['last']);
            $invite->setEmail($request->get('invite')['email']);
            $invite->setCode(md5(microtime()));
            if(!empty($inviteUser))
                $invite->setInvitee($inviteUser);
            $orm->persist($invite);
            $orm->flush();
        }

        // create a user from checkout only if we are currently logged in as guests
        if($user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) {
            // look up existing user by email address
            /** @var User $user */
            $user = $userManager->findUserByEmail($request->get('email'));

            // create a user if anonymous
            if(empty($user)) {
                $account = new AccountController();
                $account->setContainer($this->container);
                // don't send welcome email if we are inviting a student
                $account->createAction($request, true, !isset($invite));
                $user = $userManager->findUserByEmail($request->get('email'));
            }
            // change invite owner to the actual user
            if(isset($invite)) {
                $invite->setUser($user);
                $user->addInvite($invite);
                $orm->merge($invite);
            }
            $orm->flush();

            // set the context for this load, and log in after transaction is complete
            $context = $this->get('security.context');
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            $context->setToken($token);
            $session = $request->getSession();
            $session->set('_security_main', serialize($token));
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function thanksAction(Request $request)
    {
        if(!empty($request->getSession()->get('signup'))) {
            return $this->render('StudySauceBundle:Business:thanks.html.php');
        }
        return $this->render('StudySauceBundle:Buy:thanks.html.php');
    }

    /**
     * @param User $user
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cancelPaymentAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user) || !$this->getUser()->hasRole('ROLE_ADMIN'))
            $user = $this->getUser();

        $payments = $user->getPayments()->toArray();
        foreach($payments as $i => $p)
        {
            /** @var Payment $p */
            if(empty($p->getSubscription()))
                continue;
            /** @var Payment $p */
            try {
                $arbRequest = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
                $arbRequest->setSandbox(false);
                $arbResponse = $arbRequest->cancelSubscription($p->getSubscription());
                if ($arbResponse->isOk()) {

                }
                else {
                    throw new \Exception($arbResponse->getMessageText());
                }
            }
            catch (\Exception $ex){
                $this->get('logger')->error('Authorize.Net cancel failed: ' . $p->getSubscription());
                throw $ex;
            }
        }

        $user->removeRole('ROLE_PAID');
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }

}