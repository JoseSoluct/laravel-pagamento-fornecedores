<?php

namespace RedeCauzzoMais\Pagamento;

use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;
use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;

class Conta implements ContaContract
{
    protected string $banco;
    protected string $bancoNome = '';
    protected string $agencia;
    protected string $agenciaDv;
    protected string $conta;
    protected string $contaDv;

    protected PessoaContract $pessoa;

    public function __construct( $params = [] )
    {
        if ( isset( $params['pessoa'] ) and !( $params['pessoa'] instanceof Pessoa ) ) {
            $params['pessoa'] = new Pessoa( $params['pessoa'] );
        }

        Util::fillClass( $this, $params );
    }

    public static function create( $banco, $agencia, $agenciaDv, $conta, $contaDv, $pessoa ): static
    {
        return new static( [
            'banco'     => $banco,
            'agencia'   => $agencia,
            'agenciaDv' => $agenciaDv,
            'conta'     => $conta,
            'contaDv'   => $contaDv,
            'pessoa'    => $pessoa,
        ] );
    }

    public function getBanco(): string
    {
        return $this->banco;
    }

    public function setBanco( $banco ): Conta
    {
        $this->banco = $banco;

        $this->setBancoNome( Util::$bancos[$this->banco] ?? 'XXX' );

        return $this;
    }

    public function getBancoNome(): string
    {
        return $this->bancoNome;
    }

    public function setBancoNome( $bancoNome ): Conta
    {
        $this->bancoNome = $bancoNome;

        return $this;
    }

    public function getAgencia(): string
    {
        return $this->agencia;
    }

    public function setAgencia( $agencia ): Conta
    {
        $this->agencia = $agencia;

        return $this;
    }

    public function getAgenciaDv(): string
    {
        return $this->agenciaDv;
    }

    public function setAgenciaDv( $agenciaDv ): Conta
    {
        $this->agenciaDv = $agenciaDv;

        return $this;
    }

    public function getConta(): string
    {
        return $this->conta;
    }

    public function setConta( $conta ): Conta
    {
        $this->conta = $conta;

        return $this;
    }

    public function getContaDv(): string
    {
        return $this->contaDv;
    }

    public function setContaDv( $contaDv ): Conta
    {
        $this->contaDv = $contaDv;

        return $this;
    }

    public function getPessoa(): PessoaContract
    {
        return $this->pessoa;
    }

    public function setPessoa( PessoaContract $pessoa ): Conta
    {
        $this->pessoa = $pessoa;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'banco'     => $this->getBanco(),
            'bancoNome' => $this->getBancoNome(),
            'agencia'   => $this->getAgencia(),
            'agenciaDv' => $this->getAgenciaDv(),
            'conta'     => $this->getConta(),
            'contaDv'   => $this->getContaDv(),
            'pessoa'    => isset( $this->pessoa ) ? $this->pessoa->toArray() : [],
        ];
    }
}
