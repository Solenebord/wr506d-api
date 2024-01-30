<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager)
    {
        // admin
        $user = new User();
        $user->setUsername('root');
        $user->setEmail('root@root.com');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'root'
        ));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();

        // user
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@user.com');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'user'
        ));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}