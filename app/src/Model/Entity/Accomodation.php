<?php

namespace App\Model\Entity;

class Accommodation
{
    private $id;
    private $price;
    private $surface;
    private $description;
    private $capacity;
    private $id_owner;
    private $id_type;
    private $id_address;

    // Getters et Setters
    public function getId(): int
    {
        return $this->id;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getSurface(): int
    {
        return $this->surface;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getCapacity(): int
    {
        return $this->capacity;
    }
    public function getIdOwner(): int
    {
        return $this->id_owner;
    }
    public function getIdType(): int
    {
        return $this->id_type;
    }
    public function getIdAddress(): int
    {
        return $this->id_address;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
    public function setSurface(int $surface): void
    {
        $this->surface = $surface;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }
    public function setIdOwner(int $id_owner): void
    {
        $this->id_owner = $id_owner;
    }
    public function setIdType(int $id_type): void
    {
        $this->id_type = $id_type;
    }
    public function setIdAddress(int $id_address): void
    {
        $this->id_address = $id_address;
    }
}
