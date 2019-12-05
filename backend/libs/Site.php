<?php

class Site extends Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->folder = PATH_SITES;
        parent::__construct();
    }

    function getBlocksByCategory($codeCategory) {
        $path = $codeCategory . DS;
        $arBlocks = parent::getBlocksInFolder($path);
        return $arBlocks;
    }

    function getDataBlock($nameBlock) {
        $pathBlock = $nameBlock;
        return parent::getDataBlock($pathBlock);
    }

    function getDataBlocksCategory($category) {

    }

    function getCodeCategory($manifest) {
        return $manifest['code'];
    }

    function getTitleCategory($manifest) {
        return $manifest['name'];
    }
}