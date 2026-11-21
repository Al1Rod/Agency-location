<?php
declare(strict_types=1);

final class VeloElectrique extends Vehicule
{
  private const JOURS_MIN_REDUCTION = 7;
  private const TAUX_REDUCTION = 0.20;

  public function __construct(
    string $immatriculation,
    string $marque,
    string $modele,
    float $tarifJournalier,
    private int $autonomie
  ) {
    // Vérifié avant parent::__construct() pour ne pas compter un véhicule invalide
    if ($autonomie <= 0) {
      throw new InvalidArgumentException("L'autonomie doit être strictement supérieure à 0.");
    }

    parent::__construct($immatriculation, $marque, $modele, $tarifJournalier);
  }

  public function getAutonomie(): int
  {
    return $this->autonomie;
  }

  public function getType(): string
  {
    return 'Vélo électrique';
  }

  public function calculerPrixHT(int $jours): float
  {
    $prix = $this->tarifJournalier * $jours;

    if ($jours >= self::JOURS_MIN_REDUCTION) {
      $prix *= 1 - self::TAUX_REDUCTION;
    }

    return round($prix, 2);
  }
}
