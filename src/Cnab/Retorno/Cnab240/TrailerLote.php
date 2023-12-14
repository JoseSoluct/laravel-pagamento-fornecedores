<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240;

use RedeCauzzoMais\Pagamento\Contracts\Cnab\Retorno\Cnab240\TrailerLote as TrailerLoteContract;
use RedeCauzzoMais\Pagamento\Traits\MagicTrait;

class TrailerLote implements TrailerLoteContract
{
    use MagicTrait;

    protected int   $loteServico;
    protected int   $TipoRegistro;
    protected int   $qtdRegistroLote;
    protected float $valorTotalTitulos;

    public function __construct( protected $formaLancamento )
    {
    }

    public function getLoteServico(): int
    {
        return $this->loteServico;
    }

    public function setLoteServico( int $loteServico ): static
    {
        $this->loteServico = $loteServico;

        return $this;
    }

    public function getQtdRegistroLote(): int
    {
        return $this->qtdRegistroLote;
    }

    public function setQtdRegistroLote( int $qtdRegistroLote ): static
    {
        $this->qtdRegistroLote = $qtdRegistroLote;

        return $this;
    }

    public function getTipoRegistro(): int
    {
        return $this->TipoRegistro;
    }

    public function setTipoRegistro( int $TipoRegistro ): static
    {
        $this->TipoRegistro = $TipoRegistro;

        return $this;
    }

    public function getValorTotalTitulos(): float
    {
        return $this->valorTotalTitulos;
    }

    public function setValorTotalTitulos( float $valorTotalTitulos ): static
    {
        $this->valorTotalTitulos = $valorTotalTitulos;

        return $this;
    }
}
