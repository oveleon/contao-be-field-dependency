<?php

declare(strict_types=1);

/**
 * This file is part of Contao EstateManager.
 *
 * @link      https://www.contao-estatemanager.com/
 * @source    https://github.com/contao-estatemanager/core
 * @copyright Copyright (c) 2019  Oveleon GbR (https://www.oveleon.de)
 * @license   https://www.contao-estatemanager.com/lizenzbedingungen.html
 */

namespace Oveleon\ContaoBeFieldDependency\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoBeFieldDependencyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');

        if(empty($config['tables']))
        {
            $config['tables'] = [
                'tl_article',
                'tl_content',
                'tl_files',
                'tl_form_field',
                'tl_form',
                'tl_image_size_item',
                'tl_image_size',
                'tl_layout',
                'tl_member_group',
                'tl_member',
                'tl_module',
                'tl_opt_in',
                'tl_page',
                'tl_style',
                'tl_style_sheet',
                'tl_theme',
                'tl_user_group',
                'tl_user'
            ];
        }

        $container->setParameter('contao_be_field_dependency.tables', $config['tables']);
        $container->setParameter('contao_be_field_dependency.autoSubmit', $config['autoSubmit']);
    }
}
