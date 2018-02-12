#!/usr/bin/env php
<?php
$target_dir = __DIR__ . '/out/';
$path = __DIR__ . '/manuscript.txt';
$content = file_get_contents( $path );
// ルビを変換
$content = preg_replace( '/\|([^<]+)<(.*?)>/u', '{$1}($2)', $content );
// 強調を変換
$content = preg_replace_callback( '/\[(.*?)\]/u', function( $match ) {
	$letters = $match[1];
	$emphasized = '';
	for ( $i = 0, $l = mb_strlen( $letters, 'utf-8' ); $i < $l; $i++) {
		$letter = mb_substr( $letters, $i, 1, 'utf-8' );
		$emphasized .= sprintf( '{%s}(・)', $letter );
	}
	return $emphasized;
}, $content );
// 改行の中身を種別ごとのファイルに変換
$lines = explode( "\n", $content );
$prev_type = 'paragraph';
$files = [];
$index = 0;

/**
 * 2文字目を取得する関数
 * @param strin $line
 * @return string
 */
function second_letters( $line ) {
	return mb_substr( $line, 1, mb_strlen( $line ) - 1 );
};

foreach ( $lines as $line ) {
	$first_letter = mb_substr( $line, 0, 1, 'utf-8' );
	switch ( $first_letter ) {
		case '>':
			$cur_type = 'blockquote';
			$string = second_letters( $line );
			break;
		case '<':
			$cur_type = 'bottom';
			$string = second_letters( $line );
			break;
		case '~':
			$string = second_letters( $line );
			$cur_type = 'divider';
			break;
		case '':
		default:
			$string = $line;
			$cur_type = 'paragraph';
			break;
	}
	// Change index if changed.
	if ( $prev_type != $cur_type ) {
		$index++;
		$prev_type = $cur_type;
	}
	// Attache line
	$file_name = $index . '_' . $cur_type;
	if ( ! isset( $files[ $file_name ] ) ) {
		$files[ $file_name ] = [];
	}
	$files[ $file_name ][] = $string;
	
}

foreach ( $files as $file_name => $lines ) {
	file_put_contents( $target_dir . $file_name . '.txt', implode( "\n", $lines ) );
}
