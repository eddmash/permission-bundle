<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/9/19
 * Time: 7:33 AM
 */

namespace Eddmash\PermissionBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('eddmash_permission');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('eddmash_permission');
        }

        $rootNode
                ->children()
                    ->scalarNode('user_entity')->isRequired()->end()
                    ->scalarNode('fetch_admin_callback')->defaultValue("fetchAdmin")->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}