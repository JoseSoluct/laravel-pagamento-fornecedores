<?php

namespace RedeCauzzoMais\Pagamento\Contracts\Pagamento;

use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;

interface Pagamento
{
    const COD_BANCO_BB        = '001';
    const COD_BANCO_SANTANDER = '033';
    const COD_BANCO_CEF       = '104';
    const COD_BANCO_BRADESCO  = '237';
    const COD_BANCO_ITAU      = '341';
    const COD_BANCO_HSBC      = '399';
    const COD_BANCO_SICREDI   = '748';
    const COD_BANCO_BANRISUL  = '041';
    const COD_BANCO_BANCOOB   = '756';
    const COD_BANCO_BNB       = '004';

    const TIPO_MOVIMENTO_INCLUSAO = 0;
    const TIPO_MOVIMENTO_EXCLUSAO = 9;

    public function getBanco(): ?string;

    public function getAgencia(): ?string;

    public function getAgenciaDv(): ?string;

    public function getTipoMovimento(): int;

    public function getInstrucaoMovimento(): string;

    public function getTipoMoeda(): string;

    public function getFavorecido(): ?PessoaContract;

    public function getConta(): ?string;

    public function getContaDv(): ?string;

    public function getNumeroDocumento(): ?string;

    public function getData(): ?\Carbon\Carbon;

    public function getValor(): string;


}
