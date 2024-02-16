<?php

use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Banco\Sicredi;

require 'autoload.php';

$path = realpath( __DIR__ . '/arquivos/3Q5C0202161503.ret' );

$retorno = new Sicredi( $path );

echo $retorno->getBancoNome() . PHP_EOL . PHP_EOL;

dump( $retorno->count() );
dump( $retorno->getTotais() );

$extra = [
    'codigo_empresa'     => $retorno->getHeader()->getCodigoCedente(),
    'sequencial_arquivo' => $retorno->getHeader()->getNumeroSequencialArquivo(),
];

/** @var $registro \RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Detalhe */
foreach ( $retorno as $registro ) {
    try {
        $comprovante = \RedeCauzzoMais\Pagamento\Comprovante\Banco\Sicredi\Factory::make( $registro, $extra );

        file_put_contents( "/tmp/{$registro->getAutenticacao()}.pdf", $comprovante );

        dump( $registro->toArray() );
    } catch ( Exception $e ) {
        dump( $e->getMessage() );
    }
}

