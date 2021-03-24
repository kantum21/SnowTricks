<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $emails = [
            'thomas@yopmail.com',
            'camille@yopmail.com',
            'martin@yopmail.com',
            'marion@yopmail.com',
            'mathilde@yopmail.com'
        ];

        $usernames = [
            'Thomas',
            'Camille',
            'Martin',
            'Marion',
            'Mathilde'
        ];

        for ($i = 1; $i<= 5; $i++)
        {
            $user = new User();
            $user->setEmail($emails[$i - 1]);
            $user->setUsername($usernames[$i - 1]);
            $user->setPassword($this->encoder->encodePassword($user, 'ffjjddkk@ST'));
            $user->setActivated(true);

            $manager->persist($user);
            $this->addReference('User' . '_' . $i, $user);
        }

        $manager->flush();
    }
}
