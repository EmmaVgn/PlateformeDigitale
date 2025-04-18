<?php

namespace App\Service;

use App\Repository\FormationRepository;

class FormationService
{
    public function __construct(private FormationRepository $formationRepository) {}

    public function getFormationsByType(string $type): array
    {
        return $this->formationRepository->findBy(['type' => $type]);
    }
}