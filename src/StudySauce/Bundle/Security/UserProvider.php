<?php

namespace StudySauce\Bundle\Security;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseUserProvider;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 * @package StudySauce\Bundle\Security
 */
class UserProvider extends BaseUserProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * Constructor
     *
     * @param UserManagerInterface $userManager
     * @param ContainerInterface $container
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param array $properties
     */
    public function __construct(UserManagerInterface $userManager, ContainerInterface $container, EncoderFactoryInterface $encoderFactory, array $properties)
    {
        $this->container = $container;
        $this->encoderFactory = $encoderFactory;
        parent::__construct($userManager, $properties);
    }

    /**
     * @param string $owner
     * @return bool
     */
    public function isConnectible($owner)
    {
        return isset($this->properties[$owner]);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        /** @var EntityManager $orm */
        //$orm = $this->container->get('doctrine')->getManager();
        /** @var Request $request */
        //$request = $this->container->get('request');
        /** @var User $user */
        /** @var PathUserResponse $response */
        $prop = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== ($previousUser = $this->userManager->findUserBy([$prop => $username]))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        //$user->setFirst($response->getFirst());
        //$user->setLast($response->getLast());
        $user->$setter_id($username);
        if(!empty($response->getRefreshToken())) {
            $user->$setter_token($response->getRefreshToken());
        }
        else {
            $user->$setter_token($response->getAccessToken());
        }

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        /** @var EntityManager $orm */
        //$orm = $this->container->get('doctrine')->getManager();
        /** @var Request $request */
        //$request = $this->container->get('request');
        /** @var PathUserResponse $response */
        if(empty($response->getUsername()) && empty($response->getEmail())) {
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $response->getUsername()));
        }
        /** @var User $user */
        $prop = $this->getProperty($response);
        if(!empty($response->getUsername()))
            $user = $this->userManager->findUserBy([$prop => $response->getUsername()]);
        // allow user with same email address to connect because we trust facebook and google auth
        if(empty($user) && !empty($response->getUsername()))
            $user = $this->userManager->findUserBy(['email' => $response->getEmail()]);

        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);

        // create new user here
        if (!empty($user) && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {

            // these fields can always be updated and sync from the service
            $setter_id = $setter.'Id';
            $user->$setter_id($response->getUsername());
            $setter_token = $setter.'AccessToken';
            $user->$setter_token($response->getAccessToken());
            if($response instanceof PathUserResponse) {
                if(!empty($response->getFirst()))
                    $user->setFirst($response->getFirst());
                if(!empty($response->getLast()))
                    $user->setLast($response->getLast());
            }

            $this->userManager->updateUser($user);
        }
        else
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $response->getUsername()));

        return $user;
    }

}