<?php

if (! function_exists('old_text')) {
    /**
     * Valor antigo de um campo de texto para reexibir no formulário.
     * Se o envio trouxe uma lista (ex.: "nome[]=x"), usa o padrão em vez de quebrar o escape do Blade.
     */
    function old_text(string $key, mixed $default = ''): string
    {
        $value = old($key, $default);

        if (is_array($value) || is_object($value)) {
            $value = $default;
        }

        return is_scalar($value) ? (string) $value : '';
    }
}
