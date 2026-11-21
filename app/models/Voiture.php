<?php
declare(strict_types=1);

class Voiture extends Vehicule implements Entretenable
{
  public const SEUIL_ENTRETIEN = 15000;
  private const PLACES_SANS_SUPPLEMENT = 5;
  private const SUPPLEMENT_JOURNALIER = 10.0;

  public function __construct(
    string $immatriculation,
    string $marque,
    string $modele,
    float $tarifJournalier,
    private int $nbPlaces,
    private int $kmDepuisEntretien = 0
  ) {
    // Vérifié avant parent::__construct() pour ne pas compter un véhicule invalide
    if ($nbPlaces < 1) {
      throw new InvalidArgumentException('Une voiture doit avoir au moins 1 place.');
    }
    if ($kmDepuisEntretien < 0) {
      throw new InvalidArgumentException('Le kilométrage ne peut pas être négatif.');
    }

    parent::__construct($immatriculation, $marque, $modele, $tarifJournalier);
  }

  public function getNbPlaces(): int
  {
    return $this->nbPlaces;
  }

  public function getKmDepuisEntretien(): int
  {
    return $this->kmDepuisEntretien;
  }

  public function getType(): string
  {
    return 'Voiture';
  }

  public function calculerPrixHT(int $jours): float
  {
    $tarif = $this->tarifJournalier;

    if ($this->nbPlaces > self::PLACES_SANS_SUPPLEMENT) {
      $tarif += self::SUPPLEMENT_JOURNALIER;
    }

    return round($tarif * $jours, 2);
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
}
