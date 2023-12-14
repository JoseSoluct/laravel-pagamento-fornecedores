<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240;

interface TrailerLote
{
    public function getLoteServico(): int;

    public function getQtdRegistroLote(): int;

    public function getTipoRegistro(): int;

    public function getValorTotalTitulos(): float;

    public function toArray(): array;
}
