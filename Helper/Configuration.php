<?php

namespace Comwrap\ElasticsuiteBlog\Helper;

use Smile\ElasticsuiteCore\Helper\AbstractConfiguration;


class Configuration extends AbstractConfiguration
{
    /**
     * Location of Elasticsuite bog post settings configuration.
     *
     * @var string
     */
    const CONFIG_XML_PREFIX = 'comwrap_elasticsuite_blog/post_settings';

    /**
     * Retrieve a configuration value by its key
     *
     * @param string $key The configuration key
     *
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_PREFIX . "/" . $key);
    }
}
