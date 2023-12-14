<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240;

interface Trailer
{
    public function getTipoRegistro(): ?int;

    public function getNumeroLote(): ?int;

    public function getQtdLotesArquivo(): ?int;

    public function getQtdRegistroArquivo(): ?int;

    public function toArray(): array;
}
