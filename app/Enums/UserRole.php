<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMINISTRADOR = 1;
    case MODERADOR = 2;
    case VENDEDOR_ASOCIADO = 3;
    case VENDEDOR_AFILIADO = 4;
    case USUARIO = 5;

    public function title(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'administrador',
            self::MODERADOR => 'moderador',
            self::VENDEDOR_ASOCIADO => 'vendedor asociado',
            self::VENDEDOR_AFILIADO => 'vendedor afiliado',
            self::USUARIO => 'usuario',
        };
    }
}
