<?php

/*
 * @param templateDir 未编译模板目录
 * @param compileDir  编译好的模板目录
 * @param currentTemp 未编译模板文件名
 * @param outputHtml  未编译模板文件内容
 * @param varPool     用来储存变量的变量池
 */
class Template
{
    private $templateDir;
    private $compileDir;
    private $leftTag = '{#';
    private $rightTag = '#}';
    private $currentTemp = '';
    private $outputHtml;
    private $varPool = array();

    function __construct($templateDir, $compileDir, $leftTag = null, $rightTag = null)
    {
        $this->templateDir = $templateDir;
        $this->compileDir = $compileDir;
        if (!empty($leftTag)) $this->leftTag = $leftTag;
        if (!empty($rightTag)) $this->rightTag = $rightTag;
    }

    function assign($tag, $var)
    {
        $this->varPool[$tag] = $var;
    }

    function getVar($tag)
    {
        return $this->varPool[$tag];
    }

    /*
     * 拼接模板绝对路径并读取内容
     */
    function getSourceTemplate($templateName, $ext = '.html')
    {
        $this->currentTemp = $templateName;
        $sourceFilename = $this->templateDir . $this->currentTemp . $ext;
        $this->outputHtml = file_get_contents($sourceFilename);
    }

    /*
     * 把模板的内容正则替换成以echo的形式输入并以编译好的内容储存
     */
    function compileTemplate()
    {
        $pattern = '/' . preg_quote($this->leftTag);
        $pattern .= ' *\$([a-zA-Z_*]+) *';
        $pattern .= preg_quote($this->rightTag) . '/';

        //替换带有{#...#}的内容
        $this->outputHtml = preg_replace($pattern, '<?php echo $this->getVar(\'$1\') ?>', $this->outputHtml);
        //编译好的模板以md5命名保存
        $compiledFilename = $this->compileDir . md5($this->currentTemp) . '.html';
        file_put_contents($compiledFilename, $this->outputHtml);
    }

    /*
     * @param templateNanme 模板名
     * @param temp_filetime 最后一次模板修改的时间
     * @param comp_filetime 最后一次编译好的模板修改时间
     */
    function display($templateName = null, $ext = '.html')
    {
        $temp_file_dir = dirname(__DIR__) . '/view/' . $templateName . $ext;
        $comp_file_dir = dirname(__DIR__) . '/compiled/' . md5($templateName) . $ext;

        //检查是否重新需要编译，如果是，重新编译；否，则直接读取

        if (file_exists($comp_file_dir)) {
            $temp_filetime = filemtime($temp_file_dir);
            $comp_filetime = filemtime($comp_file_dir);
            if ($temp_filetime > $comp_filetime) {
                $this->getSourceTemplate($templateName, $ext);
                $this->compileTemplate();
            }
        } else {
            if (!empty($templateName)) {
                $this->getSourceTemplate($templateName, $ext);
                $this->compileTemplate();
            }
        }
        include_once $this->compileDir . md5($templateName) . $ext;
    }
}
