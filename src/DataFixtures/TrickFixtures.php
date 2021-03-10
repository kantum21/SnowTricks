<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++)
        {
            $trick = new Trick();
            $trick->setName('Trick ' . $i);
            $trick->setSlug($this->slugger->slug(strtolower($trick->getName())));
            $trick->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac nibh ac nunc mattis maximus eget vitae libero. Curabitur vitae dolor in magna vulputate imperdiet a eu enim. Integer venenatis sit amet metus non tristique. Donec vehicula tortor vel scelerisque lacinia. Cras mattis, quam ac molestie fringilla, sem ligula gravida mi, quis imperdiet odio lacus at velit. Curabitur commodo ex eu sapien malesuada, nec sagittis libero porta. Duis quis enim id lacus aliquet volutpat eget sed risus. Integer vehicula, mi ac porta pretium, enim dolor gravida orci, nec rhoncus metus purus eu nunc. Suspendisse ultricies dui elit, sit amet sollicitudin orci tincidunt ac. Nullam eleifend faucibus risus, sit amet rhoncus tellus gravida non.');
            $trick->setCreatedAt(new \DateTime());
            $trick->setCategory($this->getReference('Category' . '_' . rand(0, 6)));
            $trick->setMainPicture($this->getReference('Picture' . '_' . 0));
            $trick->addPicture($this->getReference('Picture' . '_' . 0));
            $trick->addVideo($this->getReference('Video' . '_' . 0));
            $trick->addVideo($this->getReference('Video' . '_' . 1));

            $manager->persist($trick);
            $this->addReference('Trick' . '_' . $i, $trick);

        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            PictureFixtures::class,
            VideoFixtures::class
            ];
    }
}
