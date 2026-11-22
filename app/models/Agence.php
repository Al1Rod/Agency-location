<?php
declare(strict_types=1);

class Agence
{
  private string $nom;
  // Indexé par immatriculation pour retrouver un véhicule directement
  private array $vehicules = [];
  private array $locations = [];

  public function __construct(string $nom)
  {
    $this->setNom($nom);
  }

  public function getNom(): string
  {
    return $this->nom;
  }

  public function setNom(string $nom): static
  {
    $nom = trim($nom);

    if ($nom === '') {
      throw new InvalidArgumentException("Le nom de l'agence est obligatoire.");
    }

    $this->nom = $nom;
    return $this;
  }

  public function getVehicules(): array
  {
    return array_values($this->vehicules);
  }

  public function getLocations(): array
  {
    return $this->locations;
  }

  public function getVehicule(string $immatriculation): ?Vehicule
  {
    return $this->vehicules[strtoupper(trim($immatriculation))] ?? null;
  }

  public function ajouterVehicule(Vehicule $vehicule): static
  {
    $immatriculation = $vehicule->getImmatriculation();

    if (isset($this->vehicules[$immatriculation])) {
      throw new InvalidArgumentException("Le véhicule $immatriculation est déjà dans l'agence.");
    }

    $this->vehicules[$immatriculation] = $vehicule;
    return $this;
  }

  public function getVehiculesDisponibles(): array
  {
    return array_values(array_filter(
      $this->vehicules,
      fn(Vehicule $vehicule): bool => $vehicule->estDisponible()
    ));
  }

  public function louerVehicule(Client $client, string $immatriculation, int $jours): ?Location
  {
    $vehicule = $this->getVehicule($immatriculation);

    if ($vehicule === null || !$vehicule->estDisponible() || $jours < 1) {
      return null;
    }

    if ($vehicule instanceof Entretenable && $vehicule->besoinEntretien()) {
      return null;
    }

    $location = new Location($client, $vehicule, $jours, $vehicule->louer($jours));
    $this->locations[] = $location;

    return $location;
  }

  public function rendreVehicule(string $immatriculation, int $kmParcourus = 0): void
  {
    $vehicule = $this->getVehicule($immatriculation);

    if ($vehicule === null) {
      throw new InvalidArgumentException("Aucun véhicule immatriculé $immatriculation dans l'agence.");
    }

    $vehicule->rendre();

    if ($vehicule instanceof Entretenable) {
      $vehicule->ajouterKilometres($kmParcourus);
    }
  }

  public function getChiffreAffaires(): float
  {
    $total = 0.0;

    foreach ($this->locations as $location) {
      $total += $location->getMontantTTC();
    }

    return round($total, 2);
  }

  public function __serialize(): array
  {
    return [
      'nom' => $this->nom,
      'vehicules' => $this->vehicules,
      'locations' => $this->locations,
    ];
  }

  public function __unserialize(array $data): void
  {
    $this->nom = $data['nom'];
    $this->vehicules = $data['vehicules'];
    $this->locations = $data['locations'];
  }

  public function sauvegarder(string $fichier): void
  {
    $dossier = dirname($fichier);

    if (!is_dir($dossier)) {
      mkdir($dossier, 0777, true);
    }

    if (file_put_contents($fichier, serialize($this)) === false) {
      throw new RuntimeException("Impossible d'écrire dans le fichier $fichier.");
    }
  }

  public static function charger(string $fichier): Agence
  {
    $contenu = is_file($fichier) ? file_get_contents($fichier) : false;

    if ($contenu === false) {
      throw new RuntimeException("Impossible de lire le fichier $fichier.");
    }

    $agence = unserialize($contenu);

    if (!$agence instanceof Agence) {
      throw new RuntimeException("Le fichier $fichier ne contient pas une agence valide.");
    }

    return $agence;
  }
}
