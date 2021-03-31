<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $contents = [
            "What an amazing trick !",
            "It looks very fun !",
            "Crazy, i have to try it !"
        ];

        for ($i = 1; $i <= 3;$i++ )
        {
            $comment = new Comment();
            $comment->setContent($contents[$i - 1]);
            $comment->setCreatedAt(new \DateTime("now"));
            $comment->setTrick($this->getReference('Trick_' . 1));
            $comment->setUser($this->getReference('User_' . $i));

            $manager->persist($comment);
            $this->addReference('Comment_' . $i, $comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
            UserFixtures::class
        ];
    }
}
