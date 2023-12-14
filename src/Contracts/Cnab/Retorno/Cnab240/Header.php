<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240;

interface Header
{
    public function getLoteServico(): string;

    public function getTipoRegistro(): string;

    public function getTipoInscricao(): string;

    public function getAgencia(): string;

    public function getAgenciaDv(): string;

    public function getNomeEmpresa(): string;

    public function getDocumentoEmpresa(): string;

    public function getNumeroSequencialArquivo(): string;

    public function getVersaoLayoutArquivo(): string;

    public function getNumeroInscricao(): string;

    public function getConta(): string;

    public function getContaDv(): string;

    public function getCodigoCedente(): string;

    public function getDataGeracao( $format = 'd/m/Y' ): ?string;

    public function getConvenio(): string;

    public function getCodBanco(): string;

    public function getCodigoRemessaRetorno(): int;

    public function getNomeBanco(): string;

    public function toArray(): array;
}
