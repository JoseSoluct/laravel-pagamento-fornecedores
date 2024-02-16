<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno;

use Illuminate\Support\Collection;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Detalhe as Detalhe240Contract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Header as Header240Contract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\Trailer as Trailer240Contract;
use RedeCauzzoMais\Pagamento\Util;
use OutOfBoundsException;
use SeekableIterator;
use Countable;
use Exception;

abstract class AbstractRetorno implements Countable, SeekableIterator
{
    protected Header240Contract  $header;
    protected Trailer240Contract $trailer;

    protected bool   $processado = false;
    protected string $codigoBanco;
    protected int    $increment  = 0;
    protected array  $file;
    protected array  $detalhe    = [];
    protected array  $totais     = [];
    private int      $_position  = 1;

    /**
     * @throws Exception
     */
    public function __construct( $file )
    {
        if ( !$this->file = Util::file2array( $file ) ) {
            throw new Exception( 'Arquivo não existe' );
        }

        if ( !Util::isHeaderRetorno( $this->file[0] ) ) {
            throw new Exception( 'Arquivo de retorno inválido' );
        }

        $codigoBanco = Util::isCnab400( $this->file[0] ) ? substr( $this->file[0], 76, 3 ) : substr( $this->file[0], 0, 3 );

        if ( $this->codigoBanco <> $codigoBanco ) {
            throw new Exception( "Banco {$codigoBanco}, inválido" );
        }
    }

    public function getCodigoBanco(): string
    {
        return $this->codigoBanco;
    }

    public function getBancoNome(): string
    {
        return Util::bancoNome( $this->codigoBanco );
    }

    public function getDetalhes(): Collection
    {
        return new Collection( $this->detalhe );
    }

    public function getDetalhe( $i ): ?Detalhe240Contract
    {
        return $this->detalhe[$i] ?? null;
    }

    public function getHeader(): Header240Contract
    {
        return $this->header;
    }

    public function getTrailer(): Trailer240Contract
    {
        return $this->trailer;
    }

    protected function detalheAtual(): ?Detalhe240Contract
    {
        return $this->detalhe[$this->increment];
    }

    protected function isProcessado(): bool
    {
        return $this->processado;
    }

    protected function setProcessado(): static
    {
        $this->processado = true;

        return $this;
    }

    public function getTotais(): array
    {
        return $this->totais;
    }

    abstract protected function incrementDetalhe();

    abstract protected function processar();

    abstract protected function toArray(): array;

    /**
     * @throws Exception
     */
    protected function rem( $i, $f, &$array ): string
    {
        return Util::removeInPosition( $i, $f, $array );
    }

    public function current(): mixed
    {
        return $this->detalhe[$this->_position];
    }

    public function next(): void
    {
        ++$this->_position;
    }

    public function key(): mixed
    {
        return $this->_position;
    }

    public function valid(): bool
    {
        return isset( $this->detalhe[$this->_position] );
    }

    public function rewind(): void
    {
        $this->_position = 1;
    }

    public function count(): int
    {
        return count( $this->detalhe );
    }

    public function seek( $offset ): void
    {
        $this->_position = $offset;
        if ( !$this->valid() ) {
            throw new OutOfBoundsException( "Posição inválida {$offset}" );
        }
    }
}
