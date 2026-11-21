<?php
declare(strict_types=1);

class Location
{
  public function __construct(
    private Client $client,
    private Vehicule $vehicule,
    private int $jours,
    private float $montantTTC,
    private ?DateTime $dateDebut = null
  ) {
    if ($jours < 1) {
      throw new InvalidArgumentException('Une location dure au moins 1 jour.');
    }
    if ($montantTTC < 0) {
      throw new InvalidArgumentException('Le montant TTC ne peut pas être négatif.');
    }

    // Date du jour par défaut ; clone pour ne pas partager l'objet DateTime de l'appelant
    $this->dateDebut = $dateDebut !== null ? clone $dateDebut : new DateTime();
  }

  public function getClient(): Client
  {
    return $this->client;
  }

  public function getVehicule(): Vehicule
  {
    return $this->vehicule;
  }

  public function getJours(): int
  {
    return $this->jours;
  }

  public function getMontantTTC(): float
  {
    return $this->montantTTC;
  }

  public function getDateDebut(): DateTime
  {
    // Copie pour que l'appelant ne puisse pas modifier la date de la location
    return clone $this->dateDebut;
  }

  public function __toString(): string
  {
    return sprintf(
      '%s - %s %s (%s) - %d %s - %s €',
      $this->client->getNomComplet(),
      $this->vehicule->getMarque(),
      $this->vehicule->getModele(),
      $this->vehicule->getImmatriculation(),
      $this->jours,
      $this->jours > 1 ? 'jours' : 'jour',
      number_format($this->montantTTC, 2, '.', '')
    );
  }
}
