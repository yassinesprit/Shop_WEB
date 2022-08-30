<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=products::class)
     * @Groups ("post:read")
     */
    private $produit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups ("post:read")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups ("post:read")
     */
    private $prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?products
    {
        return $this->produit;
    }

    public function setProduit(?products $produit): self
    {
        $this->produit = $produit;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
