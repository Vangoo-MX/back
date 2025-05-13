<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMINISTRADOR = 1;
    case MODERADOR = 2;
    case DESARROLLADOR = 3;
    case VENDEDOR_ASOCIADO = 4;
    case VENDEDOR_AFILIADO = 5;
    case USUARIO = 6;

    public function title(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::MODERADOR => 'Moderador',
            self::DESARROLLADOR => 'Desarrollador',
            self::VENDEDOR_ASOCIADO => 'Vendedor asociado',
            self::VENDEDOR_AFILIADO => 'Vendedor afiliado',
            self::USUARIO => 'Usuario',
        };
    }
}
