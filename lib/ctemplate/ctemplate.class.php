<?php

class CTemplate
{
    private $template_dir;
    private $compile_dir;
    private $tpl_vars;

    function __construct($template_dir = './template/', $compile_dir = './compile/')
    {
        $this->template_dir = rtrim($template_dir, '/').'/';
        $this->compile_dir = rtrim($compile_dir, '/').'/';
        $this->tpl_vars = array();
    }

    public function render($file_name, $data)
    {
        if (!is_array($data))
            return FALSE;
        $this->tpl_vars = $data;

        $tpl_file = $this->template_dir.$file_name;
        if (!file_exists($tpl_file))
        {
            return FALSE;
        }

        $com_file = $this->compile_dir."com_".basename($tpl_file).'.php';
        if (!file_exists($com_file) || filemtime($com_file) < filemtime($tpl_file))
        {
            $rep_content = $this->tpl_replace(file_get_contents($tpl_file));
            $handle = fopen($com_file, 'w+');
            fwrite($handle, $rep_content);
            fclose($handle);
        }
        require $com_file;
    }

    private function tpl_replace($content)
    {
        $pattern = array(
            '/<\{\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[(\S*)\])*\s*\}>/i',//匹配变量
            '/<\{\s*if\s*(.+?)\s*\}>(.+?)<\{\s*\/if\s*\}>/ies',//匹配if语句
            '/<\{\s*else\s*if\s*(.+?)\s*\}>/ies',//匹配else if语句
            '/<\{\s*else\s*\}>/is',//匹配else语句
            '/<\{\s*include\s+[\(]?[\"\']?(.+?)[\"\']?[\)]?\s*\}>/ie',//匹配include
            '/<\{f\s+(.+?)\s*\}>/is',//匹配使用函数规则
            '/<\{\s*loop\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>(.+?)<\{\s*\/loop\s*\}>/is',
            '/<\{\s*loop\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*=>\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}>(.+?)<\{\s*\/loop\s*\}>/is'
        );

        $replacement = array(
            '<?php echo $this->tpl_vars["${1}"]${2}; ?>',//替换变量
            '$this->stripvtags(\'<?php if(${1}){ ?>\', \'${2}<?php } ?>\')',//替换if语句
            '$this->stripvtags(\'<?php } elseif(${1}){ ?>\', "")',//替换else if语句
            '<?php } else { ?>',//替换else语句
            'file_get_contents($this->template_dir."${1}")',//替换include
            '<?php ${1} ?>',//替换PHP函数
            '<?php foreach($this->tpl_vars["${1}"] as $this->tpl_vars["${2}"]) { ?>${3}<?php } ?>',
            '<?php foreach($this->tpl_vars["${1}"] as $this->tpl_vars["${2}"] => $this->tpl_vars["${3}"]) { ?>${4}<?php } ?>'
        );

        $rep_content = preg_replace($pattern, $replacement, $content);
        $match = '/<\{([^(\}>)]{1,})\}>/';
        if (preg_match($match, $rep_content))
        {
            $rep_content = $this->tpl_replace($rep_content);
        }
        return $rep_content;
    }

    private function stripvtags($expr, $statement='')
    {
        $var_pattern = '/\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[(\S*)\])*\s*/is';//匹配变量
        $expr = preg_replace($var_pattern, '$this->tpl_vars["${1}"]${2}', $expr);//替换变量
        $expr = str_replace("\\\"", "\"", $expr);
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr.$statement;
    }

}

?>
