<?php

namespace App\Entity;

use App\Repository\LigneCommandeProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeProduitRepository::class)]
class LigneCommandeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'lesLignesCommandeProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $uneCommande = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $unProduit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getUneCommande(): ?Commande
    {
        return $this->uneCommande;
    }

    public function setUneCommande(?Commande $uneCommande): static
    {
        $this->uneCommande = $uneCommande;

        return $this;
    }

    public function getUnProduit(): ?Produit
    {
        return $this->unProduit;
    }

    public function setUnProduit(?Produit $unProduit): static
    {
        $this->unProduit = $unProduit;

        return $this;
    }
}
