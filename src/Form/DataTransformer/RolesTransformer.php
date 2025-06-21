<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RolesTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        // Convertir le tableau de rôles en une chaîne unique
        if (empty($value)) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Les rôles doivent être un tableau');
        }

        return $value[0]; // On prend le premier rôle
    }

    public function reverseTransform(mixed $value): mixed
    {
        // Convertir la chaîne de rôle en tableau
        if (!$value) {
            return ['ROLE_CITOYEN']; // Rôle par défaut
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Le rôle doit être une chaîne de caractères');
        }

        return [$value];
    }
}
