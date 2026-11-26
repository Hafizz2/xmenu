<?php

namespace App\Http\Controllers;

use App\Models\Cafe;

abstract class Controller
{
    protected function cafe(): Cafe
    {
        return request()->attributes->get('cafe');
    }

    /** 404 unless the model belongs to the signed-in owner's café. */
    protected function own($model): void
    {
        abort_unless($model && (int) $model->cafe_id === (int) $this->cafe()->id, 404);
    }

    /** Read a {en, am} pair from fields like name_en / name_am. */
    protected function pair(array $data, string $field): array
    {
        $out = [];
        foreach (array_keys(config('xmenu.languages')) as $lang) {
            $v = trim((string) ($data["{$field}_{$lang}"] ?? ''));
            $out[$lang] = $v === '' ? null : $v;
        }

        return $out;
    }
}
