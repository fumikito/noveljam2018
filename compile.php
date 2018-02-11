#!/usr/bin/env php
<?php

list( $this_file, $title ) = $argv;

if ( ! $title ) {
	die( 'タイトルを指定してください' );
}

$path = __DIR__ . '/manuscript.txt';
$title = htmlspecialchars( $title );
$content = file_get_contents( $path );
// ルビを変換
$content = preg_replace( '/\|([^<]+)<(.*?)>/u', '<ruby>$1<rt>$2</rt></ruby>', $content );
// 強調を変換
$content = preg_replace_callback( '/\[(.*?)\]/u', function( $match ) {
	$letters = $match[1];
	$emphasized = '';
	for ( $i = 0, $l = mb_strlen( $letters, 'utf-8' ); $i < $l; $i++) {
		$letter = mb_substr( $letters, $i, 1, 'utf-8' );
		var_dump( $letter );
		$emphasized .= sprintf( '<ruby>%s<rt>・</rt></ruby>', $letter );
	}
	return $emphasized;
}, $content );
// 改行の中身を全部pタグに
$content = implode( "\n", array_map( function( $line ) {
	return sprintf( '<p>%s</p>', $line );
}, explode( "\n", $content ) ) );
ob_start();
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title><?= $title ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="./style.css" />
</head>
<body>
<header>
<h1><?= $title ?></h1>
</header>
<div class="main">

<?= $content ?>

</div>
<footer>
<a href="https://github.com/fumikito/noveljam2018"><i class="fa fa-github"></i></a>
<p>
 &copy; 2018 <a href="https://takahashifumiki.com">Takahashi Fumiki</a>
</p>
</footer>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$target = __DIR__ . '/docs/index.html';
if ( ! is_dir( dirname( $target ) ) ) {
	mkdir( dirname( $target ) );
}
file_put_contents( $target, $html );