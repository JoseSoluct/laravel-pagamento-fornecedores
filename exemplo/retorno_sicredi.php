<?php

use RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Banco\Sicredi;

require 'autoload.php';

$path = realpath( __DIR__ . '/arquivos/3Q5C0202161503.ret' );

$retorno = new Sicredi( $path );

echo $retorno->getBancoNome() . PHP_EOL . PHP_EOL;

dump( $retorno->count() );
dump( $retorno->totais );

/** @var $registro \RedeCauzzoMais\Pagamento\Cnab\Retorno\Cnab240\Detalhe */
//foreach ( $retorno as $registro ) {
//    dump( $registro->toArray() );
//}
