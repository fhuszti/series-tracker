<?php
namespace App\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Apporte un traçage de la date de création et de dernière modification aux entités qui l'utilisent.
 * ATTENTION : toujours ajouter aussi l'annotation suivante au-dessus des entités concernées :
 * ORM\HasLifecycleCallbacks()
 */
trait TimestampableTrait
{
    /**
     * @var DateTimeImmutable La date d'ajout de l'entité
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable La date de dernière modification de l'entité
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $updatedAt;


    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
