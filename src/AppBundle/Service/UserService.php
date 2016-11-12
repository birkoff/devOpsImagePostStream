<?php


namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectRepository;

class UserService
{
    /**
     * @var UserRepository $repository
     */
    private $repository;

    /**
     * UserService constructor.
     * UserRepository
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findByUsername($username)
    {
        $user = new User();
        $user->setUsername($username)->setPassword('123');
        return $user;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        return $this->repository->loadUserByUsername($username);
    }

    public function checkCredentials($credentials, User $user)
    {
        $plainPassword = $credentials['password'];
        if ($this->encoder->isPasswordValid($user, $plainPassword)) {
            return true;
        }

        throw new BadCredentialsException();
    }

}