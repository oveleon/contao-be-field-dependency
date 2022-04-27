<?php
declare(strict_types=1);

namespace Oveleon\ContaoBeFieldDependency\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use Doctrine\DBAL\Connection;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(string $table): void
    {
        if(!$this->connection->getSchemaManager()->tablesExist($table) || !in_array($table, $this->getValidTables()))
        {
            return;
        }

        $dc = $this->simulateDataContainer($table);

        if(!($strClass = ($GLOBALS['TL_MODELS'][$dc->table] ?? null)))
        {
            return;
        }

        $objModel = $strClass::findById($dc->id);

        if($dcaFields = $GLOBALS['TL_DCA'][$dc->table]['fields'])
        {
            $dependentFields = [];

            foreach ($dcaFields as $fieldName => $arrField)
            {
                $dependencies = $arrField['dependsOn'] ?? null;

                if(\is_array($dependencies))
                {
                    $disable = [];
                    $blnSkip = false;

                    foreach ($dependencies as $conditionFieldName => $condition)
                    {
                        if(is_string($conditionFieldName))
                        {
                            $dependentFields[] = $conditionFieldName;

                            // If the condition field has already been removed, the current field to be checked must not be output.
                            if(!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$conditionFieldName]))
                            {
                                $disable[] = false;
                                $blnSkip   = true;
                            }
                        }

                        // Check condition
                        if(!$blnSkip)
                        {
                            if(is_callable($condition))
                            {
                                $disable[] = $condition($conditionFieldName, $objModel, $dependentFields);
                            }
                            else
                            {
                                $disable[] = $objModel->{$conditionFieldName} == $condition;
                            }
                        }
                    }

                    if(in_array(false, $disable))
                    {
                        unset($GLOBALS['TL_DCA'][$dc->table]['fields'][$fieldName]);
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

    private function getValidTables(): array
    {
        return array_merge(
            $GLOBALS['BE_FIELD_DEPENDENCY_TABLES'] ?? [],
            Controller::getContainer()->getParameter('contao_be_field_dependency.tables') ?? []
        );
    }

    private function simulateDataContainer($table): \stdClass
    {
        $dc = new \stdClass();
        $dc->table = $table;
        $dc->id = Input::get('id') ?? Input::post('id');

        return $dc;
    }
}
