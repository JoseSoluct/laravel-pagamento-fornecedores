<?php

namespace RedeCauzzoMais\Pagamento\Contracts;

use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;

interface Conta
{
    public function getBanco(): string;

    public function getBancoNome(): string;

    public function getAgencia(): string;

    public function getAgenciaDv(): string;

    public function getConta(): string;

    public function getContaDv(): string;

    public function getPessoa(): PessoaContract;
}
