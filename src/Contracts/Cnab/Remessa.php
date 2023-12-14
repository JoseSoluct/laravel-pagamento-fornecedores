<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab;

interface Remessa extends Cnab
{
    public function gerar();
}
