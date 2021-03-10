<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trick", mappedBy="pictures")
     */
    private $tricks;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please choose a name for alt attribute")
     */
    private $alt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="mainPicture")
     */
    private $tricksMainPicture;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->tricksMainPicture = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->addPicture($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            $trick->removePicture($this);
        }

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricksMainPicture(): Collection
    {
        return $this->tricksMainPicture;
    }

    public function addTricksMainPicture(Trick $tricksMainPicture): self
    {
        if (!$this->tricksMainPicture->contains($tricksMainPicture)) {
            $this->tricksMainPicture[] = $tricksMainPicture;
            $tricksMainPicture->setMainPicture($this);
        }

        return $this;
    }

    public function removeTricksMainPicture(Trick $tricksMainPicture): self
    {
        if ($this->tricksMainPicture->contains($tricksMainPicture)) {
            $this->tricksMainPicture->removeElement($tricksMainPicture);
            // set the owning side to null (unless already changed)
            if ($tricksMainPicture->getMainPicture() === $this) {
                $tricksMainPicture->setMainPicture(null);
            }
        }

        return $this;
    }
}
