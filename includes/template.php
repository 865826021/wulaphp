<?php

/**
 * merge arguments.
 *
 * @param array $args the array to be merged
 * @param array $default the array to be merged with
 * @return array the merged arguments array
 */
function merge_args($args, $default) {
    $_args = array ();
    foreach ( $args as $key => $val ) {
        if (is_numeric ( $val ) || is_bool ( $val ) || ! empty ( $val )) {
            $_args [$key] = $val;
        }
    }
    foreach ( $default as $key => $val ) {
        if (! isset ( $_args [$key] )) {
            $_args [$key] = $val;
        }
    }
    return $_args;
}

/**
 * 解析smarty参数.
 *
 * 将参数中 '" 去除比,如 '1' 转换为1.
 *
 * @param array $args 参数数组
 * @return array 解析后的参数
 */
function smarty_parse_args($args) {
    foreach ( $args as $key => $value ) {
        if (strpos ( $value, '_smarty_tpl->tpl_vars' ) !== false) {
            $args [$key] = trim ( $value, '\'"' );
        }
    }
    return $args;
}

/**
 * 将smarty传过来的参数转换为可eval的字符串.
 *
 * @param array $args
 * @return string
 */
function smarty_argstr($args) {
    $a = array ();
    foreach ( $args as $k => $v ) {
        $v1 = trim ( $v );
        if (empty ( $v1 ) && $v1 != '0' && $v1 != 0) {
            continue;
        }
        if ($v == false) {
            $a [] = "'$k'=>false";
        } else {
            $a [] = "'$k'=>$v";
        }
    }
    return 'array(' . implode ( ',', $a ) . ')';
}

/**
 * Smarty here modifier plugin.
 *
 * <code>
 * {'images/logo.png'|here}
 * </code>
 * 以上代表输出模板所在目录下的images/logo.png
 *
 * Type: modifier<br>
 * Name: here<br>
 * Purpose: 输出模板所在目录下资源的URL
 *
 * @staticvar string WEBROOT的LINUX表示.
 * @param array $params 参数
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_here($params, $compiler) {
    static $base = null;
    if ($base == null) {
        $base = str_replace ( DS, '/', WWWROOT );
    }
    $tpl = str_replace ( DS, '/', dirname ( $compiler->template->source->filepath ) );
    $tpl = str_replace ( $base, '', $tpl );
    $url = ! empty ( $tpl ) ? trailingslashit ( $tpl ) : '';
    return "'{$url}'." . $params [0] . '';
}

function cleanhtml2simple($text) {
    $text = str_ireplace ( array ('[page]',' ','　',"\t","\r","\n",'&nbsp;' ), '', $text );
    $text = preg_replace ( '#</?[a-z0-9][^>]*?>#msi', '', $text );
    return $text;
}

function smarty_modifiercompiler_clean($params, $compiler) {
    return 'cleanhtml2simple(' . $params [0] . ')';
}

function smarty_modifiercompiler_kk($params, $compiler) {
    $var = $params [0];
    return "'<pre style=\"margin:5px;padding:5px;overflow:auto;\">',var_export($var,true),'</pre>'";
}

function smarty_modifiercompiler_rstr($params, $compiler) {
    $str = array_shift ( $params );
    $cnt = 10;
    if (! empty ( $params )) {
        $cnt = intval ( array_shift ( $params ) );
    }
    $append = "''";
    if (! empty ( $params )) {
        $append = array_shift ( $params );
    }
    
    return "{$str}.{$append}.rand_str({$cnt}, 'a-z,A-Z')";
}

function smarty_modifiercompiler_rnum($params, $compiler) {
    $str = array_shift ( $params );
    $cnt = 10;
    if (! empty ( $params )) {
        $cnt = intval ( array_shift ( $params ) );
    }
    $append = "''";
    if (! empty ( $params )) {
        $append = array_shift ( $params );
    }
    
    return "{$str}.{$append}.rand_str({$cnt}, '0-9')";
}

function smarty_modifiercompiler_timediff($params, $compiler) {
    $cnt = time ();
    if (! empty ( $params )) {
        $cnt = array_shift ( $params );
    }
    return "timediff({$cnt})";
}

function smarty_modifiercompiler_timeread($params, $compiler) {
    return "readable_date({$params[0]})";
}

/**
 * Smarty checked modifier plugin.
 *
 * <code>
 * {'0'|checked:$value}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: checked<br>
 * Purpose: 根据值输出checked="checked"
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_checked($value, $compiler) {
    return "((is_array($value[1]) && in_array($value[0],$value[1]) ) || $value[0] == $value[1])?'checked = \"checked\"' : ''";
}

/**
 * Smarty status modifier plugin.
 *
 * <code>
 * {value|status:list}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: status<br>
 * Purpose: 将值做为LIST中的KEY输出LIST对应的值
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_status($status, $compiler) {
    if (count ( $status ) < 2) {
        trigger_error ( 'error usage of status', E_USER_WARNING );
        return "'error usage of status'";
    }
    $key = "$status[0]";
    $status_str = "$status[1]";
    $output = "$status_str" . "[$key]";
    return $output;
}

function smarty_modifiercompiler_random($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of random', E_USER_WARNING );
        return "'error usage of random'";
    }
    $output = "is_array({$ary[0]})?{$ary[0]}[array_rand({$ary[0]})]:''";
    return $output;
}

function smarty_modifiercompiler_render($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of render', E_USER_WARNING );
        return "''";
    }
    $render = $ary [0];
    array_shift ( $ary );
    $args = empty ( $ary ) ? '' : smarty_argstr ( $ary );
    return "{$render} instanceof \wulaphp\mvc\view\Renderable?{$render}->render($args):{$render}";
}