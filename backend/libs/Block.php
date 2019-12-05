<?php

class Block extends Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->folder = PATH_BLOCKS;
        parent::__construct();
    }

    function getBlocksByCategory($codeCategory) {
        $path = '';//$this->pathBlock;
        $arBlocks = parent::getBlocksInFolder($path);
        foreach ($arBlocks as $key => $block) {
            if ($block['section'] != $codeCategory) {
                unset($arBlocks[$key]);
            }
        }
        return $arBlocks;
    }

    function getDataBlock($nameBlock) {
        $pathBlock = $nameBlock;
        return parent::getDataBlock($pathBlock);
    }

    function getCodeCategory($manifest) {
        return $manifest['fields']['SECTIONS'];
    }

    function getTitleCategory($manifest) {
        $category = array(
            'cover' => 'Обложка',
            'about' => 'О проекте',
            'title' => 'Заголовок',
            'text' => 'Текстовый блок',
            'video' => 'Видео',
        );
        return $category[$manifest['fields']['SECTIONS']];
    }
}