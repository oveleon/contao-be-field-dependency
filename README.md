# Contao Backend Field Dependency

In addition to palettes and sub-palettes, this extension adds a new field property (`dependsOn`) for DCA's where conditions can be defined to display the field.

#### Condition based on another field and its value:
```php
'field1'  => [
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'eval'       => array('tl_class' => 'w50 m12'),
    'sql'        => "char(1) NOT NULL default '0'",
],
'field2'  => [
    'exclude'    => true,
    'inputType'  => 'text',
    'eval'       => array('maxlength'=>64, 'tl_class'=>'w50'),
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        'field1' => 1 // Displays this field only if the checkbox (field1) has been selected.
    ]
],
```

#### Condition based on another field and a custom callback:
```php
'field2'  => [
    'exclude'    => true,
    'inputType'  => 'text',
    'eval'       => array('maxlength'=>64, 'tl_class'=>'w50'),
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        'field1' => static function($fieldName, $objModel) {
            // $fieldName = field1
            return $objModel->{$fieldName} == 1; // true = show / false = hide 
        }
    ]
],
```

With this variant, the specification of the field name to be reacted to is optional. Please note that the first parameter corresponds to the given key.\
For example:
```php
'field2'  => [
    'exclude'    => true,
    'inputType'  => 'text',
    'eval'       => array('maxlength'=>64, 'tl_class'=>'w50'),
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        static function($fieldName, $objModel) {
            // $fieldName = 0
            // ... 
        }
    ]
],
```

### Configuration
Basic settings must be maintained via the config/config.yml file.

```yaml
contao_be_field_dependency:
    autoSubmit: true
    tables:
        - tl_article
        - tl_content
        - tl_files
        - tl_form_field
        - tl_form
        - tl_image_size_item
        - tl_image_size
        - tl_layout
        - tl_member_group
        - tl_member
        - tl_module
        - tl_opt_in
        - tl_page
        - tl_style
        - tl_style_sheet
        - tl_theme
        - tl_user_group
        - tl_user
```

Parameter | Default | Description
---------- | ----------- | -----------
`autoSubmit` | true | Referenced fields in `dependsOn` automatically get the "submitOnChange" evaluation set. Should this parameter be set to `false`, the evaluation must be added manually.
`tables` | (see above) | List of tables where `dependsOn` may be taken into account. When adding your own tables, the defaults will be overwritten, please add all tables again.
