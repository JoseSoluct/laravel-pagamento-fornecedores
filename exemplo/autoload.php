<?php
if ( file_exists( $path = realpath( __DIR__ . '/../vendor/' ) . DIRECTORY_SEPARATOR . 'autoload.php' ) ) {
    require $path;
} else {
    trigger_error( 'autoload.php não localizado' );
}
