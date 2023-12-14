<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240;

interface HeaderLote
{
    public function getTipoRegistro(): string;

    public function getTipoOperacao(): string;

    public function getTipoServico(): string;

    public function getVersaoLayoutLote(): string;

    public function getCodBanco(): string;

    public function getTipoInscricao(): string;

    public function getNumeroInscricao(): string;

    public function getLoteServico(): string;

    public function getConvenio(): string;

    public function getNomeEmpresa(): string;

    public function getAgencia(): string;

    public function getAgenciaDv(): string;

    public function getConta(): string;

    public function getContaDv(): string;

    public function toArray(): array;
}
