<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Novo = 'novo';
    case Confirmado = 'confirmado';
    case Pronto = 'pronto';
    case Entregue = 'entregue';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Novo => 'Novo',
            self::Confirmado => 'Confirmado',
            self::Pronto => 'Pronto para retirada',
            self::Entregue => 'Entregue',
            self::Cancelado => 'Cancelado',
        };
    }

    /** Classes Tailwind do selo de status. */
    public function badge(): string
    {
        return match ($this) {
            self::Novo => 'bg-amber-100 text-amber-800',
            self::Confirmado => 'bg-sky-100 text-sky-800',
            self::Pronto => 'bg-emerald-100 text-emerald-800',
            self::Entregue => 'bg-stone-200 text-stone-700',
            self::Cancelado => 'bg-rose-100 text-rose-800',
        };
    }
}
