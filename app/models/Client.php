<?php
declare(strict_types=1);

class Client
{
  private string $nom;
  private string $prenom;
  private string $email;
  private ?string $telephone;

  public function __construct(string $nom, string $prenom, string $email, ?string $telephone = null)
  {
    $this->setNom($nom)
      ->setPrenom($prenom)
      ->setEmail($email)
      ->setTelephone($telephone);
  }

  public function getNom(): string
  {
    return $this->nom;
  }

  public function setNom(string $nom): static
  {
    $nom = trim($nom);

    if ($nom === '') {
      throw new InvalidArgumentException('Le nom du client est obligatoire.');
    }

    $this->nom = $nom;
    return $this;
  }

  public function getPrenom(): string
  {
    return $this->prenom;
  }

  public function setPrenom(string $prenom): static
  {
    $prenom = trim($prenom);

    if ($prenom === '') {
      throw new InvalidArgumentException('Le prénom du client est obligatoire.');
    }

    $this->prenom = $prenom;
    return $this;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $email = trim($email);

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      throw new InvalidArgumentException("Adresse email invalide : \"$email\".");
    }

    $this->email = $email;
    return $this;
  }

  public function getTelephone(): ?string
  {
    return $this->telephone;
  }

  public function setTelephone(?string $telephone): static
  {
    // Une chaîne vide est traitée comme une absence de téléphone
    $telephone = $telephone === null ? null : trim($telephone);
    $this->telephone = $telephone === '' ? null : $telephone;
    return $this;
  }

  public function getNomComplet(): string
  {
    return $this->prenom . ' ' . $this->nom;
  }

  public function __toString(): string
  {
    return $this->getNomComplet() . ' (' . $this->email . ')';
  }
}
