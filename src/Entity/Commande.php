<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'lesCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $unUtilisateur = null;

    /**
     * @var Collection<int, LigneCommandeProduit>
     */
    #[ORM\OneToMany(targetEntity: LigneCommandeProduit::class, mappedBy: 'uneCommande', orphanRemoval: true, cascade: ['persist'])]
    private Collection $lesLignesCommandeProduits;

    public function __construct()
    {
        $this->lesLignesCommandeProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUnUtilisateur(): ?Utilisateur
    {
        return $this->unUtilisateur;
    }

    public function setUnUtilisateur(?Utilisateur $unUtilisateur): static
    {
        $this->unUtilisateur = $unUtilisateur;

        return $this;
    }

    /**
     * @return Collection<int, LigneCommandeProduit>
     */
    public function getLesLignesCommandeProduits(): Collection
    {
        return $this->lesLignesCommandeProduits;
    }

    public function addLesLignesCommandeProduit(LigneCommandeProduit $lesLignesCommandeProduit): static
    {
        if (!$this->lesLignesCommandeProduits->contains($lesLignesCommandeProduit)) {
            $this->lesLignesCommandeProduits->add($lesLignesCommandeProduit);
            $lesLignesCommandeProduit->setUneCommande($this);
        }

        return $this;
    }

    public function removeLesLignesCommandeProduit(LigneCommandeProduit $lesLignesCommandeProduit): static
    {
        if ($this->lesLignesCommandeProduits->removeElement($lesLignesCommandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($lesLignesCommandeProduit->getUneCommande() === $this) {
                $lesLignesCommandeProduit->setUneCommande(null);
            }
        }

        return $this;
    }
}
