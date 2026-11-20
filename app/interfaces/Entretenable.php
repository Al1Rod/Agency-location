<?php
declare(strict_types=1);

interface Entretenable
{
  public function ajouterKilometres(int $km): self;
  public function besoinEntretien(): bool;
  public function entretenir(): void;
}
