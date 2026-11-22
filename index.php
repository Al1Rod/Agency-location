<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/app/interfaces/Louable.php';
require_once __DIR__ . '/app/interfaces/Entretenable.php';
require_once __DIR__ . '/app/traits/HistoriqueTrait.php';
require_once __DIR__ . '/app/models/Vehicule.php';
require_once __DIR__ . '/app/models/Voiture.php';
require_once __DIR__ . '/app/models/Utilitaire.php';
require_once __DIR__ . '/app/models/VeloElectrique.php';
require_once __DIR__ . '/app/models/Client.php';
require_once __DIR__ . '/app/models/Location.php';
require_once __DIR__ . '/app/models/Agence.php';

function afficherLocation(?Location $location): string
{
  if ($location === null) {
    return '<p class="echec">Location refusée</p>';
  }

  return '<p class="succes">Location réussie : ' . htmlspecialchars((string) $location) . '</p>';
}

$agence = new Agence('BapAlCas Location');
$clio = new Voiture('AB-123-CD', 'Renault', 'Clio', 42, 5);
$voiture7Places = new Voiture('EF-456-GH', 'Peugeot', '5008', 60, 7);
$utilitaire = new Utilitaire('IJ-789-KL', 'Renault', 'Master', 70, 12);
$velo = new VeloElectrique('MN-012-OP', 'Decathlon', 'Elops 920E', 15, 80);

$agence->ajouterVehicule($clio)
  ->ajouterVehicule($voiture7Places)
  ->ajouterVehicule($utilitaire)
  ->ajouterVehicule($velo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($agence->getNom()) ?></title>
  <style>
    body { font-family: sans-serif; max-width: 900px; margin: 0 auto; padding: 1rem; }
    .parc { display: flex; flex-wrap: wrap; gap: 1rem; }
    .vehicule { border: 1px solid #ccc; border-radius: 8px; padding: 0 1rem; }
    .succes { color: green; }
    .echec { color: #c00; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($agence->getNom()) ?></h1>

  <h2>1. Création de l'agence et des véhicules</h2>
  <p>Agence « <?= htmlspecialchars($agence->getNom()) ?> » créée avec <?= count($agence->getVehicules()) ?> véhicules.</p>

  <h2>2. Nombre de véhicules créés</h2>
  <p><?= Vehicule::getNbVehicules() ?> véhicules créés.</p>

  <h2>3. Le parc</h2>
  <div class="parc">
    <?php foreach ($agence->getVehicules() as $vehicule) {
      echo $vehicule;
    } ?>
  </div>

  <h2>4. Les clients</h2>
  <?php
  $jean = new Client('Dupont', 'Jean', 'jean.dupont@example.com', '0601020304');
  $marie = new Client('Martin', 'Marie', 'marie.martin@example.com');
  echo '<p>Client créé : ' . htmlspecialchars((string) $jean) . '</p>';
  echo '<p>Client créé : ' . htmlspecialchars((string) $marie) . '</p>';

  try {
    new Client('Durand', 'Paul', 'paul.durand@invalide');
  } catch (InvalidArgumentException $e) {
    echo '<p class="echec">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
  }
  ?>

  <h2>5. Premières locations</h2>
  <p>Voiture 7 places, 3 jours :</p>
  <?= afficherLocation($agence->louerVehicule($jean, $voiture7Places->getImmatriculation(), 3)) ?>
  <p>Vélo électrique, 8 jours :</p>
  <?= afficherLocation($agence->louerVehicule($marie, $velo->getImmatriculation(), 8)) ?>
  <p>Nouvelle tentative sur la voiture 7 places, déjà louée :</p>
  <?= afficherLocation($agence->louerVehicule($marie, $voiture7Places->getImmatriculation(), 2)) ?>

  <h2>6. Retour avec 16 000 km</h2>
  <?php $agence->rendreVehicule($voiture7Places->getImmatriculation(), 16000); ?>
  <p>Voiture rendue avec <?= $voiture7Places->getKmDepuisEntretien() ?> km depuis le dernier entretien.</p>
  <p>Nouvelle tentative de location, entretien nécessaire :</p>
  <?= afficherLocation($agence->louerVehicule($marie, $voiture7Places->getImmatriculation(), 2)) ?>

  <h2>7. Entretien puis nouvelle location</h2>
  <?php $voiture7Places->entretenir(); ?>
  <p>Entretien effectué : <?= $voiture7Places->getKmDepuisEntretien() ?> km depuis le dernier entretien.</p>
  <?= afficherLocation($agence->louerVehicule($marie, $voiture7Places->getImmatriculation(), 2)) ?>

  <h2>8. Bilan</h2>
  <h3>Locations</h3>
  <ul>
    <?php foreach ($agence->getLocations() as $location) { ?>
      <li><?= htmlspecialchars((string) $location) ?></li>
    <?php } ?>
  </ul>
  <h3>Chiffre d'affaires</h3>
  <p><?= number_format($agence->getChiffreAffaires(), 2, '.', '') ?> € TTC</p>
  <h3>Historique de la voiture 7 places</h3>
  <?= $voiture7Places->afficherHistorique() ?>

  <h2>9. Sauvegarde</h2>
  <?php
  $fichier = __DIR__ . '/data/agence.txt';
  $agence->sauvegarder($fichier);
  $agenceChargee = Agence::charger($fichier);
  ?>
  <p>Agence sauvegardée dans data/agence.txt.</p>
  <p>Vérification : l'agence rechargée contient <?= count($agenceChargee->getVehicules()) ?> véhicules
    et <?= count($agenceChargee->getLocations()) ?> locations.</p>
</body>
</html>
