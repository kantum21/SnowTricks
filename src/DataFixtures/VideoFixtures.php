<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $video = new Video();
        $video->setLink("<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/JiVKdWt_92c\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>");
        $manager->persist($video);
        $this->addReference('Video' . '_' . 0, $video);

        $video = new Video();
        $video->setLink("<iframe frameborder=\"0\" width=\"480\" height=\"270\" src=\"https://www.dailymotion.com/embed/video/x7l1f8h\" allowfullscreen allow=\"autoplay\"></iframe>");
        $manager->persist($video);
        $this->addReference('Video' . '_' . 1, $video);

        $manager->flush();
    }
}
