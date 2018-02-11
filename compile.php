#!/usr/bin/env php
<?php

if ( isset( $argv[1] ) ) {
	die( 'タイトルは決定済みです。' );
}
$title = 'オートマティック クリミナル';

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
		$emphasized .= sprintf( '<ruby>%s<rt>・</rt></ruby>', $letter );
	}
	return $emphasized;
}, $content );
// 改行の中身を全部タグに変換
$content = implode( "\n", array_map( function( $line ) {
	$first_letter = mb_substr( $line, 0, 1, 'utf-8' );
	$second_letters = function( $line ) {
		return mb_substr( $line, 1, mb_strlen( $line ) - 1 );
	};
	switch ( $first_letter ) {
		case '>':
			return sprintf( '<blockquote>%s</blockquote>', $second_letters( $line ) );
		case '<':
			return sprintf( '<p class="right">%s</p>', $second_letters( $line ) );
		case '~':
			return sprintf( '<p class="center">%s</p>', $second_letters( $line ) );
		case '':
			return '<p>&nbsp;</p>';
		default:
			return sprintf( '<p>%s</p>', $line );
	}
}, explode( "\n", $content ) ) );
ob_start();
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title><?= $title ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" media="screen" />
<link rel="stylesheet" href="./style.css" media="screen" />
<link rel="stylesheet" href="./print.css" media="print" />
<meta property="og:url" content"http://github.takahashifumiki.com/noveljam2018/">
<meta property="og:type" content"article">
<meta property="og:title" content"<?= $title ?>">
<meta property="og:image" content"http://github.takahashifumiki.com/noveljam2018/cover.jpg">
<meta property="og:description" content="NovalJam 2018に著者として参加する高橋文樹のリポジトリです。">
<meta property="fb:app_id" content="264573556888294">
</head>
<body>
<header>
<h1><?= $title ?></h1>
</header>
<div class="main">

<h1 class="print-title">
<?= $title ?>
<small>高橋文樹</small>
</h1>

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