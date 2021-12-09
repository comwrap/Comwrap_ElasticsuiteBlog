# Extension is not acvitely maintaining.
If you need the module and want to maintain the repo, please drop me a message. 
Twitter: @nuzil

## ElasticSuite Blog search for Magefan Magento 2 Blog


This module connecting between each other [ElasticSuite](https://github.com/Smile-SA/elasticsuite) search extension and  [Magefan](https://magefan.com/magento2-extensions) [Magento 2 Blog extension](https://magefan.com/magento2-blog-extension)  (GitHub: [magefan/module-blog](https://github.com/magefan/module-blog))

It allows to index Magento 2 Blog posts into the search engine and display them into the autocomplete results, and also on the search result page.

### Requirements

* For version 1.x.x: Magento Community Edition 2.2.* or Magento Enterprise Edition 2.2.*
* For version 2.x.x: Magento Community Edition 2.3.* or Magento Enterprise Edition 2.3.*

The module requires :

- [ElasticSuite](https://github.com/Smile-SA/elasticsuite)
- [Magefan Blog](https://github.com/magefan/module-blog)

### How to use

1. Enable it

``` bin/magento module:enable Comwrap_ElasticsuiteBlog ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

4. Process a full reindex of the Blog Post search index

``` bin/magento index:reindex elasticsuite_blog_fulltext ```

