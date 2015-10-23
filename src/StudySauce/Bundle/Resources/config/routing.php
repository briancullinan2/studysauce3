<?php
if(!defined('DASHBOARD_VIEWS')) {
    define('DASHBOARD_VIEWS', 'index|tab|json|funnel|adviser');
}
// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('_welcome',
    new Route('/', ['_controller' => 'StudySauceBundle:Landing:index'])
);
$collection->add('cronsauce',
    new Route(
        '/cron/{options}',
        ['_controller' => 'StudySauceBundle:Landing:cron', 'options' => null]
    )
);
$collection->add(
    'terms',
    new Route(
        '/terms/{_format}',
        ['_controller' => 'StudySauceBundle:Landing:terms', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'privacy',
    new Route(
        '/privacy/{_format}',
        ['_controller' => 'StudySauceBundle:Landing:privacy', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'contact',
    new Route(
        '/contact/{_format}',
        ['_controller' => 'StudySauceBundle:Landing:contact', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'about',
    new Route(
        '/about/{_format}',
        ['_controller' => 'StudySauceBundle:Landing:about', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'refund',
    new Route(
        '/refund/{_format}',
        ['_controller' => 'StudySauceBundle:Landing:refund', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    '_visit',
    new Route(
        '/_visit',
        ['_controller' => 'StudySauceBundle:Landing:visit',],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'partner_welcome',
    new Route('/partners/{_code}', ['_controller' => 'StudySauceBundle:Landing:partners','_code' => ''])
);
$collection->add(
    'parent_welcome',
    new Route('/parents/{_code}', ['_controller' => 'StudySauceBundle:Landing:parents','_code' => ''])
);
$collection->add(
    'student_welcome',
    new Route('/students/{_code}', ['_controller' => 'StudySauceBundle:Landing:students','_code' => ''])
);
$collection->add(
    'scholar_welcome',
    new Route('/scholars/{_code}', ['_controller' => 'StudySauceBundle:Landing:scholars','_code' => ''])
);
$collection->add(
    'home',
    new Route(
        '/home/{_format}',
        ['_controller' => 'StudySauceBundle:Home:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'hwi_oauth_connect',
    new Route(
        '/connect',
        ['_controller' => 'StudySauceBundle:Connect:connect']
    )
);
$collection->add(
    'hwi_oauth_connect_service',
    new Route(
        '/connect/service/{service}',
        ['_controller' => 'StudySauceBundle:Connect:connectService']
    )
);
$collection->add(
    'hwi_oauth_connect_registration',
    new Route(
        '/connect/registration/{key}',
        ['_controller' => 'HWIOAuthBundle:Connect:registration']
    )
);
$collection->add(
    'account',
    new Route(
        '/account/{_format}',
        ['_controller' => 'StudySauceBundle:Account:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'account_update',
    new Route(
        '/account/update',
        ['_controller' => 'StudySauceBundle:Account:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'remove_social',
    new Route(
        '/remove/social',
        ['_controller' => 'StudySauceBundle:Account:removeSocial'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'password_reset',
    new Route(
        '/reset/{_format}',
        ['_controller' => 'StudySauceBundle:Account:reset', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || !request.isXmlHttpRequest()'
    )
);
$collection->add(
    'login',
    new Route(
        '/login',
        ['_controller' => 'StudySauceBundle:Account:login', '_format' => 'funnel'],
        ['_format' => 'funnel']
    )
);
$collection->add('facebook_login', new Route('/login/facebook/'));
$collection->add('google_login', new Route('/login/google/'));
$collection->add('evernote_login', new Route('/login/evernote/'));
$collection->add('gcal_login', new Route('/login/gcal/'));
$collection->add(
    'account_auth',
    new Route(
        '/authenticate',
        ['_controller' => 'StudySauceBundle:Account:authenticate'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || request.getMethod()=="POST"'
    )
);
$collection->add(
    'register',
    new Route(
        '/register',
        ['_controller' => 'StudySauceBundle:Account:register', '_format' => 'funnel'],
        ['_format' => 'funnel']
    )
);
$collection->add('logout', new Route('/logout'));
$collection->add('demo', new Route('/demo'));
$collection->add('demoadviser',
    new Route(
        '/{page}',
        [],
        ['page' => 'demoadviser|demoadvisor']));
$collection->add(
    'error403',
    new Route(
        '/denied',
        ['_controller' => 'StudySauceBundle:Account:denied', '_format' => 'funnel'],
        ['_format' => 'funnel']
    )
);
$collection->add(
    'account_create',
    new Route(
        '/account/create',
        ['_controller' => 'StudySauceBundle:Account:create'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || request.getMethod()=="POST"'
    )
);
$collection->add(
    'file_create',
    new Route(
        '/file/create',
        ['_controller' => 'StudySauceBundle:File:create'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || request.getMethod()=="POST"'
    )
);
$collection->add(
    'file_status',
    new Route(
        '/file/status',
        ['_controller' => 'StudySauceBundle:File:checkStatus'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'signup',
    new Route(
        '/signup/{_format}',
        ['_controller' => 'StudySauceBundle:Business:signup', '_format' => 'funnel'],
        ['_format' => 'funnel',]
    )
);
$collection->add(
    'checkout',
    new Route(
        '/checkout/{_format}',
        ['_controller' => 'StudySauceBundle:Buy:checkout', '_format' => 'funnel'],
        ['_format' => 'funnel',]
    )
);
$collection->add(
    'thanks',
    new Route(
        '/thanks/{_format}',
        ['_controller' => 'StudySauceBundle:Buy:thanks', '_format' => 'funnel'],
        ['_format' => 'funnel',]
    )
);
$collection->add(
    'checkout_pay',
    new Route(
        '/checkout/pay',
        ['_controller' => 'StudySauceBundle:Buy:pay'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'signup_save',
    new Route(
        '/signup/save',
        ['_controller' => 'StudySauceBundle:Business:signupSave'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'checkout_coupon',
    new Route(
        '/checkout/coupon',
        ['_controller' => 'StudySauceBundle:Buy:applyCoupon'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'cancel_payment',
    new Route(
        '/account/cancel',
        ['_controller' => 'StudySauceBundle:Buy:cancelPayment'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'signup_beta',
    new Route(
        '/signup-beta',
        ['_controller' => 'StudySauceBundle:Dialogs:signup'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || request.getMethod()=="POST"'
    )
);
$collection->add(
    'contact_send',
    new Route(
        '/contact/send',
        ['_controller' => 'StudySauceBundle:Dialogs:contactSend'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest() || request.getMethod()=="POST"'
    )
);
$collection->add(
    'contact_parents',
    new Route(
        '/contact/parents',
        ['_controller' => 'StudySauceBundle:Dialogs:billParentsSend'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'contact_students',
    new Route(
        '/contact/students',
        ['_controller' => 'StudySauceBundle:Dialogs:inviteStudentSend'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'packs',
    new Route(
        '/packs/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'packs_create',
    new Route(
        '/packs/create',
        ['_controller' => 'StudySauceBundle:Packs:create'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'packs_list',
    new Route(
        '/packs/list',
        ['_controller' => 'StudySauceBundle:Packs:list']
    )
);
$collection->add(
    'cards',
    new Route(
        '/packs/download',
        ['_controller' => 'StudySauceBundle:Packs:download']
    )
);
$collection->add(
    'responses',
    new Route(
        '/packs/responses',
        ['_controller' => 'StudySauceBundle:Packs:responses']
    )
);
return $collection;