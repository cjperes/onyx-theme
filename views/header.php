<?php
/**
 * The template for displaying the header.
 *
 * @package Onyx Theme
 */

use \Onyx\O;

?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<!------------------------------------
		@ ESPECIFICAÇÕES MOBILE/TABLETS
		-------------------------------------->

		<meta name="application-name" content="<?php O::print( 'name' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<meta name="mobile-web-app-capable" content="no">
		<meta name="apple-mobile-web-app-capable" content="no">

		<!------------------------------------
		@ CORES, ICONES E FAVICON
		-------------------------------------->

		<link rel="apple-touch-icon" sizes="180x180" href="<?php O::print( 'dir_uri' ); ?>/assets/images/icons/favicon-180.png">
		<link rel="icon" type="image/png" href="<?php O::print( 'dir_uri' ); ?>/assets/images/icons/favicon-32.png">
		<link rel="manifest" href="<?php O::print( 'dir_uri' ); ?>/site.webmanifest">
		<meta name="theme-color" content="#FFF">
		<meta name="apple-mobile-web-app-title" content="<?php O::print( 'dir_uri' ); ?>">
		<meta name="apple-mobile-web-app-status-bar-style" content="default">

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
