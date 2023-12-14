<?php

namespace RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi;

use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\AbstractPagamento;
use Exception;

class Pix extends AbstractPagamento implements PagamentoContract
{
    const CHAVE_TELEFONE        = '01';
    const CHAVE_EMAIL           = '02';
    const CHAVE_CPF             = '03';
    const CHAVE_CNPJ            = '03';
    const CHAVE_ALEATORIA       = '04';
    const CHAVE_DADOS_BANCARIOS = '05';

    const TIPO_CONTA_CORRENTE  = '01';
    const TIPO_CONTA_PAGAMENTO = '02';
    const TIPO_CONTA_POUPANCA  = '03';

    protected ?string $pixTipo   = null;
    protected ?string $pixChave  = null;
    protected string  $tipoConta = self::TIPO_CONTA_CORRENTE;

    protected array $defaultRequiredFields = [];

    public function __construct( array $params = [] )
    {
        $this->defaultRequiredFields = $this->getRequiredFields();

        parent::__construct( $params );
    }

    public function setPixTipo( $pixTipo ): static
    {
        $this->pixTipo = $pixTipo;

        if ( $this->pixTipo <> self::CHAVE_DADOS_BANCARIOS ) {
            $this->setRequiredFields( [...$this->defaultRequiredFields, 'pixTipo', 'pixChave'] );
        } else {
            $this->setRequiredFields( [
                ...$this->defaultRequiredFields,
                'pixTipo',
                'tipoConta',
                'banco',
                'agencia',
                'conta',
                'contaDv'
            ] );
        }

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

    /**
     * @throws Exception
     */
    function setTipoConta( string $tipoConta ): static
    {
        if ( !in_array( $tipoConta, [
            self::TIPO_CONTA_CORRENTE,
            self::TIPO_CONTA_PAGAMENTO,
            self::TIPO_CONTA_POUPANCA
        ] ) ) {
            throw new Exception( "Tipo de conta inválido" );
        }

        $this->tipoConta = $tipoConta;

        return $this;
    }

    function getTipoConta(): string
    {
        return $this->tipoConta;
    }
}
