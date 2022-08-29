<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=LigneCommandeRepository::class)
 */
class LigneCommande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class)
     * @Groups ("post:read")
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=commande::class)
     * @Groups ("post:read")
     */
    private $commande;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prixProduit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups ("post:read")
     */
    private $quantite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Products
    {
        return $this->produit;
    }

    public function setProduit(?Products $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getCommande(): ?commande
    {
        return $this->commande;
    }

    public function setCommande(?commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getPrixProduit(): ?int
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(?int $prixProduit): self
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}
