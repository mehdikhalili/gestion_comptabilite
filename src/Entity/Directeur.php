<?php

namespace App\Entity;

use App\Repository\DirecteurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DirecteurRepository::class)
 */
class Directeur extends User
{
    
}
