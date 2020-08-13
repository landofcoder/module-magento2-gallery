<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Gallery\Model\Config\Source;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Module\Dir;

class Themes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * [$_moduleDir description]
     * @var [type]
     */
    protected $_moduleDir;

    /**
     * [$_filesystem description]
     * @var [type]
     */
    protected $_filesystem;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        Dir $moduleDir
    ){
        $this->_filesystem = $filesystem;
        $this->_moduleDir = $moduleDir;
    } 
    private function _listDirectories($path, $fullPath = false)
    {
        $result = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DIRECTORY_SEPARATOR . $entry)){
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DIRECTORY_SEPARATOR . $entry;
                }
                $result[] = $entry;
            }
            unset($entry);
            closedir($dir);
        }

        return $result;
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $file = $this->_moduleDir->getDir('Lof_Gallery',Dir::MODULE_VIEW_DIR). DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'css';
        //$file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/lof/gallery/view/frontend/web/css');
        $directories = $this->_listDirectories($file);
        $templates =  array();
        foreach($directories as $key => $template){
            if($template != "images"){
                $templates[] = array('value' => $template, 'label'=>$template);
            }
        }
        return $templates;
    }
}