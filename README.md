## Comwrap Blog Posts Search

This module is a plugin for [ElasticSuite](https://github.com/Smile-SA/elasticsuite).

It allows to index Blog posts into the search engine and display them into the autocomplete results, and also on the search result page.

### Requirements

The module requires :

- [ElasticSuite](https://github.com/Smile-SA/elasticsuite) > 2.1.*

### How to use

1. Enable it

``` bin/magento module:enable Comwrap_ElasticsuiteBlog ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

4. Process a full reindex of the Blog Post search index

``` bin/magento index:reindex elasticsuite_blog_fulltext ```

