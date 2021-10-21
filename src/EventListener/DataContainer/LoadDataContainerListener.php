<?php
declare(strict_types=1);

namespace Oveleon\ContaoBeFieldDependency\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        if('edit' !== Input::get('act') || !in_array($table, Controller::getContainer()->getParameter('contao_be_field_dependency.tables')))
        {
            return;
        }

        $dc = $this->simulateDataContainer($table);

        $strClass = $GLOBALS['TL_MODELS'][$dc->table];
        $objModel = $strClass::findById($dc->id);

        if($dcaFields = $GLOBALS['TL_DCA'][$dc->table]['fields'])
        {
            $dependentFields = [];

            foreach ($dcaFields as $fieldName => $arrField)
            {
                $dependencies = $arrField['dependsOn'] ?? null;

                if(\is_array($dependencies))
                {
                    foreach ($dependencies as $conditionFieldName => $condition)
                    {
                        $dependentFields[] = $conditionFieldName;

                        // Check condition
                        if(is_callable($condition))
                        {
                            $blnDisable = !$condition($conditionFieldName, $objModel);
                        }
                        else
                        {
                            $blnDisable = $objModel->{$conditionFieldName} != $condition;
                        }

                        if($blnDisable)
                        {
                            unset($GLOBALS['TL_DCA'][$dc->table]['fields'][$fieldName]);
                        }
                    }
                }
            }

            if(Controller::getContainer()->getParameter('contao_be_field_dependency.autoSubmit') && $dependentFields = array_filter($dependentFields))
            {
                foreach ($dependentFields as $field)
                {
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['eval']['submitOnChange'] = true;
                }
            }
        }
    }

    private function simulateDataContainer($table): \stdClass
    {
        $dc = new \stdClass();
        $dc->table = $table;
        $dc->id = Input::get('id');

        return $dc;
    }
}
