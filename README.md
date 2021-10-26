# Contao Backend Field Dependency

In addition to palettes and sub-palettes, this extension adds a new field property (`dependsOn`) for DCA's where conditions can be defined to display the field.

#### Condition based on another field and its value:
```php
'field1'  => [
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'eval'       => ['tl_class' => 'w50 m12'],
    'sql'        => "char(1) NOT NULL default '0'",
],
'field2'  => [
    'exclude'    => true,
    'inputType'  => 'text',
    'eval'       => ['maxlength'=>64, 'tl_class'=>'w50'],
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
    'eval'       => ['maxlength'=>64, 'tl_class'=>'w50'],
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        'field1' => static function($fieldName, $objModel) {
            // $fieldName = field1
            // "field1" is automatically supplemented with the field evaluation "submitOnChange = true" (autoSubmit = true).
            // Return true = show / false = hide
            return $objModel->{$fieldName} == 1;  
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
    'eval'       => ['maxlength'=>64, 'tl_class'=>'w50'],
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        static function($fieldName, $objModel, &$arrEvaluationFields) {
            // $fieldName = 0
            // 'field1' must be extended independently with the field evaluation 'submitOnChange = true' or added via the third parameter ($arrFields) (autoSubmit = true).
            $arrEvaluationFields[] = 'field1';
            
            // Return true = show / false = hide       
            return $objModel->field1 == 1; 
        }
    ]
],
```

#### Multiple conditions:
```php
'field1'  => [
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'eval'       => ['tl_class' => 'w50 m12'],
    'sql'        => "char(1) NOT NULL default '0'",
],
'field2'  => [
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'eval'       => ['tl_class' => 'w50 m12'],
    'sql'        => "char(1) NOT NULL default '0'",
],
'field3'  => [
    'exclude'    => true,
    'inputType'  => 'text',
    'eval'       => ['maxlength'=>64, 'tl_class'=>'w50'],
    'sql'        => "varchar(64) NOT NULL default ''",
    'dependsOn'  => [
        'field2' => 1, 
        'field3' => 1 // Both fields must be checked to display this field
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
`tables` | (see above) | List of tables where `dependsOn` may be taken into account. If you add your own tables, the default settings will be overwritten and you will have to set them yourself.
