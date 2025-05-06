<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'post_images')]
#[ORM\Entity]
class PostImages
{
    #[ORM\Column(name: 'post_image_id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $postImageId = null;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'Post_id', nullable: false, onDelete: 'CASCADE')]
    private ?Posts $post = null;

    #[ORM\Column(name: 'image', type: Types::BLOB)]
    #[Assert\NotBlank(message: 'Image cannot be blank.')]
    private $image;

    public function getPostImageId(): ?int
    {
        return $this->postImageId;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): static
    {
        $this->image = $image;

        return $this;
    }
}
