<?php


namespace Wheregroup\AsseticFilterSassc;


use Assetic\Filter\Sass\ScssFilter;

class SasscFilter extends ScssFilter
{
    private $initializing;

    public function __construct($sasscPath = '/usr/bin/sassc')
    {
        $this->initializing = true;
        parent::__construct($sasscPath, null);
        $this->initializing = false;

        // prevent parent's automatic --scss option addition based on file extension
        $this->setScss(false);
        // undo parent constructor's initialization for unsupported --cache-location option
        $this->setCacheLocation(null);
    }

    public function setScss($scss)
    {
        if ($this->initializing) {
            return;
        }
        if ($scss) {
            throw new \InvalidArgumentException("Implementation does not support --scss switch");
        }
        parent::setScss(false);
    }

    public function setNoCache($noCache)
    {
        if ($noCache) {
            throw new \InvalidArgumentException("Implementation does not support --no-cache switch");
        }
        parent::setNoCache(false);
    }

    public function setCacheLocation($cacheLocation)
    {
        if ($cacheLocation) {
            throw new \InvalidArgumentException("Implementation does not support --cache-location switch");
        }
        parent::setCacheLocation(null);
    }
}
