# SilverStripe Forager Subsites

By default, all subsites content will be indexed together. In some cases you may want to split
each subsite's content into a separate engine or only want to index a single subsite.

## Configuration

### Basic Usage

To split each subsite into its own index, use the following configuration:

```yaml
SilverStripe\Forager\Service\IndexConfiguration:
  indexes:
    content-subsite0:
      subsite_id: 0
      context: subsite
      includeClasses:
        Page: &page_defaults
          fields:
            title: true
            content: true
            summary: true
        My\Other\Class: &other_class_defaults
          fields:
            title:
              property: Title
            summary:
              property: Summary
    content-subsite4:
      subsite_id: 4 # or you can use an environment variable such as 'NAME_OF_ENVIRONMENT_VARIABLE'
      context: subsite
      includeClasses:
        Page:
          <<: *page_defaults
        My\Other\Class:
          <<: *other_class_defaults
```

**Note:** The YAML anchor syntax (`&page_defaults` and `<<: *page_defaults`) reduces the need for copy-paste when you want to duplicate the same configuration across multiple indexes.

__Additional note__:
> In the sample above, if the data object (My\Other\Class) does not have a subsite ID, then it will be included in the indexing as it is explicitly defined in the index configuration

### Configuration Options

#### Subsite ID

The `subsite_id` key specifies which subsite's content to index.

- `subsite_id: all` Indexes content from all subsites.
- `subsite_id: <number>` Indexes content only for the specified subsite (e.g. `subsite_id: 0` for the main site, `subsite_id: 4` for subsite with ID 4).

For some jobs we require the context to be set to `subsite` in order for it to correctly filter.

#### Context

The `context` key controls how the IndexDataContextProvider sets the state during indexing operations.

Setting the context to `subsite` will set the subsite state during indexing to the what is specified within the index data.