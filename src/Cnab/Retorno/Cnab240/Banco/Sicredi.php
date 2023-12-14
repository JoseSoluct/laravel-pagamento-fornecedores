<?php

namespace RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Banco;

use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\AbstractRetorno;
use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Detalhe;
use RedeCauzzoMais\Pagamento\Contracts\Conta as ContaContract;
use RedeCauzzoMais\Pagamento\Contracts\Cnab\RetornoCnab240;
use RedeCauzzoMais\Pagamento\Contracts\Pagamento\Pagamento;
use RedeCauzzoMais\Pagamento\Pagamento\Banco\Sicredi\Pix;
use RedeCauzzoMais\Pagamento\Util;
use Exception;

class Sicredi extends AbstractRetorno implements RetornoCnab240
{
    protected string $codigoBanco = Pagamento::COD_BANCO_SICREDI;

    private int $loteAtual;

    private array $ocorrencias = [
        '00' => 'Crédito ou débito efetivado à indica que o pagamento foi confirmado',
        '01' => 'Insuficiência de fundos - Débito Não Efetuado',
        '02' => 'Crédito ou débito cancelado pelo pagador/credor',
        '03' => 'Débito autorizado pela agência – Efetuado',
        'AA' => 'Controle inválido',
        'AB' => 'Tipo de operação inválido',
        'AC' => 'Tipo de serviço inválido',
        'AD' => 'Forma de lançamento inválida',
        'AE' => 'Tipo/número de inscrição inválido',
        'AF' => 'Código de convênio inválido',
        'AG' => 'Agência/conta corrente/DV inválido',
        'AH' => 'Nº sequencial do registro no lote inválido',
        'AI' => 'Código de segmento de detalhe inválido',
        'AJ' => 'Tipo de movimento inválido',
        'AK' => 'Código da câmara de compensação do banco favorecido/depositário inválido.',
        'AL' => 'Código do banco favorecido ou depositário inválido',
        'AM' => 'Agência mantenedora da conta corrente do favorecido inválida',
        'AN' => 'Conta corrente/DV do favorecido inválido',
        'AO' => 'Nome do favorecido não informado',
        'AP' => 'Data lançamento inválido',
        'AQ' => 'Tipo/quantidade da moeda inválido',
        'AR' => 'Valor do lançamento inválido',
        'AS' => 'Aviso ao favorecido - identificação inválida',
        'AT' => 'Tipo/número de inscrição do favorecido inválido',
        'AV' => 'Nº do local do favorecido não informado',
        'AW' => 'Cidade do favorecido não informada',
        'AX' => 'CEP/complemento do favorecido inválido',
        'AY' => 'Sigla do estado do favorecido inválida',
        'AZ' => 'Código/nome do banco depositário inválido',
        'BA' => 'Código/nome da agência depositária não informado',
        'BB' => 'Seu número inválido',
        'BC' => 'Nosso número inválido',
        'BD' => 'Inclusão efetuada com sucesso',
        'BE' => 'Alteração efetuada com sucesso',
        'BF' => 'Exclusão efetuada com sucesso',
        'BG' => 'Agência/conta impedida legalmente/Bloqueada',
        'BH' => 'Empresa não pagou salário',
        'BI' => 'Falecimento do mutuário',
        'BJ' => 'Empresa não enviou remessa do mutuário',
        'BK' => 'Empresa não enviou remessa no vencimento',
        'BL' => 'Valor da parcela inválida',
        'BM' => 'Identificação do contrato inválida',
        'BN' => 'Operação de consignação incluída com sucesso',
        'BO' => 'Operação de consignação alterada com sucesso',
        'BP' => 'Operação de consignação excluída com sucesso',
        'BQ' => 'Operação de consignação liquidada com sucesso',
        'CA' => 'Código de barras - código do banco inválido',
        'CB' => 'Código de barras - código da moeda inválido',
        'CC' => 'Código de barras - dígito verificador geral inválido',
        'CD' => 'Código de barras - valor do título inválido',
        'CE' => 'Código de barras - campo livre inválido',
        'CF' => 'Valor do documento inválido',
        'CG' => 'Valor do abatimento inválido',
        'CH' => 'Valor do desconto inválido',
        'CI' => 'Valor de mora inválido',
        'CJ' => 'Valor da multa inválido',
        'CK' => 'Valor do IR inválido',
        'CL' => 'Valor do ISS inválido',
        'CM' => 'Valor do IOF inválido',
        'CN' => 'Valor de outras deduções inválido',
        'CO' => 'Valor de outros acréscimos inválido',
        'CP' => 'Valor do INSS inválido',
        'HA' => 'Lote não aceito',
        'HB' => 'Inscrição da empresa inválida para o contrato',
        'HC' => 'Convênio com a empresa inexistente/inválido para o contrato',
        'HD' => 'Agência/conta corrente da empresa inexistente/inválido para o contrato',
        'HE' => 'Tipo de serviço inválido para o contrato',
        'HF' => 'Conta corrente da empresa com saldo insuficiente',
        'HG' => 'Lote de serviço fora de sequência',
        'HH' => 'Lote de serviço inválido',
        'HI' => 'Arquivo não aceito',
        'HJ' => 'Tipo de registro inválido',
        'HK' => 'Código remessa / retorno inválido',
        'HL' => 'Versão de leiaute inválida',
        'HM' => 'Mutuário não identificado',
        'HN' => 'Tipo do benefício não permite empréstimo',
        'HO' => 'Benefício cessado/suspenso',
        'HP' => 'Benefício possui representante legal',
        'HQ' => 'Benefício é do tipo PA (pensão alimentícia)',
        'HR' => 'Quantidade de contratos permitida excedida',
        'HS' => 'Benefício não pertence ao banco informado',
        'HT' => 'Início do desconto informado já ultrapassado',
        'H1' => 'Arquivo sem trailer',
        'H2' => 'Mutuário sem crédito na competência',
        'H3' => 'Não descontado – outros motivos',
        'H4' => 'Retorno de crédito não pago',
        'H5' => 'Cancelamento de empréstimo retroativo',
        'H6' => 'Outros motivos de glosa',
        'H7' => 'Margem consignável excedida para o mutuário acima do prazo do contrato',
        'H8' => 'Mutuário desligado do empregador',
        'H9' => 'Mutuário afastado por licença',
        'TA' => 'Lote não aceito - totais do lote com diferença',
        'YA' => 'Título não encontrado',
        'YB' => 'Identificador registro opcional inválido',
        'YC' => 'Código padrão inválido',
        'YD' => 'Código de ocorrência inválido.',
        'YE' => 'Complemento de ocorrência inválido',
        'YF' => 'Alegação já informada',
        'ZA' => 'Agência / Conta do Favorecido Substituída',
        'ZB' => 'Divergência entre o primeiro e último nome do beneficiário versus primeiro e último nome na Receita Federal',
        'ZC' => 'Confirmação de Antecipação de Valor',
        'ZD' => 'Antecipação parcial de valor',
        'ZE' => 'Título bloqueado na base',
        'ZF' => 'Sistema em contingência – título valor maior que referência',
        'ZI' => 'Beneficiário divergente',
        'ZK' => 'Boleto já liquidado',
        'ZJ' => 'Limite de pagamento parciais excedido',
        'ZG' => 'Sistema em contingência - Título vencido',
        'ZH' => 'Sistema em contingência - Título Indexado',
        'PA' => 'PIX não efetivado',
        'PB' => 'Transação interrompida devido a erro no PSP do Recebedor',
        'PC' => 'Número da conta transacional encerrada no PSP do Recebedor',
        'PD' => 'Tipo incorreto para a conta transacional especificada',
        'PE' => 'Tipo de transação não é suportado/autorizado na conta transacional especificada',
        'PF' => 'CPF/CNPJ do usuário recebedor não é consistente com o titular da conta transacional especificada',
        'PG' => 'CPF/CNPJ do usuário recebedor incorreto',
        'PH' => 'Ordem rejeitada pelo PSP do Recebedor',
        'PI' => 'ISPB do PSP do Pagador inválido ou inexistente',
        'PJ' => 'Chave não cadastrada no DICT',
        'PK' => 'QR Code inválido/vencido',
        'PL' => 'Forma de iniciação inválida ',
        'PM' => 'Chave de pagamento inválida',
        'PN' => 'Chave de pagamento não informada',
    ];

    protected function init(): void
    {
        $this->totais = [
            'entradas'   => 0,
            'liquidados' => 0,
            'alterados'  => 0,
            'excluidos'  => 0,
            'erros'      => 0,
        ];
    }

    /**
     * @throws Exception
     */
    protected function processarHeader( array $header ): bool
    {
        $this->getHeader()
             ->setCodBanco( $this->rem( 1, 3, $header ) )
             ->setLoteServico( $this->rem( 4, 7, $header ) )
             ->setTipoRegistro( $this->rem( 8, 8, $header ) )
             ->setTipoInscricao( $this->rem( 18, 18, $header ) )
             ->setNumeroInscricao( $this->rem( 19, 32, $header ) )
             ->setConvenio( $this->rem( 33, 52, $header ) )
             ->setCodigoCedente( $this->rem( 33, 52, $header ) )
             ->setAgencia( $this->rem( 53, 57, $header ) )
             ->setAgenciaDv( $this->rem( 58, 58, $header ) )
             ->setConta( $this->rem( 59, 70, $header ) )
             ->setContaDv( $this->rem( 71, 71, $header ) )
             ->setNomeEmpresa( $this->rem( 73, 102, $header ) )
             ->setDocumentoEmpresa( $this->rem( 19, 32, $header ) )
             ->setNomeBanco( $this->rem( 103, 132, $header ) )
             ->setCodigoRemessaRetorno( $this->rem( 143, 143, $header ) )
             ->setDataGeracao( $this->rem( 144, 151, $header ) )
             ->setNumeroSequencialArquivo( $this->rem( 158, 163, $header ) )
             ->setVersaoLayoutArquivo( $this->rem( 164, 166, $header ) );

        if ( empty( $this->getHeader()->getNomeBanco() ) ) {
            $this->getHeader()->setNomeBanco( Util::$bancos[$this->rem( 1, 3, $header )] );
        }

        return true;
    }

    /**
     * @throws Exception
     */
    protected function processarHeaderLote( array $headerLote ): bool
    {
        $this->loteAtual = (int) $this->rem( 12, 13, $headerLote );

        $this->getHeaderLote( $this->loteAtual )
             ->setCodBanco( $this->rem( 1, 3, $headerLote ) )
             ->setLoteServico( $this->rem( 4, 7, $headerLote ) )
             ->setTipoRegistro( $this->rem( 8, 8, $headerLote ) )
             ->setTipoOperacao( $this->rem( 9, 9, $headerLote ) )
             ->setTipoServico( $this->rem( 10, 11, $headerLote ) )
             ->setVersaoLayoutLote( $this->rem( 14, 16, $headerLote ) )
             ->setTipoInscricao( $this->rem( 18, 18, $headerLote ) )
             ->setNumeroInscricao( $this->rem( 19, 32, $headerLote ) )
             ->setConvenio( $this->rem( 33, 52, $headerLote ) )
             ->setAgencia( $this->rem( 53, 57, $headerLote ) )
             ->setAgenciaDv( $this->rem( 58, 58, $headerLote ) )
             ->setConta( $this->rem( 59, 70, $headerLote ) )
             ->setContaDv( $this->rem( 71, 71, $headerLote ) )
             ->setNomeEmpresa( $this->rem( 73, 102, $headerLote ) );

        return true;
    }

    /**
     * @throws Exception
     */
    protected function processarDetalhe( array $detalhe ): bool
    {
        /** @var Detalhe $d */
        $d = $this->detalheAtual();

        if ( is_null( $d->getContaFavorecido() ) ) {
            $d->setContaFavorecido( ['pessoa' => ['nome' => '', 'documento' => '']] );
        }

        if ( is_null( $d->getContaPagador() ) ) {
            $d->setContaPagador( [
                'banco'     => $this->getHeader()->getCodBanco(),
                'agencia'   => $this->getHeader()->getAgencia(),
                'agenciaDv' => $this->getHeader()->getAgenciaDv(),
                'conta'     => $this->getHeader()->getConta(),
                'contaDv'   => $this->getHeader()->getContaDv(),
                'pessoa'    => [
                    'nome'      => $this->getHeader()->getNomeEmpresa(),
                    'documento' => $this->getHeader()->getDocumentoEmpresa(),
                ],
            ] );
        }

        switch ( $this->getSegmentType( $detalhe ) ) {
            case 'A':
                $d->setDataOcorrencia( $this->getHeader()->getDataGeracao( 'dmY' ) )
                  ->setOcorrencia( $this->rem( 231, 240, $detalhe ) )
                  ->setOcorrenciaDescricao( array_get( $this->ocorrencias, $d->getOcorrencia(), 'Desconhecida' ) )
                  ->setNossoNumero( $this->rem( 135, 154, $detalhe ) )
                  ->setNumeroDocumento( $this->rem( 74, 93, $detalhe ) )
                  ->setDataPagamento( $this->rem( 94, 101, $detalhe ) )
                  ->setValorPagamento( Util::toFloat( $this->rem( 120, 134, $detalhe ) / 100, 2, false ) )
                  ->setLoteServico( $this->rem( 4, 7, $detalhe ) )
                  ->setTipoRegistro( $this->rem( 8, 8, $detalhe ) )
                  ->setSegmento( $this->rem( 14, 14, $detalhe ) )
                  ->setFormaLancamento( $this->loteAtual );

                $d->getContaFavorecido()->getPessoa()->setNome( $this->rem( 44, 73, $detalhe ) );

                $d->getContaFavorecido()
                  ->setBanco( $this->rem( 21, 23, $detalhe ) )
                  ->setAgencia( $this->rem( 24, 28, $detalhe ) )
                  ->setAgenciaDv( $this->rem( 29, 29, $detalhe ) )
                  ->setConta( $this->rem( 30, 41, $detalhe ) )
                  ->setContaDv( $this->rem( 42, 42, $detalhe ) );

                if ( $d->hasOcorrencia( '00', '03' ) ) {
                    $this->totais['liquidados']++;
                    $d->setOcorrenciaTipo( $d::OCORRENCIA_LIQUIDADA );
                } elseif ( $d->hasOcorrencia( 'BD' ) ) {
                    $this->totais['entradas']++;
                    $d->setOcorrenciaTipo( $d::OCORRENCIA_ENTRADA );
                } elseif ( $d->hasOcorrencia( 'BE' ) ) {
                    $this->totais['alterados']++;
                    $d->setOcorrenciaTipo( $d::OCORRENCIA_ALTERACAO );
                } elseif ( $d->hasOcorrencia( 'BF' ) ) {
                    $this->totais['excluidos']++;
                    $d->setOcorrenciaTipo( $d::OCORRENCIA_BAIXADA );
                } else {
                    if ( isset( $this->ocorrencias[$d->getOcorrencia()] ) ) {
                        $d->setError( $this->ocorrencias[$d->getOcorrencia()] );

                        $this->totais['erros']++;
                    }

                    $d->setOcorrenciaTipo( $d::OCORRENCIA_OUTROS );
                }

                break;
            case 'B':

                if ( !empty( $pixTipo = $this->rem( 15, 17, $detalhe ) ) ) {
                    $chavePix = match ( $pixTipo ) {
                        Pix::CHAVE_CPF => $this->rem( 22, 32, $detalhe ),
                        Pix::CHAVE_CNPJ => $this->rem( 19, 32, $detalhe ),
                        Pix::CHAVE_TELEFONE, Pix::CHAVE_EMAIL, Pix::CHAVE_ALEATORIA => $this->rem( 68, 127, $detalhe ),
                        Pix::CHAVE_DADOS_BANCARIOS => 'Chave de dados bancários',
                        default => throw new Exception( 'Tipo de chave PIX desconhecida' ),
                    };

                    $d->setPixTipo( $pixTipo );
                    $d->setPixChave( $chavePix );
                }

                $documento = $this->rem( 19, 32, $detalhe );

                if ( $this->rem( 18, 18, $detalhe ) == 1 ) {
                    $documento = substr( $documento, 3 );
                }

                if ( $d->getContaFavorecido() instanceof ContaContract ) {
                    $d->getContaFavorecido()->getPessoa()->setDocumento( $documento );
                }
                break;
            case 'Z':
                $d->setAutenticacao( $this->rem( 15, 78, $detalhe ) );
                break;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    protected function processarTrailerLote( array $trailer ): bool
    {
        $this->getTrailerLote( $this->loteAtual )
             ->setLoteServico( $this->rem( 4, 7, $trailer ) )
             ->setTipoRegistro( $this->rem( 8, 8, $trailer ) )
             ->setQtdRegistroLote( $this->rem( 18, 23, $trailer ) )
             ->setValorTotalTitulos( Util::toFloat( $this->rem( 24, 41, $trailer ) / 100, 2, false ) );

        return true;
    }

    /**
     * @throws Exception
     */
    protected function processarTrailer( array $trailer ): bool
    {
        $this->getTrailer()
             ->setNumeroLote( $this->rem( 4, 7, $trailer ) )
             ->setTipoRegistro( $this->rem( 8, 8, $trailer ) )
             ->setQtdLotesArquivo( (int) $this->rem( 18, 23, $trailer ) )
             ->setQtdRegistroArquivo( (int) $this->rem( 24, 29, $trailer ) );

        return true;
    }
}
