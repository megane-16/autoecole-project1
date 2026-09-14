<?php

namespace App\Enums;

enum UserRole: string
{
    case Apprenant = 'apprenant';
    case Moniteur = 'moniteur';
    case AutoEcole = 'auto_ecole';
    case Administrateur = 'administrateur';

    public function label(): string
    {
        return match ($this) {
            self::Apprenant => 'Apprenant',
            self::Moniteur => 'Moniteur',
            self::AutoEcole => 'Auto-école',
            self::Administrateur => 'Administrateur',
        };
    }

    public function isMobile(): bool
    {
        return in_array($this, [self::Apprenant, self::Moniteur], true);
    }

    public function isWeb(): bool
    {
        return in_array($this, [self::AutoEcole, self::Administrateur], true);
    }
}
