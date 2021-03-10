<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PictureFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $picture = new Picture();
        $picture->setPicture('defaultTrick.jpg');
        $picture->setAlt('jump');
        $manager->persist($picture);
        $this->addReference('Picture' . '_' . 0, $picture);

        $picture = new Picture();
        $picture->setPicture('snowboard_trick1.jpg');
        $picture->setAlt('jump');
        $manager->persist($picture);
        $this->addReference('Picture' . '_' . 1, $picture);

        $picture = new Picture();
        $picture->setPicture('snowboard_trick2.jpg');
        $picture->setAlt('huge');
        $manager->persist($picture);
        $this->addReference('Picture' . '_' . 2, $picture);

        $picture = new Picture();
        $picture->setPicture('snowboard_trick3.jpg');
        $picture->setAlt('amazing');
        $manager->persist($picture);
        $this->addReference('Picture' . '_' . 3, $picture);

        $manager->flush();
    }
}
