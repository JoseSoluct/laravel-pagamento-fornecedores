<?php

namespace RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi;

use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\AbstractPagamento;

class Transfer extends AbstractPagamento implements PagamentoContract
{
    public function __construct( array $params = [] )
    {
        parent::__construct( $params );

        $this->addRequiredField( ['banco', 'agencia', 'conta', 'contaDv'] );
    }
}
