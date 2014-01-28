<?php
/**
 * Tags for using Tr8n Translation Markup Language inside MediaWiki
 *
 * @file
 * @ingroup Extensions
 * @author Michal Berkovich
 */

if ( !defined( 'MEDIAWIKI' ) ) die();

$wgExtensionCredits['Tr8n'][] = array(
    'path' => __FILE__,
    'name' => 'Parser for tml tags',
    'descriptionmsg' => 'Tags for using Tr8n Translation Markup Language inside MediaWiki',
    'version' => '2014-01-27',
    'author' => 'Michael Berkovich',
    'url' => 'https://www.mediawiki.org/wiki/Extension:Tr8n',
);

require_once('vendor/tr8n/tr8n-client-sdk/library/Tr8n.php');

$wgHooks['ParserFirstCallInit'][] = 'tr8nSetup';
$wgHooks['BeforePageDisplay'][] = 'tr8nBeforeDisplay';
$wgHooks['NormalizeMessageKey'][] = 'tr8nNormalizeMessageKey';

//$wgTr8nServerUrl =  "http://localhost:3000";
//$wgTr8nApplicationKey =  "default";
//$wgTr8nApplicationSecret =  "e6ee64803c7b1cf51";

/**
 * @param $parser Parser
 * @return bool
 */
function tr8nSetup( &$parser ) {
    global $wgTr8nServerUrl;
    global $wgTr8nApplicationKey;
    global $wgTr8nApplicationSecret;

    tr8n_init_client_sdk($wgTr8nServerUrl, $wgTr8nApplicationKey, $wgTr8nApplicationSecret);

    global $wgOut;
    $wgOut->addScript('<script type="text/javascript" src="' . \Tr8n\Config::instance()->application->jsBootUrl() . '"></script>'. "\n");

    $parser->setHook( 'tr8n:tr', 'tr8nTranslateRender' );
    $parser->setHook( 'tr8n:trh', 'tr8nTranslateHtmlRender' );
    $parser->setHook( 'tr8n:block', 'tr8nBlockRender' );
    return true;
}

function tr8nBeforeDisplay( $out ) {
    \Tr8n\Config::instance()->application->submitMissingKeys();
    return true;
}

function tr8nNormalizeMessageKey($key, $useDB, $langCode, $transform ) {
    return "a" . $transform;
}

function tr8nBlockRender( $input, array $args, Parser $parser, PPFrame $frame ) {
    if (\Tr8n\Config::instance()->isDisabled()) {
        return $parser->recursiveTagParse($input);
    }

    $options = array();
    if (isset($args['source'])) {
        $options['source'] = $args['source'];
    }
    if (isset($args['locale'])) {
        $options['locale'] = $args['locale'];
    }
    \Tr8n\Config::instance()->beginBlockWithOptions($options);
    $content = $parser->recursiveTagParse( $input );
    \Tr8n\Config::instance()->finishBlockWithOptions();
    return $content;
}

function tr8nPrepareAttributes($args) {
    $tokens = array();
    $options = array();

    if (is_string($args)) $args = array();

    $description = isset($args['description']) ? $args['description'] : null;
    if ($description == null) {
        $description = isset($args['context']) ? $args['context'] : null;
    }

    if (isset($args['tokens'])) {
        $tokens = json_decode($args['tokens'], true);
    }

    if (isset($args['options'])) {
        $options = json_decode($args['options'], true);
    }

    foreach($args as $name => $value) {
        if (\Tr8n\Utils\StringUtils::startsWith('token:', $name)) {
            $parts = explode('.', substr($name, 6));
            if (count($parts) == 1) {
                $tokens[$parts[0]] = $value;
            } else {
                if (!isset($tokens[$parts[0]])) $tokens[$parts[0]] = array();
                \Tr8n\Utils\ArrayUtils::createAttribute($tokens[$parts[0]], array_slice($parts,1), $value);
            }
        } else if (\Tr8n\Utils\StringUtils::startsWith('option:', $name)) {
            $parts = explode('.', substr($value, 7));
            if (count($parts) == 1) {
                $options[$parts[0]] = $value;
            } else {
                if (!isset($options[$parts[0]])) $options[$parts[0]] = array();
                \Tr8n\Utils\ArrayUtils::createAttribute($options[$parts[0]], array_slice($parts,1), $value);
            }
        }
    }

    if (isset($args['split'])) {
        $options['split'] = $args['split'];
    }

    return array("description" => $description, "tokens" => $tokens, "options" => $options);
}

function tr8nTranslateRender( $input, array $args, Parser $parser, PPFrame $frame ) {
    $parser->disableCache();

    if (\Tr8n\Config::instance()->isDisabled()) {
        return $input;
    }

    if ($input == null) return $input;
    $label = trim($input);
    if ($label == "") return $label;

    $args = tr8nPrepareAttributes($args);

    try {
        return tr($label, $args["description"], $args["tokens"], $args["options"]);
    } catch(\Tr8n\Tr8nException $e) {
        \Tr8n\Logger::instance()->info($e->getMessage());
        return $input;
    }
}

function tr8nTranslateHtmlRender( $input, array $args, Parser $parser, PPFrame $frame ) {
    $parser->disableCache();

    if (\Tr8n\Config::instance()->isDisabled()) {
        return $input;
    }

    if ($input == null) return $input;
    $label = trim($input);
    if ($label == "") return $label;

    $args = tr8nPrepareAttributes($args);

    $content = $parser->recursiveTagParse( $label );

    try {
        if ($content != strip_tags($content)) {
            return trh($content, $args["description"], $args["tokens"], $args["options"]);
        }
        return tr($content, $args["description"], $args["tokens"], $args["options"]);
    } catch(\Tr8n\Tr8nException $e) {
        \Tr8n\Logger::instance()->info($e->getMessage());
        return $input;
    }
}

