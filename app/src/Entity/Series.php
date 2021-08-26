<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Une série importée depuis IMDb
 * @ORM\Entity(repositoryClass=SeriesRepository::class)
 */
class Series
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var string L'ID de la série sur IMDb
     * @ORM\Column(type="string", length=20)
     * @Assert\Regex(
     *     pattern="/^tt[0-9]{7,18}$/",
     *     message="L'ID d'une série sur IMDb doit commencer par les lettres 'tt' et être suivi d'au moins 7 chiffres"
     * )
     */
    private string $imdbId;

    /**
     * @var int Le rang de la série sur IMDb
     * @ORM\Column(type="smallint")
     * @Assert\Positive(
     *     message="Le rang d'une série sur IMDb doit être supérieur à 0"
     * )
     */
    private int $imdbRank;

    /**
     * @var string Le titre de la série
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Le titre de la série doit comporter au moins {{ limit }} caractère",
     *      maxMessage = "Le titre de la série ne doit pas comporter plus de {{ limit }} caractères"
     * )
     */
    private string $title;

    /**
     * @var string Le titre complet de la série
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Le titre complet de la série doit comporter au moins {{ limit }} caractère",
     *      maxMessage = "Le titre complet de la série ne doit pas comporter plus de {{ limit }} caractères"
     * )
     */
    private string $fullTitle;

    /**
     * @var int|null L'année de sortie de la série
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Range(
     *      min = 1850,
     *      max = 2100,
     *      notInRangeMessage = "L'année de sortie de la série doit être entre {{ min }} et {{ max }}",
     * )
     */
    private ?int $year = null;

    /**
     * @var string|null Le lien vers le poster de la série
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "L'URL du poster de la série doit comporter au moins {{ limit }} caractère",
     *      maxMessage = "L'URL du poster de la série ne doit pas comporter plus de {{ limit }} caractères"
     * )
     * @Assert\Url(
     *    message = "L'adresse '{{ value }}' n'est pas une URL valide",
     * )
     */
    private ?string $imageUrl = null;

    /**
     * @var float La note de la série sur IMDb
     * @ORM\Column(type="float")
     * @Assert\Range(
     *      min = 0,
     *      max = 10,
     *      notInRangeMessage = "La note IMDb de la série doit être entre {{ min }} et {{ max }}",
     * )
     */
    private float $imdbRating;

    /**
     * @var bool L'utilisateur a-t-il déjà vu la série ?
     * @ORM\Column(type="boolean")
     */
    private bool $isSeen = false;

    /**
     * @var bool L'utilisateur désire-t-il regarder la série ?
     * @ORM\Column(type="boolean")
     */
    private bool $isToWatch = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImdbId(): ?string
    {
        return $this->imdbId;
    }

    public function setImdbId(string $imdb_id): self
    {
        $this->imdbId = $imdb_id;

        return $this;
    }

    public function getImdbRank(): ?int
    {
        return $this->imdbRank;
    }

    public function setImdbRank(int $imdbRank): self
    {
        $this->imdbRank = $imdbRank;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFullTitle(): ?string
    {
        return $this->fullTitle;
    }

    public function setFullTitle(string $fullTitle): self
    {
        $this->fullTitle = $fullTitle;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImdbRating(): ?float
    {
        return $this->imdbRating;
    }

    public function setImdbRating(float $imdbRating): self
    {
        $this->imdbRating = $imdbRating;

        return $this;
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

        return $this;
    }

    public function getIsToWatch(): ?bool
    {
        return $this->isToWatch;
    }

    public function setIsToWatch(bool $isToWatch): self
    {
        $this->isToWatch = $isToWatch;

        return $this;
    }
}
