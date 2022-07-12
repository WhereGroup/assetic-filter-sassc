<?php


namespace Wheregroup\AsseticFilterSassc;


use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;
use Symfony\Component\Process\Process;

/**
 * Self-contained replacement for ScssFilter hierarchy
 * no longer present in replacement package
 * assetic/framework.
 *
 * Works with both original kriswallsmith/assetic 1.4.0 and
 * assetic/framework 1.4 or 2.0.x.
 *
 * Contents based on original kriswallsmith/assetic code copyright
 * 2010-2015 OpenSky Project Inc
 *
 * @see https://github.com/kriswallsmith/assetic/blob/master/LICENSE
 * @see https://github.com/kriswallsmith/assetic/blob/v1.4.0/src/Assetic/Filter/Sass/BaseSassFilter.php
 * @see https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Filter/BaseProcessFilter.php
 */
class SasscFilter implements FilterInterface
{
    protected $loadPaths = array();
    protected $binaryPath;
    protected $timeout;
    protected $style = 'nested';

    public function __construct($binaryPath = '')
    {
        $this->binaryPath = $binaryPath;
    }

    public function setLoadPaths(array $loadPaths)
    {
        $this->loadPaths = $loadPaths;
    }

    public function addLoadPath($loadPath)
    {
        $this->loadPaths[] = $loadPath;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $processArgs = array($this->binaryPath);
        if ($this->style) {
            $processArgs[] = '--style';
            $processArgs[] = $this->style;
        }
        if ($srcDir = $asset->getSourceDirectory()) {
            $processArgs[] = '--load-path';
            $processArgs[] = $srcDir;
        }
        foreach ($this->loadPaths as $loadPath) {
            $processArgs[] = '--load-path';
            $processArgs[] = $loadPath;
        }
        if (DIRECTORY_SEPARATOR === '\\') {
            $inputFile = FilesystemUtils::createTemporaryFile('sass');
            \file_put_contents($inputFile, $asset->getContent());
            $processArgs[] = $inputFile;
        } else {
            $inputFile = false;
            $processArgs[] = '--stdin';
        }
        $process = new Process($processArgs);
        if (!$inputFile) {
            $process->setInput($asset->getContent());
        }
        if ($this->timeout !== null) {
            $process->setTimeout($this->timeout);
        }
        $exitCode = $process->run();
        if ($inputFile) {
            \unlink($inputFile);
        }
        if ($exitCode) {
            throw FilterException::fromProcess($process);
        }
        $asset->setContent($process->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
        // Nothing
    }

    public function setScss($scss)
    {
        if ($scss) {
            throw new \InvalidArgumentException("Implementation does not support --scss switch");
        }
    }

    public function setCacheLocation($cacheLocation)
    {
        if ($cacheLocation) {
            throw new \InvalidArgumentException("Implementation does not support --cache-location switch");
        }
    }
}
