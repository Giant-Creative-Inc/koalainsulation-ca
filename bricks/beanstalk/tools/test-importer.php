<?php
/** Run isolated plugin tests in separate PHP processes. */
foreach ( array_merge( glob( dirname( __DIR__, 3 ) . '/tests/city-import/*-test.php' ), glob( dirname( __DIR__, 3 ) . '/tests/content-engine/*.php' ) ) as $test ) {
	passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $test ), $status );
	if ( $status ) { exit( $status ); }
}
