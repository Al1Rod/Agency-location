<?php
declare(strict_types=1);

class Utilitaire extends Vehicule implements Entretenable
{
  public const SEUIL_ENTRETIEN = 10000;
  public const FRAIS_NETTOYAGE = 50.0;

  public function __construct(
    string $immatriculation,
    string $marque,
    string $modele,
    float $tarifJournalier,
    private float $volume,
    private int $kmDepuisEntretien = 0
  ) {
    // Vérifié avant parent::__construct() pour ne pas compter un véhicule invalide
    if ($volume <= 0) {
      throw new InvalidArgumentException('Le volume doit être strictement supérieur à 0.');
    }
    if ($kmDepuisEntretien < 0) {
      throw new InvalidArgumentException('Le kilométrage ne peut pas être négatif.');
    }

    parent::__construct($immatriculation, $marque, $modele, $tarifJournalier);
  }

  public function getVolume(): float
  {
    return $this->volume;
  }

  public function getKmDepuisEntretien(): int
  {
    return $this->kmDepuisEntretien;
  }

  public function getType(): string
  {
    return 'Utilitaire';
  }

  // Frais de nettoyage facturés une seule fois, quelle que soit la durée
  public function calculerPrixHT(int $jours): float
  {
    return round($this->tarifJournalier * $jours + self::FRAIS_NETTOYAGE, 2);
  }

  public function ajouterKilometres(int $km): static
  {
    if ($km < 0) {
      throw new InvalidArgumentException('Le nombre de kilomètres ajoutés ne peut pas être négatif.');
    }

    $this->kmDepuisEntretien += $km;
    return $this;
  }

  public function besoinEntretien(): bool
  {
    return $this->kmDepuisEntretien >= self::SEUIL_ENTRETIEN;
  }

  public function entretenir(): void
  {
    $this->ajouterHistorique(sprintf('Entretien effectué (%d km remis à zéro)', $this->kmDepuisEntretien));
    $this->kmDepuisEntretien = 0;
  }

  public function __toString(): string
  {
    $volume = '<p>Volume : ' . $this->volume . ' m³</p>';

    return str_replace('</article>', $volume . '</article>', parent::__toString());
  }
}
