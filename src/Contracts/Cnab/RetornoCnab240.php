<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab;

use Illuminate\Support\Collection;

interface RetornoCnab240 extends Cnab
{
    public function getCodigoBanco();

    public function getBancoNome();

    public function getDetalhes(): Collection;

    public function getDetalhe( int $i ): ?Retorno\Cnab240\Detalhe;

    public function getHeader(): Retorno\Cnab240\Header;

    public function getHeaderLote( int $lote ): Retorno\Cnab240\HeaderLote;

    public function getTrailerLote( int $lote ): Retorno\Cnab240\TrailerLote;

    public function getHeaderLotes(): array;

    public function getTrailerLotes(): array;

    public function getTrailer(): Retorno\Cnab240\Trailer;

    public function processar();

    public function toArray(): array;
}
