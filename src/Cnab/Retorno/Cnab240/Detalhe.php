<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Detalhe as DetalheContract;
use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;
use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;
use RedeCauzzoMais\Pagamento\Traits\MagicTrait;
use RedeCauzzoMais\Pagamento\Util;
use Carbon\Carbon;
use Throwable;

class Detalhe implements DetalheContract
{
    use MagicTrait;

    protected string  $loteServico;
    protected string  $tipoRegistro;
    protected string  $segmento;
    protected int     $formaLancamento;
    protected string  $ocorrencia;
    protected int     $ocorrenciaTipo;
    protected string  $ocorrenciaDescricao;
    protected string  $numeroDocumento;
    protected string  $nossoNumero;
    protected ?Carbon $dataOcorrencia = null;
    protected ?Carbon $dataPagamento  = null;
    protected string  $valorDocumento;
    protected string  $valorPagamento;

    protected ?string $pixTipo  = null;
    protected ?string $pixChave = null;

    protected ?string $autenticacao = null;

    protected ?PessoaContract $pagador         = null;
    protected ?PessoaContract $favorecido      = null;
    protected ?ContaContract  $contaFavorecido = null;
    protected ?ContaContract  $contaPagador    = null;
    protected ?string         $error           = null;

    public function getOcorrencia(): string
    {
        return $this->ocorrencia;
    }

    public function hasOcorrencia(): bool
    {
        $ocorrencias = func_get_args();

        if ( count( $ocorrencias ) == 0 and !empty( $this->getOcorrencia() ) ) {
            return true;
        }

        if ( count( $ocorrencias ) == 1 and is_array( func_get_arg( 0 ) ) ) {
            $ocorrencias = func_get_arg( 0 );
        }

        if ( in_array( $this->getOcorrencia(), $ocorrencias ) ) {
            return true;
        }

        return false;
    }

    public function setOcorrencia( $ocorrencia ): static
    {
        $this->ocorrencia = $ocorrencia;

        return $this;
    }

    public function getOcorrenciaTipo(): int
    {
        return $this->ocorrenciaTipo;
    }

    public function setOcorrenciaTipo( $ocorrenciaTipo ): static
    {
        $this->ocorrenciaTipo = $ocorrenciaTipo;

        return $this;
    }

    public function getOcorrenciaDescricao(): string
    {
        return $this->ocorrenciaDescricao;
    }

    public function setOcorrenciaDescricao( $ocorrenciaDescricao ): static
    {
        $this->ocorrenciaDescricao = $ocorrenciaDescricao;

        return $this;
    }

    public function getNumeroDocumento(): string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento( $numeroDocumento ): static
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getNossoNumero(): string
    {
        return $this->nossoNumero;
    }

    public function setNossoNumero( $nossoNumero ): static
    {
        $this->nossoNumero = $nossoNumero;

        return $this;
    }

    public function getDataPagamento( $format = 'd/m/Y' ): ?string
    {
        if ( is_null( $this->dataPagamento ) ) {
            return null;
        }

        return $this->dataPagamento->format( $format );
    }

    public function setDataPagamento( $data, $format = 'dmY' ): static
    {
        $this->dataPagamento = trim( $data, '0 ' ) ? Carbon::createFromFormat( $format, $data ) : null;

        return $this;
    }

    public function getDataOcorrencia( $format = 'd/m/Y' ): ?string
    {
        if ( is_null( $this->dataOcorrencia ) ) {
            return null;
        }

        return $this->dataOcorrencia->format( $format );
    }

    public function setDataOcorrencia( $data, $format = 'dmY' ): static
    {
        $this->dataOcorrencia = trim( $data, '0 ' ) ? Carbon::createFromFormat( $format, $data ) : null;

        return $this;
    }

    public function getValorPagamento(): string
    {
        return $this->valorPagamento;
    }

    public function setValorPagamento( $valor ): static
    {
        $this->valorPagamento = $valor;

        return $this;
    }

    public function getValorDocumento(): string
    {
        return $this->valorDocumento;
    }

    public function setValorDocumento( $valor ): static
    {
        $this->valorDocumento = $valor;

        return $this;
    }

    public function getPagador(): ?PessoaContract
    {
        return $this->pagador;
    }

    /**
     * @throws Throwable
     */
    public function setPagador( array|PessoaContract $pagador ): static
    {
        Util::addPessoa( $this->pagador, $pagador );

        return $this;
    }

    public function getFavorecido(): ?PessoaContract
    {
        return $this->favorecido;
    }

    /**
     * @throws Throwable
     */
    public function setFavorecido( array|PessoaContract $favorecido ): static
    {
        Util::addPessoa( $this->favorecido, $favorecido );

        return $this;
    }

    public function getContaPagador(): ?ContaContract
    {
        return $this->contaPagador;
    }

    /**
     * @throws Throwable
     */
    public function setContaPagador( array|ContaContract $contaPagador ): static
    {
        Util::addConta( $this->contaPagador, $contaPagador );

        return $this;
    }

    public function getContaFavorecido(): ?ContaContract
    {
        return $this->contaFavorecido;
    }

    /**
     * @throws Throwable
     */
    public function setContaFavorecido( array|ContaContract $contaFavorecido ): static
    {
        Util::addConta( $this->contaFavorecido, $contaFavorecido );

        return $this;
    }

    public function hasError(): bool
    {
        return $this->getOcorrencia() == self::OCORRENCIA_ERRO;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError( $error ): static
    {
        $this->ocorrenciaTipo = self::OCORRENCIA_ERRO;
        $this->error          = $error;

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

    public function getTipoRegistro(): string
    {
        return $this->tipoRegistro;
    }

    public function setTipoRegistro( $tipoRegistro ): static
    {
        $this->tipoRegistro = $tipoRegistro;

        return $this;
    }

    public function getSegmento(): string
    {
        return $this->segmento;
    }

    public function setSegmento( $segmento ): static
    {
        $this->segmento = $segmento;

        return $this;
    }

    public function getFormaLancamento(): int
    {
        return $this->formaLancamento;
    }

    public function setFormaLancamento( $formaLancamento ): static
    {
        $this->formaLancamento = $formaLancamento;

        return $this;
    }


    public function setPixTipo( $pixTipo ): static
    {
        $this->pixTipo = $pixTipo;

        return $this;
    }

    public function getPixTipo(): ?string
    {
        return $this->pixTipo;
    }

    public function setPixChave( $pixChave ): static
    {
        $this->pixChave = $pixChave;

        return $this;
    }

    public function getPixChave(): ?string
    {
        return $this->pixChave;
    }

    public function setAutenticacao( $autenticacao ): static
    {
        $this->autenticacao = $autenticacao;

        return $this;
    }

    public function getAutenticacao(): ?string
    {
        return $this->autenticacao;
    }
}
