<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categorie;
use App\Entity\Unite;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $stockAlerte = null; // Nom modifié en camelCase

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unite $unite = null;

    #[ORM\OneToOne(mappedBy: 'article', cascade: ['persist', 'remove'])]
    private ?Stock $stock = null;

    /**
     * @var Collection<int, Entree>
     */
    #[ORM\OneToMany(targetEntity: Entree::class, mappedBy: 'article')]
    private Collection $entrees;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\OneToMany(targetEntity: Sortie::class, mappedBy: 'article')]
    private Collection $sorties;

    public function __construct()
    {
        $this->entrees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getStockAlerte(): ?int
    {
        return $this->stockAlerte; // Accès à la nouvelle propriété camelCase
    }

    public function setStockAlerte(int $stockAlerte): static
    {
        $this->stockAlerte = $stockAlerte;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getUnite(): ?Unite
    {
        return $this->unite;
    }

    public function setUnite(?Unite $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): static
    {
        // set the owning side of the relation if necessary
        if ($stock->getArticle() !== $this) {
            $stock->setArticle($this);
        }

        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, Entree>
     */
    public function getEntrees(): Collection
    {
        return $this->entrees;
    }

    public function addEntree(Entree $entree): static
    {
        if (!$this->entrees->contains($entree)) {
            $this->entrees->add($entree);
            $entree->setArticle($this);
        }

        return $this;
    }

    public function removeEntree(Entree $entree): static
    {
        if ($this->entrees->removeElement($entree)) {
            // set the owning side to null (unless already changed)
            if ($entree->getArticle() === $this) {
                $entree->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): static // Correction de Sorty -> Sortie
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties->add($sortie);
            $sortie->setArticle($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static // Correction de Sorty -> Sortie
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getArticle() === $this) {
                $sortie->setArticle(null);
            }
        }

        return $this;
    }
}
