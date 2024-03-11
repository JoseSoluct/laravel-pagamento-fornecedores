<?php

namespace RedeCauzzoMais\Pagamento\Comprovante\Banco\Sicredi;

use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\AbstractRetorno;
use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Detalhe;
use InvalidArgumentException;
use Throwable;

class Factory
{
    /**
     * @throws Throwable
     */
    public static function make( Detalhe $d, array $extra = [] ): string
    {
        match ( $d->getFormaLancamento() ) {
            AbstractRetorno::LOTE_PIX => $class = Pix::class,
            AbstractRetorno::LOTE_TED => $class = Ted::class,
            AbstractRetorno::LOTE_TRANSFER => $class = Transfer::class,
            default => throw new InvalidArgumentException( "Forma de lançamento não suportada: {$d->getFormaLancamento()}" )
        };

        return $class::make( $d, $extra );
    }
}

