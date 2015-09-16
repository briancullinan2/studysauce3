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
    'schedule',
    new Route(
        '/schedule/{_format}',
        ['_controller' => 'StudySauceBundle:Schedule:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'institutions',
    new Route(
        '/institutions',
        ['_controller' => 'StudySauceBundle:Schedule:institutions'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'update_schedule',
    new Route(
        '/schedule/update',
        ['_controller' => 'StudySauceBundle:Schedule:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'goals',
    new Route(
        '/goals/{_format}',
        ['_controller' => 'StudySauceBundle:Goals:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'update_goals',
    new Route(
        '/goals/update',
        ['_controller' => 'StudySauceBundle:Goals:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'claim_goals',
    new Route(
        '/goals/claim',
        ['_controller' => 'StudySauceBundle:Goals:notifyClaim'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'checkin',
    new Route(
        '/checkin/{_format}',
        ['_controller' => 'StudySauceBundle:Checkin:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'checkin_update',
    new Route(
        '/checkin/update',
        ['_controller' => 'StudySauceBundle:Checkin:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'metrics',
    new Route(
        '/metrics/{_format}',
        ['_controller' => 'StudySauceBundle:Metrics:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'deadlines',
    new Route(
        '/deadlines/{_format}',
        ['_controller' => 'StudySauceBundle:Deadlines:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'update_deadlines',
    new Route(
        '/deadlines/update',
        ['_controller' => 'StudySauceBundle:Deadlines:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'remove_deadlines',
    new Route(
        '/deadlines/remove',
        ['_controller' => 'StudySauceBundle:Deadlines:remove'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'partner',
    new Route(
        '/partner/{_format}',
        ['_controller' => 'StudySauceBundle:Partner:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'partner_update',
    new Route(
        '/partner/update',
        ['_controller' => 'StudySauceBundle:Partner:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
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
    'plan',
    new Route(
        '/plan/{_format}',
        ['_controller' => 'StudySauceBundle:Plan:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'plan_download',
    new Route(
        '/plan/download',
        ['_controller' => 'StudySauceBundle:Plan:download']
    )
);
$collection->add(
    'plan_pdf',
    new Route(
        '/plan/pdf/{user}',
        ['_controller' => 'StudySauceBundle:Plan:pdf', 'user' => null],
        ['user' => '[0-9]+']
    )
);
$collection->add(
    'plan_complete',
    new Route(
        '/plan/complete',
        ['_controller' => 'StudySauceBundle:Plan:complete'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'plan_week',
    new Route(
        '/plan/{_week}/{_format}',
        ['_controller' => 'StudySauceBundle:Plan:index', '_format' => 'index', '_week' => ''],
        ['_format' => DASHBOARD_VIEWS, '_week' => '[0-9]+|[0-9]{4}-[0-9]{2}-[0-9]{2}T00:00:00\.000Z']
    )
);
$collection->add(
    'plan_adviser_week',
    new Route(
        'adviser/{_user}/plan/{_week}/{_format}',
        ['_controller' => 'StudySauceBundle:Plan:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS, '_week' => '[0-9]+|[0-9]{4}-[0-9]{2}-[0-9]{2}T00:00:00\.000Z']
    )
);
$collection->add(
    'plan_update',
    new Route(
        '/plan/update',
        ['_controller' => 'StudySauceBundle:Plan:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'plan_strategy',
    new Route(
        '/plan/strategy',
        ['_controller' => 'StudySauceBundle:Plan:updateStrategy'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'plan_create',
    new Route(
        '/plan/create',
        ['_controller' => 'StudySauceBundle:Plan:createStudy'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'profile_update',
    new Route(
        '/profile/update',
        ['_controller' => 'StudySauceBundle:Plan:updateProfile'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'premium',
    new Route(
        '/premium/{_format}',
        ['_controller' => 'StudySauceBundle:Premium:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'tips',
    new Route(
        '/tips/{_format}',
        ['_controller' => 'StudySauceBundle:Tips:index', '_format' => 'index',],
        ['_format' => DASHBOARD_VIEWS,]
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
        'request.isXmlHttpRequest()'
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
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'courses',
    new Route(
        '/courses/{_format}',
        ['_controller' => 'StudySauceBundle:Course:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS,]
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
        'request.isXmlHttpRequest() || request.isMethod("POST")'
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
    'contact_send',
    new Route(
        '/contact/send',
        ['_controller' => 'StudySauceBundle:Dialogs:contactSend'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
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
    'calculator',
    new Route(
        '/calculator/{_format}',
        ['_controller' => 'StudySauceBundle:Calc:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS]
    )
);
$collection->add(
    'calculator_update',
    new Route(
        '/calculator/update',
        ['_controller' => 'StudySauceBundle:Calc:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'notes',
    new Route(
        '/notes/{_format}',
        ['_controller' => 'StudySauceBundle:Notes:index', '_format' => 'index'],
        ['_format' => DASHBOARD_VIEWS,]
    )
);
$collection->add(
    'notes_update',
    new Route(
        '/notes/update',
        ['_controller' => 'StudySauceBundle:Notes:update'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'notes_remove',
    new Route(
        '/notes/remove/{note}',
        ['_controller' => 'StudySauceBundle:Notes:remove'],
        ['note' => '[0-9]+']
    )
);
$collection->add(
    'notes_notebook',
    new Route(
        '/notes/notebook',
        ['_controller' => 'StudySauceBundle:Notes:notebook'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'notes_summary',
    new Route(
        '/notes/summary',
        ['_controller' => 'StudySauceBundle:Notes:noteSummary'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'notes_note',
    new Route(
        '/notes/body/{note}',
        ['_controller' => 'StudySauceBundle:Notes:note'],
        ['note' => '[0-9]+']
    )
);
$collection->add(
    'notes_search',
    new Route(
        '/notes/search',
        ['_controller' => 'StudySauceBundle:Notes:search'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);
$collection->add(
    'notes_thumb',
    new Route(
        '/notes/thumb/{note}',
        ['_controller' => 'StudySauceBundle:Notes:thumb'],
        ['note' => '[0-9]+']
    )
);
/*$collection->add('course', new Route('/course/{_course}/{_format}', array(            '_controller' => 'StudySauceBundle:Courses:Course{_course}:index',            '_format'     => 'dashboard'        )));$collection->add('default', new Route('/{_controller}'));$acmeHello = $loader->import('@StudySauceBundle/Resources/public/images/', 'directory');$acmeHello->addPrefix('/bundles/studysauce/images/');$collection->addCollection($acmeHello);*/
return $collection;