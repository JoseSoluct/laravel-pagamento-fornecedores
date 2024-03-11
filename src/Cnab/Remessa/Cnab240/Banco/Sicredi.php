<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Remessa\Cnab240\Banco;

use RedeCauzzoMais\Pagamento\Cnab\Remessa\Cnab240\AbstractRemessa;
use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento as PagamentoContract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\Remessa as RemessaContract;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Ted;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Transfer;
use RedeCauzzoMais\Pagamento\Util;
use Exception;
use Throwable;

class Sicredi extends AbstractRemessa implements RemessaContract
{
    protected string $endLine = "\r\n";
    protected string $endFile = "\r\n";

    protected string $codigoBanco = PagamentoContract::COD_BANCO_SICREDI;
    protected string $codigoCliente;
    protected string $agenciaDv;

    protected array $carteiras = [1];

    protected array $totalLote        = [
        Pix::class      => 0,
        Transfer::class => 0,
        Ted::class      => 0,
    ];
    protected array $countLote        = [];
    protected array $countRecordsLote = [
        Pix::class      => 0,
        Transfer::class => 0,
        Ted::class      => 0,
    ];

    public function __construct( array $params = [] )
    {
        parent::__construct( $params );

        $this->addRequiredFields( ['codigoCliente', 'agenciaDv', 'idRemessa'] );
    }

    public function getCodigoCliente(): string
    {
        return $this->codigoCliente;
    }

    public function setCodigoCliente( $codigoCliente ): static
    {
        $this->codigoCliente = $codigoCliente;

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

    /**
     * @throws Throwable
     */
    public function addPagamento( PagamentoContract $pay ): static
    {
        $payClass = get_class( $pay );
        if ( !isset( $this->countLote[$payClass] ) ) {
            $this->countLote[$payClass] = count( $this->countLote ) + 1;
        }

        $pay->isValid( $messages );

        if ( !empty( $messages ) ) {
            throw new Exception( implode( PHP_EOL, $messages ) );
        }

        $this->segmentoA( $this->countRecordsLote[$payClass] += 1, $pay );

        switch ( $payClass ) {
            case Pix::class:
                $this->segmentoBPix( $this->countRecordsLote[$payClass] += 1, $pay );
                break;
            case Ted::class:
            case Transfer::class:
                $this->segmentoB( $this->countRecordsLote[$payClass] += 1, $pay );
                break;
            default:
                throw new Exception( 'Tipo de pagamento não suportado' );
        }

        $this->totalLote[$payClass] += (float) $pay->getValor();

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function segmentoA( $nSequencialLote, PagamentoContract $pay ): static
    {
        $payClass = get_class( $pay );

        $this->startDetalhe( $payClass );

        $codCamara = match ( $payClass ) {
            Pix::class => '009',
            Ted::class => '018',
            Transfer::class => '000',
            default => throw new Exception( 'Tipo de pagamento não suportado' ),
        };

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Código do Banco
        $this->add( 4, 7, Util::formatCnab( 9, $this->countLote[$payClass], 4 ) ); // Lote de Serviço
        $this->add( 8, 8, Util::formatCnab( 9, 3, 1 ) ); // Tipo de Registro
        $this->add( 9, 13, Util::formatCnab( 9, $nSequencialLote, 5 ) ); // Nº sequencial do registro de lote
        $this->add( 14, 14, Util::formatCnab( '9', 'A', 1 ) ); // Código de segmento do reg. detalhe
        $this->add( 15, 15, Util::formatCnab( '9', $pay->getTipoMovimento(), 1 ) ); // Tipo de movimento
        $this->add( 16, 17, Util::formatCnab( 9, $pay->getTipoMovimento(), 2 ) ); // Código da instrução movimento
        $this->add( 18, 20, Util::formatCnab( 9, $codCamara, 3 ) ); // Código da câmara centralizadora
        $this->add( 21, 23, Util::formatCnab( 9, $pay->getBanco(), 3 ) ); // Código do banco favorecido
        $this->add( 24, 28, Util::formatCnab( 9, $pay->getAgencia(), 5 ) ); // Numero da agência
        $this->add( 29, 29, Util::formatCnab( 'X', $pay->getAgenciaDv(), 1 ) ); // Digito verificador da agência
        $this->add( 30, 41, Util::formatCnab( 9, $pay->getConta(), 12 ) ); // Numero da conta
        $this->add( 42, 42, Util::formatCnab( 'X', $pay->getContaDv(), 1 ) ); // Digito verificador da conta
        $this->add( 43, 43, '' ); // Reservado (Uso Banco)
        $this->add( 44, 73, Util::formatCnab( 'X', $pay->getFavorecido()->getNome(), 30 ) ); // Nome do pagador/Sacado
        $this->add( 74, 93, Util::formatCnab( 'X', $pay->getNumeroDocumento(), 20 ) ); // Número do documento
        $this->add( 94, 101, Util::formatCnab( 9, $pay->getData()->format( 'dmY' ), 8 ) ); // Data pagamento
        $this->add( 102, 104, Util::formatCnab( 'X', $pay->getTipoMoeda(), 3 ) ); // Tipo moeda
        $this->add( 105, 119, Util::formatCnab( 9, 0, 15 ) ); // Quantidade da moeda
        $this->add( 120, 134, Util::formatCnab( 9, $pay->getValor(), 15, 2 ) ); // Valor do pagamento
        $this->add( 135, 154, Util::formatCnab( 'X', '', 20 ) ); // No do docum. atribuído pelo banco
        $this->add( 155, 162, Util::formatCnab( 9, 0, 8 ) ); // Data real/Data real da efetivação pagto
        $this->add( 163, 177, Util::formatCnab( 9, 0, 15 ) ); // Valor real/Valor real da efetivação pagto
        $this->add( 178, 217, '' ); // Outras informações – vide formatação em G031 para identificação de depósito judicial e pagto. salários de servidores pelo SIAPE
        $this->add( 218, 219, '' ); // Reservado (Uso Banco)
        $this->add( 220, 224, '' ); // Código finalidade da TED
        if ( $payClass == Ted::class ) {
            $this->add( 220, 223, Util::formatCnab( 'X', $pay->getFinalidade(), 4 ) ); // Código finalidade da TED
        }
        $this->add( 225, 226, '' ); // Reservado (Uso Banco)/Complemento de finalidade pagto
        $this->add( 227, 229, '' ); // Reservado (Uso Banco)/Uso exclusivo SICREDI
        $this->add( 230, 230, '0' ); // Reservado (Uso Banco)/Aviso ao favorecido
        $this->add( 231, 240, '' ); // Reservado (Uso Banco)/Códigos das ocorrências p/ retorno

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function segmentoB( $nSequencialLote, PagamentoContract $pay ): void
    {
        $payClass = get_class( $pay );

        $this->startDetalhe( $payClass );

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Código do Banco
        $this->add( 4, 7, Util::formatCnab( 9, $this->countLote[$payClass], 4 ) ); // Numero do lote remessa
        $this->add( 8, 8, Util::formatCnab( 9, 3, 1 ) ); // Numero do lote remessa
        $this->add( 9, 13, Util::formatCnab( 9, $nSequencialLote, 5 ) ); // Nº sequencial do registro de lote
        $this->add( 14, 14, Util::formatCnab( 'X', 'B', 1 ) ); // Nº sequencial do registro de lote
        $this->add( 15, 17, '' ); // Reservado (Uso Banco)
        $this->add( 18, 18, strlen( Util::onlyNumbers( $pay->getFavorecido()
                                                           ->getDocumento() ) ) == 14 ? 2 : 1 ); // Tipo de inscrição
        $this->add( 19, 32, Util::formatCnab( 9, Util::onlyNumbers( $pay->getFavorecido()
                                                                        ->getDocumento() ), 14 ) ); // Número de inscrição do sacado
        $this->add( 33, 62, Util::formatCnab( 'X', $pay->getFavorecido()->getEndereco(), 30 ) ); // Endereço
        $this->add( 63, 67, Util::formatCnab( 9, $pay->getFavorecido()->getNumero(), 5 ) ); // Número
        $this->add( 68, 82, Util::formatCnab( 'X', $pay->getFavorecido()->getComplemento(), 15 ) ); // Complemento
        $this->add( 83, 97, Util::formatCnab( 'X', $pay->getFavorecido()->getBairro(), 15 ) ); // Bairro
        $this->add( 98, 117, Util::formatCnab( 'X', $pay->getFavorecido()->getCidade(), 20 ) ); // Cidade
        $this->add( 118, 125, Util::formatCnab( 'X', Util::onlyNumbers( $pay->getFavorecido()
                                                                            ->getCep() ), 8 ) ); // CEP do pagador/Sacado
        $this->add( 126, 127, Util::formatCnab( 'X', $pay->getFavorecido()->getUf(), 2 ) ); // Uf do sacado
        $this->add( 128, 135, Util::formatCnab( 9, 0, 8 ) ); // Data do vencimento (nominal)
        $this->add( 136, 150, Util::formatCnab( 9, 0, 15 ) ); // Valor do documento (nominal)
        $this->add( 151, 165, Util::formatCnab( 9, 0, 15 ) ); // Valor do abatimento
        $this->add( 166, 180, Util::formatCnab( 9, 0, 15 ) ); // Valor do desconto
        $this->add( 181, 195, Util::formatCnab( 9, 0, 15 ) ); // Valor da mora
        $this->add( 196, 210, Util::formatCnab( 9, 0, 15 ) ); // Valor da multa
        $this->add( 211, 225, Util::formatCnab( 'X', $pay->getNumeroDocumento(), 15 ) ); // Tipo de inscrição do sacado
        $this->add( 226, 226, Util::formatCnab( 9, 0, 1 ) ); // Identificador de carne 000 - Não possui, 001 - Possui Carné
        $this->add( 227, 232, Util::formatCnab( 9, 0, 6 ) ); // Sequencial da parcela
        $this->add( 233, 240, Util::formatCnab( 'X', '', 8 ) ); // Código ISPB
    }

    /**
     * @throws Throwable
     */
    public function segmentoBPix( $nSequencialLote, PagamentoContract $pay ): void
    {
        $payClass = get_class( $pay );

        $this->startDetalhe( $payClass );

        $chavePix = match ( $pay->getPixTipo() ) {
            Pix::CHAVE_EMAIL => $pay->getPixChave(),
            Pix::CHAVE_TELEFONE => '+' . Util::onlyNumbers( $pay->getPixChave() ),
            Pix::CHAVE_ALEATORIA => Util::toMask( Util::onlyChars( $pay->getPixChave() ), '########-####-####-####-############' ),
            default => '',
        };

        $pixDadosBancarios = '';
        if ( $pay->getPixTipo() == Pix::CHAVE_DADOS_BANCARIOS ) {
            $pixDadosBancarios = implode( '', [
                Util::formatCnab( 9, Util::onlyNumbers( $pay->getFavorecido()->getDocumento() ), 14 ),
                self::ISPB[$pay->getBanco()],
                $pay->getTipoConta()
            ] );
        }

        if ( !empty( $pay->getFavorecido()->getDocumento() ) ) {
            $tipoInscricao = strlen( Util::onlyNumbers( $pay->getFavorecido()->getDocumento() ) ) == 14 ? 2 : 1;
        }

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Código do Banco
        $this->add( 4, 7, Util::formatCnab( 9, $this->countLote[$payClass], 4 ) ); // Numero do lote remessa
        $this->add( 8, 8, Util::formatCnab( 9, 3, 1 ) );  // Tipo de registro
        $this->add( 9, 13, Util::formatCnab( 9, $nSequencialLote, 5 ) ); // Nº sequencial do registro de lote
        $this->add( 14, 14, Util::formatCnab( 'X', 'B', 1 ) ); // Código de segmento do reg. detalhe
        $this->add( 15, 17, Util::formatCnab( 'X', $pay->getPixTipo(), 3 ) ); // Tipo de identificação de chave PIX
        $this->add( 18, 18, Util::formatCnab( 9, $tipoInscricao ?? 0, 1 ) ); // Tipo de inscrição do sacado
        $this->add( 19, 32, Util::formatCnab( 9, Util::onlyNumbers( $pay->getFavorecido()
                                                                        ->getDocumento() ?? '' ), 14 ) ); // Número de inscrição
        $this->add( 33, 62, Util::formatCnab( 'X', '', 20 ) ); // Mensagem 1
        if ( empty( $pixDadosBancarios ) ) {
            $this->add( 63, 127, Util::formatCnab( 'X', '', 65 ) ); // Mensagem 2
        } else {
            $this->add( 63, 67, '' );
            $this->add( 68, 91, Util::formatCnab( 'X', $pixDadosBancarios, 24 ) ); // Dados complementares PIX conta bancária
            $this->add( 92, 127, '' );
        }

        $this->add( 128, 226, Util::formatCnab( 'AC', $chavePix, 99 ) ); // Chave pix email, telefone ou chave aleatoria
        $this->add( 227, 232, Util::formatCnab( 9, 0, 6 ) ); // Reservado (Uso Banco)
        $this->add( 233, 240, Util::formatCnab( 9, 0, 8 ) ); // Reservado (Uso Banco)
    }

    /**
     * @throws Throwable
     */
    protected function header(): static
    {
        $this->startHeader();

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Codigo do banco
        $this->add( 4, 7, '0000' ); // Lote de Serviço
        $this->add( 8, 8, '0' ); // Tipo de Registro
        $this->add( 9, 17, '' ); // Reservados (Uso Banco)
        $this->add( 18, 18, strlen( Util::onlyNumbers( $this->getBeneficiario()
                                                            ->getDocumento() ) ) == 14 ? 2 : 1 ); // Tipo de inscrição da empresa
        $this->add( 19, 32, Util::formatCnab( '9L', $this->getBeneficiario()
                                                         ->getDocumento(), 14 ) ); // Numero de inscrição da empresa
        $this->add( 33, 52, Util::formatCnab( 'X', $this->getCodigoCliente(), 20 ) ); // Código do convênio no banco
        $this->add( 53, 57, Util::formatCnab( '9', $this->getAgencia(), 5 ) ); // Agência mantenedora da conta
        $this->add( 58, 58, Util::formatCnab( 'X', $this->getAgenciaDv(), 1 ) ); // Dígito verificador da agência (Uso Branco)
        $this->add( 59, 70, Util::formatCnab( '9', $this->getConta(), 12 ) ); // Número da conta corrente
        $this->add( 71, 71, Util::formatCnab( '9', $this->getContaDv(), 1 ) ); // Dígito verificador da conta
        $this->add( 72, 72, '' ); // Dígito verificador da Ag/conta (Uso Banco)
        $this->add( 73, 102, Util::formatCnab( 'X', $this->getBeneficiario()->getNome(), 30 ) ); // Nome da empresa
        $this->add( 103, 132, Util::formatCnab( 'X', 'Sicredi', 30 ) ); // Nome do Banco
        $this->add( 133, 142, '' ); // Reservados (Uso Banco)
        $this->add( 143, 143, '1' ); // Codigo remessa
        $this->add( 144, 151, date( 'dmY' ) ); // Data de Geracao do arquivo
        $this->add( 152, 157, date( 'His' ) ); // Reservado (Uso Banco)
        $this->add( 158, 163, Util::formatCnab( 9, $this->getIdRemessa(), 6 ) ); // Numero Sequencial do arquivo
        $this->add( 164, 166, Util::formatCnab( '9', '082', 3 ) ); // Versão do layout
        $this->add( 167, 171, Util::formatCnab( '9', '1600', 5 ) ); // Versão do layout
        $this->add( 172, 240, '' ); // Reservado (Uso Banco)

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function headerLote( $pay ): static
    {
        $this->startHeaderLote();

        $formaLancamento = match ( $pay ) {
            Pix::class => '45',
            Transfer::class => '01',
            Ted::class => '41',
            default => throw new Exception( 'Tipo de pagamento não suportado' ),
        };

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Codigo do banco
        $this->add( 4, 7, Util::formatCnab( 9, $this->countLote[$pay], 4 ) ); // Lote de Serviço
        $this->add( 8, 8, '1' ); // Tipo de Registro
        $this->add( 9, 9, 'C' ); // Tipo de operação
        $this->add( 10, 11, Util::formatCnab( 9, 20, 2 ) ); // Tipo de serviço
        $this->add( 12, 13, Util::formatCnab( 9, $formaLancamento, 2 ) ); // Forma de lançamento
        $this->add( 14, 16, Util::formatCnab( '9', '042', 3 ) ); // Versão do layout
        $this->add( 17, 17, '' ); // Reservados (Uso Banco)
        $this->add( 18, 18, strlen( Util::onlyNumbers( $this->getBeneficiario()
                                                            ->getDocumento() ) ) == 14 ? 2 : 1 ); // Tipo de inscrição da empresa
        $this->add( 19, 32, Util::formatCnab( '9L', $this->getBeneficiario()->getDocumento(), 14 ) ); // CPF/CNPJ
        $this->add( 33, 52, Util::formatCnab( 'X', $this->getCodigoCliente(), 20 ) ); // Código do convênio no banco
        $this->add( 53, 57, Util::formatCnab( '9', $this->getAgencia(), 5 ) ); // Agência mantenedora da conta
        $this->add( 58, 58, Util::formatCnab( 'X', $this->getAgenciaDv(), 1 ) ); // Dígito verificador da agência (Uso Branco)
        $this->add( 59, 70, Util::formatCnab( '9', $this->getConta(), 12 ) ); // Número da conta corrente
        $this->add( 71, 71, Util::formatCnab( '9', $this->getContaDv(), 1 ) ); // Dígito verificador da conta
        $this->add( 72, 72, '' ); // Dígito verificador da Ag/conta (Uso Banco)
        $this->add( 73, 102, Util::formatCnab( 'X', $this->getBeneficiario()->getNome(), 30 ) ); // Nome do cedente
        $this->add( 103, 142, '' ); // Mensagem 1
        $this->add( 143, 172, Util::formatCnab( 'X', $this->getBeneficiario()->getEndereco(), 30 ) ); // Logradouro
        $this->add( 173, 177, Util::formatCnab( '9', $this->getBeneficiario()->getNumero(), 5 ) ); // Numero
        $this->add( 178, 192, Util::formatCnab( 'X', $this->getBeneficiario()->getComplemento(), 15 ) ); // Complemento
        $this->add( 193, 212, Util::formatCnab( 'X', $this->getBeneficiario()->getCidade(), 20 ) ); // Cidade
        $this->add( 213, 217, Util::formatCnab( 9, Util::onlyNumbers( $this->getBeneficiario()
                                                                           ->getCep() ), 5 ) ); // CEP
        $this->add( 218, 220, Util::formatCnab( 9, Util::onlyNumbers( substr( $this->getBeneficiario()
                                                                                   ->getCep(), 6, 9 ) ), 3 ) ); // SUFIXO do cep
        $this->add( 221, 222, Util::formatCnab( 'X', $this->getBeneficiario()->getUf(), 2 ) ); // Uf do sacado
        $this->add( 223, 240, '' ); // Reservados (Uso Banco)

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function trailerLote( $pay ): static
    {
        $this->startTrailerLote();

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Codigo do banco
        $this->add( 4, 7, Util::formatCnab( 9, $this->countLote[$pay], 4 ) ); // Numero do lote remessa
        $this->add( 8, 8, Util::formatCnab( 9, 5, 1 ) ); // Tipo de registro
        $this->add( 9, 17, '' ); // Reservado (Uso Banco)
        $this->add( 18, 23, Util::formatCnab( 9, ( $this->countRecordsLote[$pay] + 2 ), 6 ) ); // Quantidade de registros do lote
        $this->add( 24, 41, Util::formatCnab( 9, $this->totalLote[$pay], 18, 2 ) ); // Valor total do lote
        $this->add( 42, 59, Util::formatCnab( 9, 0, 18 ) ); // Quantidade de moedas
        $this->add( 60, 65, Util::formatCnab( 9, 0, 6 ) ); // Numero aviso debito
        $this->add( 66, 230, '' ); // Reservado (Uso Banco)
        $this->add( 231, 240, '' ); // Codigo das ocorrencias p/ retorno

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function trailer(): static
    {
        $this->startTrailer();

        $this->add( 1, 3, Util::onlyNumbers( $this->getCodigoBanco() ) ); // Codigo do banco
        $this->add( 4, 7, '9999' ); // Numero do lote remessa
        $this->add( 8, 8, '9' ); // Tipo de registro
        $this->add( 9, 17, '' ); // Reservado (Uso Banco)
        $this->add( 18, 23, Util::formatCnab( 9, count( $this->countLote ), 6 ) ); // Qtd de lotes do arquivo
        $this->add( 24, 29, Util::formatCnab( 9, ( array_sum( $this->countRecordsLote ) + 2 + 2 * count( $this->countLote ) ), 6 ) ); // Qtd de registros do arquivo
        $this->add( 30, 35, '000000' ); // Numero do lote remessa
        $this->add( 36, 240, '' ); // Reservado (Uso Banco)

        return $this;
    }

    public function gerar(): string
    {
        if ( !$this->isValid( $errors ) ) {
            throw new Exception( 'Campos requeridos pelo banco, aparentam estar ausentes: ' . implode( ', ', $errors ) );
        }

        $stringRemessa = '';
        if ( $this->countRecords < 1 ) {
            throw new Exception( 'Nenhuma linha detalhe foi adicionada' );
        }

        $this->header();
        $stringRemessa .= $this->valida( $this->getHeader() ) . $this->endLine;

        $detalhes = $this->getDetalhes();

        foreach ( $this->countLote as $payment => $lote ) {
            $this->headerLote( $payment );
            $stringRemessa .= $this->valida( $this->getHeaderLote() ) . $this->endLine;

            foreach ( $detalhes[$payment] as $detalhe ) {
                $stringRemessa .= $this->valida( $detalhe ) . $this->endLine;
            }

            $this->trailerLote( $payment );
            $stringRemessa .= $this->valida( $this->getTrailerLote() ) . $this->endLine;
        }

        $this->trailer();
        $stringRemessa .= $this->valida( $this->getTrailer() ) . $this->endFile;

        return $stringRemessa;
    }

    /**
     * @throws Throwable
     */
    public function getRemessaNomenclatura( int $incement = 0 ): string
    {
        return $this->getCodigoCliente() . date( 'd' ) . Util::formatCnab( 9, $incement, 2 ) . '.REM';
    }
}
