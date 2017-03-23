<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\ContactMessage;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessController
 * @package StudySauce\Bundle\Controller
 */
class BusinessController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction()
    {
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('business')
            : null;

        /** @var User $user */
        $user = $this->getUser();
        $first = $user->getFirst();
        $last = $user->getLast();
        $email = $user->getEmail();

        return $this->render('StudySauceBundle:Business:signup.html.php', [
            'students' => 0,
            'email' => $email,
            'first' => $first,
            'last' => $last,
            'organization' => '',
            'title' => '',
            'phone' => '',
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signupSaveAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        // save the invite
        $fields = [
            'Organization' => $request->get('organization'),
            'Street' => $request->get('street1') . (!empty($request->get('street2')) ? ("<br />\n" . $request->get('street2')) : ''),
            'City' => $request->get('city'),
            'Zip' => $request->get('zip'),
            'State' => $request->get('state'),
            'Country' => $request->get('country'),
            'First' => $request->get('first'),
            'Title' => $request->get('title'),
            'Email' => $request->get('email'),
            'Phone' => $request->get('phone'),
            'Students' => $request->get('students'),
            'Payment' => $request->get('payment')
        ];
        $body = '';
        foreach($fields as $i => $x)
        {
            $body .= '<strong>' . $i . ':</strong> ' . $x . "<br >\n";
        }

        $email = new EmailsController();
        $email->setContainer($this->container);
        $email->contactMessageAction($user, $fields['First'], $fields['Email'], $body);

        $request->getSession()->set('signup', true);

        return $this->redirect($this->generateUrl('thanks', ['_format' => 'funnel']));

    }
}