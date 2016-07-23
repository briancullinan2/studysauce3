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
        ['_controller' => 'StudySauceBundle:Landing:visit']
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
    'home_user',
    new Route(
        '/home/{user}/{_format}',
        ['_controller' => 'StudySauceBundle:Home:index', '_format' => 'index', 'user' => 0],
        ['_format' => DASHBOARD_VIEWS, 'user' => '[0-9]+']
    )
);
$collection->add(
    'app_links',
    new Route(
        '/apple-app-site-association',
        ['_controller' => 'StudySauceBundle:Home:appLinks']
    )
);
$collection->add(
    'app_links2',
    new Route(
        '/.well-known/apple-app-site-association',
        ['_controller' => 'StudySauceBundle:Home:appLinks']
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
        ['_controller' => 'StudySauceBundle:Account:update']
    )
);
$collection->add(
    'remove_social',
    new Route(
        '/remove/social',
        ['_controller' => 'StudySauceBundle:Account:removeSocial']
    )
);
$collection->add(
    'reset',
    new Route(
        '/reset/{_format}',
        ['_controller' => 'StudySauceBundle:Account:reset', '_format' => 'funnel'],
        ['_format' => DASHBOARD_VIEWS]
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
        ['_controller' => 'StudySauceBundle:Account:authenticate']
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
    'error',
    new Route(
        '/error',
        ['_controller' => 'StudySauceBundle:Landing:error', '_format' => 'funnel'],
        ['_format' => 'funnel']
    )
);
$collection->add(
    'error404',
    new Route(
        '/not-found',
        ['_controller' => 'StudySauceBundle:Account:error', '_format' => 'funnel'],
        ['_format' => 'funnel']
    )
);
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
        ['_controller' => 'StudySauceBundle:Account:create']
    )
);
$collection->add(
    'register_child',
    new Route(
        '/register/child/{_format}',
        ['_controller' => 'StudySauceBundle:Account:registerChild', '_format' => 'funnel'],
        ['_format' => 'funnel|tab']
    )
);
$collection->add(
    'file_create',
    new Route(
        '/file/create',
        ['_controller' => 'StudySauceBundle:File:create']
    )
);
$collection->add(
    'file_status',
    new Route(
        '/file/status',
        ['_controller' => 'StudySauceBundle:File:checkStatus']
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
        ['_format' => 'funnel|tab',]
    )
);
$collection->add(
    'checkout_pay',
    new Route(
        '/checkout/pay',
        ['_controller' => 'StudySauceBundle:Buy:pay']
    )
);
$collection->add(
    'signup_save',
    new Route(
        '/signup/save',
        ['_controller' => 'StudySauceBundle:Business:signupSave']
    )
);
$collection->add(
    'checkout_coupon',
    new Route(
        '/checkout/coupon',
        ['_controller' => 'StudySauceBundle:Buy:applyCoupon']
    )
);
$collection->add(
    'cancel_payment',
    new Route(
        '/account/cancel',
        ['_controller' => 'StudySauceBundle:Buy:cancelPayment']
    )
);
$collection->add(
    'signup_beta',
    new Route(
        '/signup-beta',
        ['_controller' => 'StudySauceBundle:Dialogs:signup']
    )
);
$collection->add(
    'contact_send',
    new Route(
        '/contact/send',
        ['_controller' => 'StudySauceBundle:Dialogs:contactSend']
    )
);
$collection->add(
    'contact_parents',
    new Route(
        '/contact/parents',
        ['_controller' => 'StudySauceBundle:Dialogs:billParentsSend']
    )
);
$collection->add(
    'contact_students',
    new Route(
        '/contact/students',
        ['_controller' => 'StudySauceBundle:Dialogs:inviteStudentSend']
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
    'store',
    new Route(
        '/store/{_format}',
        ['_controller' => 'StudySauceBundle:Buy:store', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'store_cart',
    new Route(
        '/cart/{_format}',
        ['_controller' => 'StudySauceBundle:Buy:cart', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'packs_edit',
    new Route(
        '/packs/{pack}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:index', '_format' => 'index', 'pack' => 0],
        ['_format' => DASHBOARD_VIEWS, 'pack' => '[1-9][0-9]*']
    )
);
$collection->add(
    'packs_group',
    new Route(
        '/packs/group/{group}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:group', '_format' => 'index', 'group' => 0],
        ['_format' => DASHBOARD_VIEWS, 'group' => '[1-9][0-9]*']
    )
);
$collection->add(
    'packs_new',
    new Route(
        '/packs/0/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:index', '_format' => 'index', 'pack' => 0],
        ['_format' => DASHBOARD_VIEWS, 'pack' => '0']
    )
);
$collection->add(
    'packs_create',
    new Route(
        '/packs/create',
        ['_controller' => 'StudySauceBundle:Packs:create']
    )
);
$collection->add(
    'packs_intro',
    new Route(
        '/packs/intro',
        ['_controller' => 'StudySauceBundle:Packs:intro']
    )
);
$collection->add(
    'packs_list',
    new Route(
        '/packs/list/{user}',
        ['_controller' => 'StudySauceBundle:Packs:list', 'user' => null],
        ['user' => '[0-9]+']
    )
);
$collection->add(
    'packs_download',
    new Route(
        '/packs/download/{user}',
        ['_controller' => 'StudySauceBundle:Packs:download', 'user' => null],
        ['user' => '[0-9]+']
    )
);
$collection->add(
    'cards',
    new Route(
        '/cards/{card}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:card', '_format' => 'index', 'card' => 0],
        ['_format' => DASHBOARD_VIEWS, 'card' => '[0-9]+']
    )
);
$collection->add(
    'cards_answers',
    new Route(
        '/answers/{answer}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:answer', '_format' => 'index', 'answer' => 0],
        ['_format' => DASHBOARD_VIEWS, 'answer' => '[0-9]+']
    )
);
$collection->add(
    'cards_result',
    new Route(
        '/results/{pack}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:result', '_format' => 'index', 'pack' => 0],
        ['_format' => DASHBOARD_VIEWS, 'pack' => '[0-9]*']
    )
);
$collection->add(
    'responses',
    new Route(
        '/packs/responses/{user}',
        ['_controller' => 'StudySauceBundle:Packs:responses', 'user' => null],
        ['user' => '[0-9]*']
    )
);
$collection->add(
    'groups',
    new Route(
        '/groups/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:groups', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'groups_edit',
    new Route(
        '/groups/{group}/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:groups', '_format' => 'index', 'group' => 0],
        ['_format' => DASHBOARD_VIEWS, 'group' => '[1-9][0-9]*']
    )
);
$collection->add(
    'groups_new',
    new Route(
        '/groups/0/{_format}',
        ['_controller' => 'StudySauceBundle:Packs:groups', '_format' => 'index', 'group' => 0],
        ['_format' => DASHBOARD_VIEWS, 'group' => '0']
    )
);
return $collection;