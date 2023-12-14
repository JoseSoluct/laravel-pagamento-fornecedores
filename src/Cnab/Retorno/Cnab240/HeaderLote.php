<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\HeaderLote as HeaderLoteContract;
use RedeCauzzoMais\Pagamento\Traits\MagicTrait;

class HeaderLote implements HeaderLoteContract
{
    use MagicTrait;

    protected string $codBanco;
    protected string $loteServico;
    protected string $tipoRegistro;
    protected string $tipoOperacao;
    protected string $tipoServico;
    protected int    $formaLancamento;
    protected string $versaoLayoutLote;
    protected string $tipoInscricao;
    protected string $numeroInscricao;
    protected string $convenio;
    protected string $agencia;
    protected string $agenciaDv;
    protected string $conta;
    protected string $contaDv;
    protected string $nomeEmpresa;

    public function __construct( $formaLancamento )
    {
        $this->formaLancamento = $formaLancamento;
    }

    public function getTipoRegistro(): string
    {
        return $this->tipoRegistro;
    }

    public function setTipoRegistro( $tipoRegistro ): static
    {
        $this->tipoRegistro = $tipoRegistro;

        return $this;
    }

    public function getCodBanco(): string
    {
        return $this->codBanco;
    }

    public function setCodBanco( $codBanco ): static
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    public function getLoteServico(): string
    {
        return $this->loteServico;
    }

    public function setLoteServico( $loteServico ): static
    {
        $this->loteServico = $loteServico;

        return $this;
    }

    public function getTipoOperacao(): string
    {
        return $this->tipoOperacao;
    }

    public function setTipoOperacao( $tipoOperacao ): static
    {
        $this->tipoOperacao = $tipoOperacao;

        return $this;
    }

    public function getTipoServico(): string
    {
        return $this->tipoServico;
    }

    public function setTipoServico( $tipoServico ): static
    {
        $this->tipoServico = $tipoServico;

        return $this;
    }

    public function getVersaoLayoutLote(): string
    {
        return $this->versaoLayoutLote;
    }

    public function setVersaoLayoutLote( $versaoLayoutLote ): static
    {
        $this->versaoLayoutLote = $versaoLayoutLote;

        return $this;
    }

    public function getTipoInscricao(): string
    {
        return $this->tipoInscricao;
    }

    public function setTipoInscricao( $tipoInscricao ): static
    {
        $this->tipoInscricao = $tipoInscricao;

        return $this;
    }

    public function getNumeroInscricao(): string
    {
        return $this->numeroInscricao;
    }

    public function setNumeroInscricao( $numeroInscricao ): static
    {
        $this->numeroInscricao = $numeroInscricao;

        return $this;
    }

    public function getConvenio(): string
    {
        return $this->convenio;
    }

    public function setConvenio( $convenio ): static
    {
        $this->convenio = $convenio;

        return $this;
    }

    public function getNomeEmpresa(): string
    {
        return $this->nomeEmpresa;
    }

    public function setNomeEmpresa( $nomeEmpresa ): static
    {
        $this->nomeEmpresa = $nomeEmpresa;

        return $this;
    }

    public function getAgencia(): string
    {
        return $this->agencia;
    }

    public function setAgencia( $agencia ): static
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getAgenciaDv(): string
    {
        return $this->agenciaDv;
    }

    public function setAgenciaDv( $agenciaDv ): static
    {
        $this->agenciaDv = $agenciaDv;

        return $this;
    }

    public function getConta(): string
    {
        return $this->conta;
    }

    public function setConta( $conta ): static
    {
        $this->conta = $conta;

        return $this;
    }

    public function getContaDv(): string
    {
        return $this->contaDv;
    }

    public function setContaDv( $contaDv ): static
    {
        $this->contaDv = $contaDv;

        return $this;
    }

    public function getFormaLancamento(): int
    {
        return $this->formaLancamento;
    }
}
