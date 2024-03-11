<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Cnab\Retorno\AbstractRetorno as AbstractRetornoGeneric;
use Throwable;

abstract class AbstractRetorno extends AbstractRetornoGeneric
{
    const LOTE_TRANSFER = 1;
    const LOTE_TED      = 41;
    const LOTE_PIX      = 45;

    private array $headerLote;
    private array $trailerLote;

    public function __construct( $file )
    {
        parent::__construct( $file );

        $this->header      = new Header();
        $this->headerLote  = [
            self::LOTE_TRANSFER => new HeaderLote( self::LOTE_TRANSFER ),
            self::LOTE_TED      => new HeaderLote( self::LOTE_TED ),
            self::LOTE_PIX      => new HeaderLote( self::LOTE_PIX ),
        ];
        $this->trailerLote = [
            self::LOTE_TRANSFER => new TrailerLote( self::LOTE_TRANSFER ),
            self::LOTE_TED      => new TrailerLote( self::LOTE_TED ),
            self::LOTE_PIX      => new TrailerLote( self::LOTE_PIX ),
        ];
        $this->trailer     = new Trailer();

        $this->processar();
    }

    public function getHeaderLote( int $lote ): HeaderLote
    {
        return $this->headerLote[$lote];
    }

    public function getTrailerLote( int $lote ): TrailerLote
    {
        return $this->trailerLote[$lote];
    }

    public function getHeaderLotes(): array
    {
        return $this->headerLote;
    }

    public function getTrailerLotes(): array
    {
        return $this->trailerLote;
    }

    abstract protected function processarHeader( array $header ): bool;

    abstract protected function processarHeaderLote( array $headerLote ): bool;

    abstract protected function processarDetalhe( array $detalhe ): bool;

    abstract protected function processarTrailerLote( array $trailer ): bool;

    abstract protected function processarTrailer( array $trailer ): bool;

    protected function incrementDetalhe(): void
    {
        $this->increment++;

        $this->detalhe[$this->increment] = new Detalhe();
    }

    /**
     * @throws Throwable
     */
    public function processar()
    {
        if ( $this->isProcessado() ) {
            return $this;
        }

        if ( method_exists( $this, 'init' ) ) {
            call_user_func( [$this, 'init'] );
        }

        foreach ( $this->file as $linha ) {
            $i = $this->rem( 8, 8, $linha );
            switch ( $i ) {
                case '0':
                    $this->processarHeader( $linha );
                    break;
                case '1':
                    $this->processarHeaderLote( $linha );
                    break;
                case '3':
                    if ( $this->getSegmentType( $linha ) == 'A' ) {
                        $this->incrementDetalhe();
                    }

                    if ( $this->processarDetalhe( $linha ) === false ) {
                        unset( $this->detalhe[$this->increment] );
                        $this->increment--;
                    }
                    break;
                case '5':
                    $this->processarTrailerLote( $linha );
                    break;
                case '9':
                    $this->processarTrailer( $linha );
                    break;
            }
        }

        if ( method_exists( $this, 'finalize' ) ) {
            call_user_func( [$this, 'finalize'] );
        }

        return $this->setProcessado();
    }

    public function toArray(): array
    {
        $array = [
            'header'      => $this->header->toArray(),
            'headerLote'  => $this->getHeaderLotes(),
            'trailerLote' => $this->getTrailerLotes(),
            'trailer'     => $this->trailer->toArray(),
            'detalhes'    => [],
            'totais'      => $this->totais,
        ];

        foreach ( $this->detalhe as $detalhe ) {
            $array['detalhes'][] = $detalhe->toArray();
        }

        return $array;
    }

    /**
     * @throws Throwable
     */
    protected function getSegmentType( $line ): string
    {
        return strtoupper( $this->rem( 14, 14, $line ) );
    }
}
