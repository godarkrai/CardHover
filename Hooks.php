<?php

namespace Liquipedia\CardHover;

use MediaWiki\Linker\LinkRenderer;
use MWNamespace;
use Title;

class Hooks {

	private static $filePaths = [];

	private static function getFilePath( $target ) {
		$text = $target->getText();
		if ( !array_key_exists( $text, self::$filePaths ) ) {
			global $wgOut;
			$result = strip_tags( $wgOut->parseInline( '<p>{{#show:' . $target->getText() . '|?has filepath|link=none}}</p>' ) );
			$result = str_replace( '&#58;', ':', $result );
			if ( !empty( $result ) ) {
				self::$filePaths[ $text ] = $result;
			} else {
				self::$filePaths[ $text ] = '';
			}
		}
		return self::$filePaths[ $text ];
	}

	public static function onHtmlPageLinkRendererBegin( LinkRenderer $linkRenderer, $target, &$text, &$extraAttribs, &$query, &$ret ) {
		if ( empty( $query ) && $target instanceof Title && $target->getNamespace() === NS_MAIN && $target->exists() && in_array( MWNamespace::getCanonicalName( NS_CATEGORY ) . ':Cards', array_keys( $target->getParentCategories() ) ) ) {
			$url = self::getFilePath( $target );
			if ( !empty( $url ) ) {
				$extraAttribs[ 'data-img' ] = $url;
				if ( array_key_exists( 'class', $extraAttribs ) ) {
					$extraAttribs[ 'class' ] .= ' hovercard';
				} else {
					$extraAttribs[ 'class' ] = 'hovercard';
				}
			}
		}
	}

	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.cardHover' );
		return true;
	}

}
