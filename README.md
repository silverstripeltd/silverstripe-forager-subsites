# silverstripe-forager-subsites

Due to the way that filtering works with (eg) Elastic Enterprise Search, you may want to split
each subsite's content into a separate engine. To do so, you can use the following
configuration:

```yaml
SilverStripe\Forager\Service\IndexConfiguration:
  indexes:
    content-subsite0:
      subsite_id: 0
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
      subsite_id: 4 # or you can use environment variable such as 'NAME_OF_ENVIRONMENT_VARIABLE'
      includeClasses:
        Page:
          <<: *page_defaults
          My\Other\Class:
          <<: *other_class_defaults

```

Note the syntax to reduce the need for copy-paste if you want to duplicate the
same configuration across.

__Additional note__:
> In the sample above, if the data object (My\Other\Class) does not have a subsite ID,  then it will be included in the indexing as it is explicitly defined in the index configuration

This is handled via `SubsiteIndexConfigurationExtension` - this logic could be
replicated for other scenarios like languages if required.
