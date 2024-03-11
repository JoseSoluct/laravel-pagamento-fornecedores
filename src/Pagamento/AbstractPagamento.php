<?php

namespace RedeCauzzoMais\Pagamento\Pagamento;

use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;
use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Util;
use Carbon\Carbon;
use Exception;
use Throwable;

abstract class AbstractPagamento implements PagamentoContract
{
    private array $requiredFields = [
        'data',
        'valor',
        'numeroDocumento',
        'favorecido'
    ];

    protected int     $tipoMovimento      = PagamentoContract::TIPO_MOVIMENTO_INCLUSAO;
    protected ?string $instrucaoMovimento = null;
    protected ?string $banco              = null;
    protected ?string $numeroDocumento    = null;
    protected ?Carbon $data               = null;
    protected ?string $tipoMoeda          = 'BRL';
    protected float   $valor              = 0.0;
    protected ?string $agencia            = null;
    protected ?string $agenciaDv          = null;
    protected ?string $conta              = null;
    protected ?string $contaDv            = null;

    protected ?PessoaContract $favorecido = null;

    public function __construct( array $params = [] )
    {
        Util::fillClass( $this, $params );

        if ( empty( $this->getData() ) ) {
            $this->setData( new Carbon() );
        }
    }

    public function getBanco(): ?string
    {
        return $this->banco;
    }

    public function setBanco( $banco ): static
    {
        $this->banco = $banco;

        return $this;
    }

    public function getAgencia(): ?string
    {
        return $this->agencia;
    }

    public function setAgencia( $agencia ): static
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    protected function setRequiredFields(): static
    {
        $args = func_get_args();

        $this->requiredFields = [];
        foreach ( $args as $arg ) {
            $this->addRequiredField( $arg );
        }

        return $this;
    }

    protected function addRequiredField(): static
    {
        $args = func_get_args();
        foreach ( $args as $arg ) {
            !is_array( $arg ) or call_user_func_array( [$this, __FUNCTION__], $arg );
            !is_string( $arg ) or array_push( $this->requiredFields, $arg );
        }

        return $this;
    }

    public function setAgenciaDv( string $agenciaDv ): static
    {
        $this->agenciaDv = $agenciaDv;

        return $this;
    }

    public function getAgenciaDv(): ?string
    {
        return $this->agenciaDv;
    }

    public function getTipoMovimento(): int
    {
        return $this->tipoMovimento;
    }

    public function setTipoMovimento( int $tipoMovimento ): static
    {
        $this->tipoMovimento = $tipoMovimento;

        return $this;
    }

    public function getInstrucaoMovimento(): string
    {
        return $this->instrucaoMovimento;
    }

    public function setInstrucaoMovimento( $instrucaoMovimento ): static
    {
        $this->instrucaoMovimento = $instrucaoMovimento;

        return $this;
    }

    public function getTipoMoeda(): string
    {
        return $this->tipoMoeda;
    }

    public function setTipoMoeda( string $tipoMoeda ): static
    {
        $this->tipoMoeda = $tipoMoeda;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function setFavorecido( PessoaContract $favorecido ): static
    {
        Util::addPessoa( $this->favorecido, $favorecido );

        return $this;
    }

    public function getFavorecido(): ?PessoaContract
    {
        return $this->favorecido;
    }

    public function setConta( string $conta ): static
    {
        $this->conta = $conta;

        return $this;
    }

    public function getConta(): ?string
    {
        return $this->conta;
    }

    public function setContaDv( string $contaDv ): static
    {
        $this->contaDv = $contaDv;

        return $this;
    }

    public function getContaDv(): ?string
    {
        return $this->contaDv;
    }


    public function setNumeroDocumento( int $numeroDocumento ): static
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setData( Carbon $data ): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?Carbon
    {
        return $this->data;
    }

    public function setValor( string $valor ): static
    {
        $this->valor = Util::toFloat( $valor, 2, false );

        return $this;
    }

    public function getValor(): string
    {
        return Util::toFloat( $this->valor, 2, false );
    }

    /**
     * @throws Throwable
     */
    final public function setNossoNumero(): void
    {
        throw new Exception( 'Não é possível definir o nosso número diretamente. Utilize o método setNumero.' );
    }

    public function isValid( &$messages ): bool
    {
        $bool = true;

        foreach ( $this->requiredFields as $field ) {
            if ( is_null( call_user_func( [$this, 'get' . ucwords( $field )] ) ) ) {
                $messages[] = "Campo {$field} é obrigatório" . PHP_EOL;

                $bool = false;
            }
        }

        return $bool;
    }
}
