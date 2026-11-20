<?php
declare(strict_types=1);

trait HistoriqueTrait
{
  private array $historique = [];

  protected function ajouterHistorique(string $message): void
  {
    // static::class plutôt que __CLASS__ : dans le trait, __CLASS__ donnerait "Vehicule" au lieu de "Voiture"
    $this->historique[] = sprintf('[%s] %s : %s', date('d/m/Y H:i'), static::class, $message);
  }

  public function getHistorique(): array
  {
    return $this->historique;
  }

  public function afficherHistorique(): string
  {
    $html = '<ul>';

    foreach ($this->historique as $ligne) {
      $html .= '<li>' . htmlspecialchars($ligne) . '</li>';
    }

    return $html . '</ul>';
  }
}
