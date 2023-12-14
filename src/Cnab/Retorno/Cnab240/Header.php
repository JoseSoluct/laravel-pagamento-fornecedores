<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Header as HeaderContract;
use RedeCauzzoMais\Pagamento\Traits\MagicTrait;
use Carbon\Carbon;

class Header implements HeaderContract
{
    use MagicTrait;

    protected string  $codBanco;
    protected string  $loteServico;
    protected string  $tipoRegistro;
    protected string  $tipoInscricao;
    protected string  $numeroInscricao;
    protected string  $convenio;
    protected string  $codigoCedente;
    protected string  $agencia;
    protected string  $agenciaDv;
    protected string  $conta;
    protected string  $contaDv;
    protected string  $nomeEmpresa;
    protected string  $nomeBanco;
    protected string  $codigoRemessaRetorno;
    protected ?Carbon $dataGeracao;
    protected int     $numeroSequencialArquivo;
    protected string  $versaoLayoutArquivo;

    public function getLoteServico(): string
    {
        return $this->loteServico;
    }

    public function setLoteServico( $loteServico ): static
    {
        $this->loteServico = $loteServico;

        return $this;
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

    public function getTipoInscricao(): string
    {
        return $this->tipoInscricao;
    }

    public function setTipoInscricao( $tipoInscricao ): static
    {
        $this->tipoInscricao = $tipoInscricao;

        return $this;
    }

    public function getAgencia(): string
    {
        return $this->agencia;
    }

    public function setAgencia( $agencia ): static
    {
        $this->agencia = ltrim( trim( $agencia, ' ' ), '0' );

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

    public function getNomeEmpresa(): string
    {
        return $this->nomeEmpresa;
    }

    public function setNomeEmpresa( $nomeEmpresa ): static
    {
        $this->nomeEmpresa = $nomeEmpresa;

        return $this;
    }

    public function getDocumentoEmpresa(): string
    {
        return $this->numeroInscricao;
    }

    public function setDocumentoEmpresa( $documentoEmpresa ): static
    {
        $this->numeroInscricao = $documentoEmpresa;

        return $this;
    }

    public function getNumeroSequencialArquivo(): string
    {
        return $this->numeroSequencialArquivo;
    }

    public function setNumeroSequencialArquivo( $numeroSequencialArquivo ): static
    {
        $this->numeroSequencialArquivo = $numeroSequencialArquivo;

        return $this;
    }

    public function getVersaoLayoutArquivo(): string
    {
        return $this->versaoLayoutArquivo;
    }

    public function setVersaoLayoutArquivo( $versaoLayoutArquivo ): static
    {
        $this->versaoLayoutArquivo = $versaoLayoutArquivo;

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

    public function getConta(): string
    {
        return $this->conta;
    }

    public function setConta( $conta ): static
    {
        $this->conta = ltrim( trim( $conta, ' ' ), '0' );

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

    public function getCodigoCedente(): string
    {
        return $this->codigoCedente;
    }

    public function setCodigoCedente( $codigoCedente ): static
    {
        $this->codigoCedente = $codigoCedente;

        return $this;
    }

    public function getDataGeracao( $format = 'd/m/Y' ): ?string
    {
        if ( is_null( $this->dataGeracao ) ) {
            return null;
        }

        return $this->dataGeracao->format( $format );
    }

    public function setDataGeracao( $data, $format = 'dmY' ): static
    {
        $this->dataGeracao = trim( $data, '0 ' ) ? Carbon::createFromFormat( $format, $data ) : null;

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

    public function getCodBanco(): string
    {
        return $this->codBanco;
    }

    public function setCodBanco( $codBanco ): static
    {
        $this->codBanco = $codBanco;

        return $this;
    }

    public function getCodigoRemessaRetorno(): int
    {
        return $this->codigoRemessaRetorno;
    }

    public function setCodigoRemessaRetorno( $codigoRemessaRetorno ): static
    {
        $this->codigoRemessaRetorno = $codigoRemessaRetorno;

        return $this;
    }

    public function getNomeBanco(): string
    {
        return $this->nomeBanco;
    }

    public function setNomeBanco( $nomeBanco ): static
    {
        $this->nomeBanco = $nomeBanco;

        return $this;
    }
}
