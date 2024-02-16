<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Comprovante;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Detalhe as DetalheContract;

interface Comprovante
{
    public static function make( DetalheContract $d, array $extra = [] ): string;
}
