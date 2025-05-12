<?php

function limitarTexto($texto, $limite) {
    if (mb_strlen($texto, 'UTF-8') > $limite) {
        return mb_substr($texto, 0, $limite, 'UTF-8') . '...';
    }

    return $texto;
}