<?php

namespace RedeCauzzoMais\Pagamento\Pagamento\Banco;

use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Pagamento\AbstractPagamento as AbstractPagamentoGeneric;

class AbstractPagamento extends AbstractPagamentoGeneric
{
    const INCLUSAO_REGISTRO_LIBERADO = '00';
    const EXCLUSAO_REGISTRO_INCLUIDO = '99';

    public function getInstrucaoMovimento(): string
    {
        if ( $this->getTipoMovimento() === PagamentoContract::TIPO_MOVIMENTO_EXCLUSAO ) {
            return AbstractPagamento::EXCLUSAO_REGISTRO_INCLUIDO;
        }

        return AbstractPagamento::INCLUSAO_REGISTRO_LIBERADO;
    }
}
