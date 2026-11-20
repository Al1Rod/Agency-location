<?php
declare(strict_types=1);

abstract class Vehicule implements Louable
{
  use HistoriqueTrait;

  public const TVA = 1.20;
  private const FORMAT_IMMATRICULATION = '/^[A-Z]{2}-\d{3}-[A-Z]{2}$/';
  private static int $nbVehicules = 0;
  private string $immatriculation;
  private string $marque;
  private string $modele;
  protected float $tarifJournalier;
  private bool $disponible = true;
  public function __construct(string $immatriculation, string $marque, string $modele, float $tarifJournalier)
  {
    $this->setImmatriculation($immatriculation)
      ->setMarque($marque)
      ->setModele($modele)
      ->setTarifJournalier($tarifJournalier);

    // Compté uniquement si aucun setter n'a levé d'exception
    self::$nbVehicules++;
  }

  public static function getNbVehicules(): int
  {
    return self::$nbVehicules;
  }

  public function getImmatriculation(): string
  {
    return $this->immatriculation;
  }

  public function setImmatriculation(string $immatriculation): static
  {
    $immatriculation = strtoupper(trim($immatriculation));

    if (!preg_match(self::FORMAT_IMMATRICULATION, $immatriculation)) {
      throw new InvalidArgumentException("Immatriculation invalide : \"$immatriculation\" (format attendu : AB-123-CD).");
    }

    $this->immatriculation = $immatriculation;
    return $this;
  }

  public function getMarque(): string
  {
    return $this->marque;
  }

  public function setMarque(string $marque): static
  {
    $this->marque = trim($marque);
    return $this;
  }

  public function getModele(): string
  {
    return $this->modele;
  }

  public function setModele(string $modele): static
  {
    $this->modele = trim($modele);
    return $this;
  }

  public function getTarifJournalier(): float
  {
    return $this->tarifJournalier;
  }

  public function setTarifJournalier(float $tarifJournalier): static
  {
    if ($tarifJournalier <= 0) {
      throw new InvalidArgumentException('Le tarif journalier doit être strictement supérieur à 0.');
    }

    $this->tarifJournalier = $tarifJournalier;
    return $this;
  }

  public function estDisponible(): bool
  {
    return $this->disponible;
  }

  public function setDisponible(bool $disponible): static
  {
    $this->disponible = $disponible;
    return $this;
  }

  // final : les enfants personnalisent seulement calculerPrixHT()
  final public function louer(int $jours): float
  {
    if (!$this->disponible || $jours < 1) {
      return 0.0;
    }

    $prixTTC = round($this->calculerPrixHT($jours) * self::TVA, 2);

    $this->setDisponible(false);
    $this->ajouterHistorique(sprintf(
      'Loué %d jour(s) pour %s € TTC',
      $jours,
      number_format($prixTTC, 2, '.', '')
    ));

    return $prixTTC;
  }

  public function rendre(): void
  {
    if ($this->disponible) {
      return;
    }

    $this->setDisponible(true);
    $this->ajouterHistorique('Rendu à l\'agence');
  }

  abstract public function calculerPrixHT(int $jours): float;

  abstract public function getType(): string;

  // Les enfants peuvent compléter la carte en insérant avant </article>
  public function __toString(): string
  {
    $tarifTTC = round($this->tarifJournalier * self::TVA, 2);

    return '<article class="vehicule">'
      . '<h3>' . htmlspecialchars($this->getType()) . ' : '
      . htmlspecialchars($this->marque) . ' ' . htmlspecialchars($this->modele) . '</h3>'
      . '<p>Immatriculation : ' . htmlspecialchars($this->immatriculation) . '</p>'
      . '<p>Tarif journalier : ' . number_format($tarifTTC, 2, '.', '') . ' € TTC</p>'
      . '<p>' . ($this->disponible ? 'Disponible' : 'Indisponible') . '</p>'
      . '</article>';
  }
}
