<?php

abstract class Entity
{
    static $dir_site;
    protected $pathBlock;
    protected $urlBlock;
    protected $folder;

    public function __construct()
    {
        self::$dir_site = realpath(__DIR__ . DS . '..' . DS . '..' . DS) . DS;
        $this->pathBlock = self::$dir_site . $this->folder . DS;
        $this->urlBlock = SITE_URL . $this->folder . '/';
    }

    function getAllCategories() {
        $path = $this->pathBlock;

        //получение списка папок-блоков в /blocks/
        $arBlocks = self::getFolders($path);

        $arSection = [];
        foreach ($arBlocks as $block) {
            $manifest = file_get_contents($path . $block . DS . 'config.json');
            $manifest = json_decode($manifest, true);
            $categoryCode = $this->getCodeCategory($manifest);
            $arSection['listCat'][$categoryCode] = $categoryCode;
            $arSection['titleCat'][$categoryCode] = $this->getTitleCategory($manifest);
        }

        return $arSection;
    }

    function getBlocksInFolder($pathSection) {
        //получение списка папок-блоков в /blocks/
        $path = $this->pathBlock . $pathSection;
        $nameBlocks = self::getFolders($path);

        //создание массива блоков
        $data = array();
        foreach ($nameBlocks as $block) {
            $manifest = file_get_contents($path . $block . DS . 'config.json');
            $manifest = json_decode($manifest, true);
            //        print_r($manifest);

            $section = $manifest['fields']['SECTIONS'];

            $codeBlock = $manifest['code'];
            $nameBlock = $manifest['fields']['NAME'];
            $previewBlock = $manifest['fields']['PREVIEW'];
            $data[] = [
                'code' => $codeBlock,
                'name' => $nameBlock,
                'section' => $section,
                'preview' => $this->urlBlock . $pathSection . $block . DS . $previewBlock . '?v' . rand(1, 100)
            ];
        }

        return ($data);
    }

    function getDataBlock($pathBlock) {
        $dirBlock = $this->pathBlock . $pathBlock . DS;
        $urlBlock = $this->urlBlock . $pathBlock . '/';

        //файл манифеста
        $config = file_get_contents($dirBlock . 'config.json');
        $data = json_decode($config);

        //файл с разделом CONTENT манифеста
        $content = file_get_contents($dirBlock . 'index.php');
        //замена в content HTML_ALIAS_URL (#SITE_PATH) на URL сайта
        $content = str_replace(HTML_ALIAS_URL, $urlBlock, $content);
        $data->{'fields'}->{'CONTENT'} = $content;

        // к code блока обавим code сайта
        $data->{'code'} = str_replace('/', '.' , $pathBlock);

        //добавление URL к preview, assets - js, css
        $data->{'fields'}->{'PREVIEW'} =  $urlBlock . $data->{'fields'}->{'PREVIEW'};

        $js = $data->{'manifest'}->{'assets'}->{'js'};
        if ($js) {
            foreach ($js as $key => $value) {
                if (strpos($data->{'manifest'}->{'assets'}->{'js'}[$key], 'http') === false) {
                    $data->{'manifest'}->{'assets'}->{'js'}[$key] = $urlBlock . $value;
                }
            }
        }

        $css = $data->{'manifest'}->{'assets'}->{'css'};
        if ($css) {
            foreach ($css as $key => $value) {
                if (strpos($data->{'manifest'}->{'assets'}->{'css'}[$key], 'http') === false) {
                    $data->{'manifest'}->{'assets'}->{'css'}[$key] = $urlBlock . $value;
                }
            }
        }

        return $data;
    }

    function getDataBlocksInCategory($category) {
        $blocks = self::getFolders($this->pathBlock . $category . DS);
        $arDataBlocks = [];
        foreach ($blocks as $block) {
            $arDataBlocks[] = $this->getDataBlock($category . DS . $block);
        }
        return $arDataBlocks;
    }

    static function getFolders ($path) {
        $arFolders = [];
        $skip = array('.', '..');
        $dirs = scandir($path);
        foreach($dirs as $dir) {
            if(!in_array($dir, $skip) && is_dir($path . $dir))
                $arFolders[] = $dir;
        }
        return $arFolders;
    }

    function getTitleCategory($manifest) {}

    function getCodeCategory($manifest) {}

    function getBlocksByCategory($codeCategory) {}
}