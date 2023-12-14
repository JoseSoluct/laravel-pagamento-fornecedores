<?php

namespace RedeCauzzoMais\Pagamento;

use RedeCauzzoMais\Pagamento\Contracts\Pessoa as PessoaContract;
use Exception;

class Pessoa implements PessoaContract
{
    protected ?string $nome        = null;
    protected ?string $documento   = null;
    protected ?string $cep         = null;
    protected ?string $endereco    = null;
    protected ?string $numero      = null;
    protected ?string $complemento = null;
    protected ?string $bairro      = null;
    protected ?string $uf          = null;
    protected ?string $cidade      = null;

    public function __construct( $params = [] )
    {
        Util::fillClass( $this, $params );
    }

    public static function create( $nome, $documento, $cep = null, $endereco = null, $numero = null, $complemento = null, $bairro = null, $cidade = null, $uf = null ): static
    {
        return new static( [
            'nome'        => $nome,
            'documento'   => $documento,
            'cep'         => $cep,
            'endereco'    => $endereco,
            'numero'      => $numero,
            'complemento' => $complemento,
            'bairro'      => $bairro,
            'cidade'      => $cidade,
            'uf'          => $uf,
        ] );
    }

    public function setCep( $cep ): static
    {
        $this->cep = $cep;

        return $this;
    }

    public function getCep(): ?string
    {
        if ( empty( $this->cep ) ) {
            return null;
        }

        return Util::toMask( Util::onlyNumbers( $this->cep ), '#####-###' );
    }

    public function setCidade( $cidade ): static
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    /**
     * @throws Exception
     */
    public function setDocumento( $documento ): static
    {
        $documento = substr( Util::onlyNumbers( $documento ), -14 );

        if ( !in_array( strlen( $documento ), [11, 14, 0] ) ) {
            throw new Exception( 'Documento inválido' );
        }

        $this->documento = $documento;

        return $this;
    }

    public function getDocumento(): ?string
    {
        if ( empty( $this->documento ) ) {
            return null;
        }

        $mask = $this->getTipoDocumento() == 'CPF' ? '###.###.###-##' : '##.###.###/####-##';

        return Util::toMask( Util::onlyNumbers( $this->documento ), $mask );
    }

    public function setEndereco( $endereco ): static
    {
        $this->endereco = $endereco;

        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setBairro( $bairro ): static
    {
        $this->bairro = $bairro;

        return $this;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function setNome( $nome ): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setUf( $uf ): static
    {
        $this->uf = $uf;

        return $this;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }


    public function setNumero( $numero ): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setComplemento( $complemento ): static
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function getTipoDocumento(): ?string
    {
        $documento = Util::onlyNumbers( $this->documento );

        if ( strlen( $documento ) == 11 ) {
            return 'CPF';
        }

        if ( strlen( $documento ) == 14 ) {
            return 'CNPJ';
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'nome'        => $this->getNome(),
            'documento'   => $this->getDocumento(),
            'cep'         => $this->getCep(),
            'endereco'    => $this->getEndereco(),
            'numero'      => $this->getNumero(),
            'complemento' => $this->getComplemento(),
            'bairro'      => $this->getBairro(),
            'cidade'      => $this->getCidade(),
            'uf'          => $this->getUf(),
        ];
    }
}
