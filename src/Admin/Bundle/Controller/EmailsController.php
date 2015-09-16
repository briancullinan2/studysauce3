<?php

namespace Admin\Bundle\Controller;

use Admin\Bundle\Helpers\CustomPhpEngine;
use Admin\Bundle\Helpers\StringLoader;
use Codeception\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * Class ValidationController
 * @package StudySauce\Bundle\Controller
 */
class EmailsController extends \StudySauce\Bundle\Controller\EmailsController
{
    private static $emailsDir;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        self::$emailsDir = $this->container->getParameter('kernel.root_dir') . '/../src/StudySauce/Bundle/Resources/views/Emails/';
    }

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param $joins
     * @return QueryBuilder
     */
    static function searchBuilder(EntityManager $orm, Request $request, &$joins = [])
    {
        $joins = [];
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m');

        if(!empty($search = $request->get('search'))) {
            if(strpos($search, '%') === false) {
                $search = '%' . str_replace(['@', '_', 'mailinator.com'], ['%', '%', ''], $search) . '%';
            }
            $qb = $qb->andWhere('m.message LIKE :search')
                ->setParameter('search', $search);
        }

        return $qb;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }


        $yesterday = new \DateTime('yesterday');
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:Mail')->createQueryBuilder('m');
        $recent = $qb->select('COUNT(DISTINCT m.id)')
            ->andWhere('m.created > :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();

        self::$tables = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        $entities = [];
        foreach(self::$tables as $t)
        {
            $namespace = explode('\\', $t);
            $className = end($namespace);
            $data = $orm->getMetadataFactory()->getMetadataFor($t);
            foreach($data->getFieldNames() as $f) {
                $entities[] = [
                    'value' => strtolower($className) . ucfirst($f),
                    'text' => $className,
                    '0' => ':' . ucfirst($f)
                ];
            }
        }

        // count total so we know the max pages
        $total = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT m.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // max pagination to search count
        if(!empty($page = $request->get('page'))) {
            if($page == 'last') {
                $page = $total / 25;
            }
            $resultOffset = (min(max(1, ceil($total / 25)), max(1, intval($page))) - 1) * 25;
        }
        else {
            $resultOffset = 0;
        }

        // get the actual list of users
        /** @var QueryBuilder $emails */
        $emails = self::searchBuilder($orm, $request, $joins)->distinct(true)->select('m');

        // figure out how to sort
        if(!empty($order = $request->get('order'))) {
            $field = explode(' ', $order)[0];
            $direction = explode(' ', $order)[1];
            if($direction != 'ASC' && $direction != 'DESC')
                $direction = 'DESC';
            // no extra join information needed
            if($field == 'created' || $field == 'status') {
                $emails = $emails->orderBy('m.' . $field, $direction);
            }
        }
        else {
            $emails = $emails->orderBy('m.created', 'DESC');
        }

        $emails = $emails
            ->setFirstResult($resultOffset)
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();

        $status = (new \ReflectionClass('WhiteOctober\SwiftMailerDBBundle\EmailInterface'))->getConstants();

        // get count for each template that has been sent
        $templates = [];
        $templatesDir = new \DirectoryIterator(self::$emailsDir);
        foreach($templatesDir as $f) {
            /** @var \DirectoryIterator $f */
            if($f->getFilename() == 'layout.html.php')
                continue;
            if(!$f->isDot()) {
                // get count for current email category
                $base = basename($f->getFilename(), '.html.' . $f->getExtension());
                $count = self::searchBuilder($orm, $request, $joins)
                    ->select('COUNT(DISTINCT m.id)')
                    ->andWhere('m.message LIKE \'%s:' . (17 + strlen($base)) . ':"{"category":["' . $base . '"]}"%\'')
                    ->getQuery()
                    ->getSingleScalarResult();
                $templates[$base] = [
                    'id' => $base,
                    'count' => $count
                ];
            }
        }

        return $this->render('AdminBundle:Emails:tab.html.php', [
            'emails' => $emails,
            'status' => $status,
            'templates' => $templates,
            'total' => $total,
            'recent' => $recent,
            'entities' => $entities,
            'repository' => $orm->getRepository('StudySauceBundle:User')
        ]);
    }

    public static $templateVars = [];
    public static $emails = [];
    private static $tables = [];

    /**
     * @param string $_email
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function templateAction($_email = '')
    {
        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var \Swift_Message $template */
        $template = $this->buildEmail($_email, [], $params, $objects);

        return $this->render('AdminBundle:Emails:template.html.php', [
                'headers' => $template->getHeaders(),
                'template' => $template->getBody(),
                'params' => $params,
                'objects' => $objects,
                'subject' => $template->getSubject()
            ]);
    }

    /**
     * @param $_email
     * @param $variables
     * @param $params
     * @param $objects
     * @return Swift_Message
     */
    public function buildEmail($_email, $variables = [], &$params = [], &$objects = [])
    {
        /** @var EntityManager $orm */
        $orm = $this->getDoctrine()->getManager();
        $fullName = 'StudySauceBundle:Emails:' . $_email . '.html.php';

        // get automatic variables requred for ever send
        $params = [];
        $objects = [];
        if($_email == '') {
            return new Response('');
        }
        self::$tables = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        // look up inputs
        // also check template file for usages
        $templateText = implode("", file($this->getPathFromName($fullName)));
        // TODO: make this class work with torch and laurel and other bundles and group contracts
        $reflector = new \ReflectionClass('\StudySauce\Bundle\Controller\EmailsController');
        foreach($reflector->getMethods() as $m)
        {
            $methodText = $this->_getMethodText($m);
            // check if current method has a reference to the template
            if(strpos($methodText, $fullName) !== false) {

                // setup method inputs from function parameters
                foreach($m->getParameters() as $p)
                {
                    $parameterName = $p->getName();
                    $className = !empty($p->getClass()) ? basename($p->getClass()->getFileName(), '.php') : $p->getName();
                    $this->generateParams($className, $parameterName, $methodText . $templateText, $variables, $params, $objects);
                }
                // mock the email send using this class
                $this->evalSubject($objects);
                call_user_func_array([$this, $m->getName()], $objects);
                break;
            }
        }
        if(empty($params)) {
            // derive variables from template alone without types
            preg_match_all('/\$([a-z0-9]*)/i', $templateText, $matches);
            foreach(array_unique($matches[1]) as $p) {
                // don't bother with made up variable names
                $this->generateParams($p, $p, $templateText, $variables, $params, $objects, true);
            }
        }
        if(empty(self::$emails)) {
            // generate email send function
            /** @var User $user */
            $user = $objects['user'];
            /** @var \Swift_Message $message */
            $message = Swift_Message::newInstance()
                ->setFrom('admin@studysauce.com')
                ->setTo($user->getEmail())
                ->setBody($this->render($fullName, $objects)->getContent(), 'text/html');
            $headers = $message->getHeaders();
            $headers->addParameterizedHeader(
                'X-SMTPAPI',
                preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => [$_email]]))
            );
            $this->evalSubject($objects);
            call_user_func_array([$this, 'send'], [$message]);
        }

        /** @var \Swift_Message $template */
        $template = self::$emails[0];
        return $template;
    }


    /**
     * @param $objects
     */
    private function evalSubject($objects)
    {
        extract($objects);
        // alter subject line
        if(!empty($this->subject)) {
            $newSubject = 'return "' . preg_replace_callback('/\{([a-z0-9_]*?)(\:[a-z0-9_]*?)*\}/i', function ($m) {
                        return '" . $' . lcfirst($m[1]) . (!empty($m[2]) ? ('->get' . substr($m[2], 1) . '()') : '') . ' . "';
                    }, $this->subject) . '";';
            $this->subject = eval($newSubject);
        }
    }

    /**
     * @param $template
     * @return mixed
     */
    private function getPathFromName($template)
    {
        $parser = $this->container->get('templating.name_parser');
        $locator = $this->container->get('templating.locator');

        $path = $locator->locate($parser->parse($template));
        return $path;
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response $response
     * @return Response
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        /** @var PhpEngine $template */
        $template = $this->container->get('templating');
        // render submitted template instead
        if(!empty($this->template))
        {
            // replace variables in template
            $newTemplate = preg_replace_callback('/\{([a-z0-9_]*?)(\:[a-z0-9_]*?)*\}/i', function ($m) {
                        return '<?php print $' . lcfirst($m[1]) . (!empty($m[2]) ? ('->get' . substr($m[2], 1) . '()') : '') . '; ?>';
                    }, $this->template);
            $this->template = $newTemplate;
            /** @var TemplateNameParserInterface $parser */
            $parser = $this->container->get('templating.name_parser');
            /** @var GlobalVariables $globals */
            $globals = $this->container->get('templating.globals');
            $custom = new CustomPhpEngine($parser, $this->container, new StringLoader($this->template), $globals);
            $text = $custom->render($view, $parameters);
        }
        else
        {
            $text = $template->render($view, $parameters);
        }
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($text);
        return $response;
    }

    /**
     * @param $_email
     * @param Request $request
     * @return JsonResponse
     */
    public function sendAction($_email, Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        if(!empty($request->get('confirm'))) {
            $this->confirm = true;
        }

        if(!empty($request->get('template'))) {
            $this->template = $request->get('template');
        }

        if(!empty($request->get('subject'))) {
            $this->subject = $request->get('subject');
        }

        /** @var \Swift_Message $template */
        if(!empty($request->get('variables'))) {
            foreach ($request->get('variables') as $line) {
                if(empty($line['userEmail']))
                    continue;
                self::$emails = [];
                $this->buildEmail($_email, $line, $params, $objects);
            }
        }

        return new JsonResponse(true);
    }

    public $template = '';
    public $confirm = false;
    public $subject = '';
    /**
     * @param Swift_Mime_Message $message
     */
    protected function send(\Swift_Mime_Message $message)
    {
        if(!empty($this->subject))
        {
            $message->setSubject($this->subject);
        }
        if($this->confirm) {
            parent::send($message);
        }
        self::$emails[] = $message;
    }

    /**
     * @param Swift_Mime_Message $message
     */
    protected function sendToAdmin(\Swift_Mime_Message $message)
    {
        if(!empty($this->subject))
        {
            $message->setSubject($this->subject);
        }
        if($this->confirm) {
            parent::sendToAdmin($message);
        }
        self::$emails[] = $message;
    }

    protected static $autoInclude = ['user' => ['Email']];

    /**
     * @param $className
     * @param $parameterName
     * @param $subject
     * @param array $variables
     * @param array $params
     * @param array $objects
     * @param bool $entitiesOnly
     */
    private function generateParams($className, $parameterName, $subject, &$variables = [], &$params = [], &$objects = [], $entitiesOnly = false)
    {
        /** @var EntityManager $orm */
        $orm = $this->getDoctrine()->getManager();

        // if we are dealing with an entity class try to figure out which methods are used
        if(($classI = array_search(true, array_map(function ($t) use ($className) {
                        return strpos(strtolower($t), strtolower($className)) !== false;
                    }, self::$tables))) !== false) {
            $data = $orm->getMetadataFactory()->getMetadataFor(self::$tables[$classI]);
            $namespace = explode('\\', self::$tables[$classI]);
            $className = end($namespace);
            $mockName = 'Mock' . $className;
            $instance = 'class ' . $mockName . ' extends ' . self::$tables[$classI] . ' {
private $variables; private $objects;
public function __construct(&$variables, &$objects) { $this->variables = $variables;
if((new \ReflectionClass(get_parent_class($this)))->getConstructor() != null) {
    parent::__construct();}}
';
            preg_match_all('/' . $parameterName . '\s*->\s*get([a-z0-9_]*?)\s*\(/i', $subject, $properties);
            // use the entity for the field if no inputs are detected
            if (!count($properties[1])) {
                $params[$parameterName]['name'] = $className;
                $params[$parameterName]['prop'] = '';
            }
            if (isset(self::$autoInclude[$parameterName])) {
                $properties = array_unique(array_merge(self::$autoInclude[$parameterName], $properties[1]));
                self::$templateVars = array_merge(
                    self::$templateVars,
                    array_map(
                        function ($c) use ($parameterName) {
                            return $parameterName . $c;
                        },
                        self::$autoInclude[$parameterName]
                    )
                );
            } else {
                $properties = array_unique($properties[1]);
            }
            foreach ($properties as $c) {
                if(!in_array(lcfirst($c), $data->getFieldNames()) && !in_array(lcfirst($c), $data->getAssociationNames())) {
                    continue;
                }
                $params[$parameterName . $c]['name'] = $className;
                $params[$parameterName . $c]['prop'] = $c;
                // if its an associated field return the object at runtime
                if(in_array(lcfirst($c), $data->getAssociationNames())) {
                    $instance .= 'public function get' . $c . '() { \Admin\Bundle\Controller\EmailsController::$templateVars[] = "' . $parameterName . $c . '"; return isset($this->objects["' . lcfirst($c) . '"]) ? $this->objects["' . lcfirst($c) . '"] : "{' . $className . ':' . $c . '}"; }
';
                }
                // if it's an email field
                else if (strpos(strtolower($c), 'email') !== false) {
                    $instance .= 'public function get' . $c . '() { \Admin\Bundle\Controller\EmailsController::$templateVars[] = "' . $parameterName . $c . '"; return isset($this->variables["' . $parameterName . $c . '"]) ? $this->variables["' . $parameterName . $c . '"] : "' . $className . '_' . $c . '@mailinator.com"; }
';
                } else {
                    $instance .= 'public function get' . $c . '() { \Admin\Bundle\Controller\EmailsController::$templateVars[] = "' . $parameterName . $c . '"; return isset($this->variables["' . $parameterName . $c . '"]) ? $this->variables["' . $parameterName . $c . '"] : "{' . $className . ':' . $c . '}"; }
';
                }
            }
            if(!class_exists($mockName)) {
                eval($instance . '
};');
            }
            $objects[$parameterName] = eval('return new ' . $mockName . '($variables, $objects);');
        }
        elseif(!$entitiesOnly)
        {
            self::$templateVars[] = $parameterName;
            $params[$parameterName]['name'] = $parameterName;
            $params[$parameterName]['prop'] = '';
            if (strpos(strtolower($parameterName), 'email') !== false) {
                $objects[$parameterName] = isset($variables[$parameterName]) ? $variables[$parameterName] : ($parameterName . '@mailinator.com');
            }
            else {
                $objects[$parameterName] = isset($variables[$parameterName]) ? $variables[$parameterName] : '{' . $parameterName . '}';
            }
        }
    }

    /**
     * @param \ReflectionMethod $m
     * @return string
     */
    private function _getMethodText(\ReflectionMethod $m)
    {
        // check if current method has a reference to the template
        $line_start     = $m->getStartLine() - 1;
        $line_end       = $m->getEndLine();
        $line_count     = $line_end - $line_start;
        $line_array     = file($m->getFileName());
        $methodText = implode("", array_slice($line_array,$line_start,$line_count));
        return $methodText;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        /** @var EntityManager $orm */
        $orm = $this->getDoctrine()->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        self::$tables = $orm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        // look up inputs
        // also check template file for usages
        foreach(self::$tables as $t)
        {
            $namespace = explode('\\', $t);
            $parameterName = strtolower(end($namespace));
            if(strpos(strtolower($request->get('field')), $parameterName) !== 0) {
                continue;
            }

            // find setter method that matches field name
            // TODO: check table meta data instead?  It is smart enough to distinguish associated fields
            $getters = [];
            $default = '';
            $reflector = new \ReflectionClass($t);
            foreach($reflector->getMethods() as $c) {
                if(substr($c->getName(), 0, 3) == 'set')
                    continue;
                if($request->get('field') == strtolower(end($namespace)) . substr($c->getName(), 3)) {
                    $default = lcfirst(substr($c->getName(), 3));
                    $getters[] = lcfirst(substr($c->getName(), 3));
                }
                if(in_array(strtolower(end($namespace)) . substr($c->getName(), 3), explode(',', $request->get('alt')))) {

                    // search database
                    $getters[] = lcfirst(substr($c->getName(), 3));
                }
            }

            $getters = array_unique($getters);

            // do search
            $search = $orm->getRepository($t)->createQueryBuilder('m')
                ->select(array_map(function ($g) {return 'm.' . $g;}, $getters))
                ->andWhere('m.' . implode(' LIKE \'%' . $request->get('q') . '%\' OR m.', $getters) . ' LIKE \'%' . $request->get('q') . '%\'')
                ->getQuery()
                ->execute();

            return new JsonResponse(array_map(function ($x) use ($default, $namespace) {
                        $x = array_map(function ($x) {return $x instanceof \DateTime ? $x->format('r') : $x;}, $x);
                        $value = [
                            'text' => $x[$default],
                            'value' => $x[$default]];
                        $alt = array_diff_key($x, [$default => '']);
                        $value['alt'] = array_combine(array_map(function ($k) use ($namespace) {return strtolower(end($namespace)) . ucfirst($k);}, array_keys($alt)), $alt);
                        return $value + array_values($alt);
                    }, $search));
        }
        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        try {
            if(!empty($request->get('name'))) {
                $fh = fopen(self::$emailsDir . DIRECTORY_SEPARATOR . $request->get('name') . '.html.php', 'w+');
                $newTemplate = preg_replace_callback('/\{([a-z0-9_]*?)(\:[a-z0-9_]*?)*\}/i', function ($m) {
                    return '<?php print $' . lcfirst($m[1]) . (!empty($m[2]) ? ('->get' . substr($m[2], 1) . '()') : '') . '; ?>';
                }, $request->get('template'));
                fwrite($fh, $newTemplate);
                fclose($fh);
            }
        }
        catch (Exception $ex) {

        }

        return new JsonResponse(true);
    }
}

