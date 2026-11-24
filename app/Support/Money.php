<?php

namespace App\Support;

final class Money
{
    /** Formata centavos como reais: 129990 -> "R$ 1.299,90". */
    public static function format(int $cents): string
    {
        return 'R$ '.number_format($cents / 100, 2, ',', '.');
    }

    /**
     * Converte um preço digitado em centavos, sem passar por float.
     * Aceita: "89", "89,9", "89,90", "89.90", "1.299", "1.299,90", "R$ 1.299,90".
     * Retorna null para qualquer outro formato (ex.: "1.234.56", "12,345").
     */
    public static function parse(string $value): ?int
    {
        $value = trim(preg_replace('/^R\$\s*/u', '', trim($value)));

        $patterns = [
            '/^(\d{1,7})$/',                                   // 89
            '/^(\d{1,7})[.,](\d{1,2})$/',                      // 89,90 ou 89.90
            '/^(\d{1,3}(?:\.\d{3})+)$/',                       // 1.299
            '/^(\d{1,3}(?:\.\d{3})+),(\d{1,2})$/',             // 1.299,90
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value, $m)) {
                $reais = (int) str_replace('.', '', $m[1]);
                $centavos = isset($m[2]) ? (int) str_pad($m[2], 2, '0') : 0;

                return $reais * 100 + $centavos;
            }
        }

        return null;
    }
}
