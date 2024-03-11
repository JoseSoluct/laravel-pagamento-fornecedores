<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno;

use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;
use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;

interface Detalhe
{
    const OCORRENCIA_LIQUIDADA = 1;
    const OCORRENCIA_BAIXADA   = 2;
    const OCORRENCIA_ENTRADA   = 3;
    const OCORRENCIA_ALTERACAO = 4;
    const OCORRENCIA_OUTROS    = 6;
    const OCORRENCIA_ERRO      = 9;

    public function getFavorecido(): ?PessoaContract;

    public function getPagador(): ?PessoaContract;

    public function getNossoNumero();

    public function getNumeroDocumento();

    public function getOcorrencia();

    public function getOcorrenciaDescricao();

    public function getOcorrenciaTipo();

    public function getDataOcorrencia( string $format = 'd/m/Y' );

    public function getDataPagamento( string $format = 'd/m/Y' );

    public function getValorDocumento();

    public function getValorPagamento();

    public function getPixTipo(): ?string;

    public function getPixChave(): ?string;

    public function getAutenticacao(): ?string;

    public function getContaFavorecido(): ?ContaContract;

    public function getContaPagador(): ?ContaContract;

    public function getError();

    public function hasError(): bool;

    public function hasOcorrencia(): bool;

    public function toArray(): array;
}
