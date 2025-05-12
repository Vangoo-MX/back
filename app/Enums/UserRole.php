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
            self::ADMINISTRADOR => 'Administrador',
            self::MODERADOR => 'Moderador',
            self::VENDEDOR_ASOCIADO => 'Vendedor asociado',
            self::VENDEDOR_AFILIADO => 'Vendedor afiliado',
            self::USUARIO => 'Usuario',
        };
    }
}
