<?php

declare(strict_types=1);

namespace Oveleon\ContaoBeFieldDependency\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Oveleon\ContaoBeFieldDependency\ContaoBeFieldDependency;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoBeFieldDependency::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['be-field-dependency']),
        ];
    }
}
