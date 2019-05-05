<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/9/19
 * Time: 7:20 AM
 */

namespace Eddmash\PermissionBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class EddmashPermissionExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $definition = $container->getDefinition('eddmash.permission.permission');
        $definition->setArgument(0, $config['user_entity']);
        $definition->setArgument(1, $config['fetch_admin_callback']);
        $definition = $container->getDefinition('Eddmash\PermissionBundle\Command\AuthCommand');
        $definition->setArgument(2, $config['user_entity']);
        $definition->setArgument(3, $config['fetch_admin_callback']);
        $definition = $container->getDefinition('Eddmash\PermissionBundle\EventListener\DynamicRelationSubscriber');
        $definition->setArgument(0, $config['user_entity']);
        $definition = $container->getDefinition('Eddmash\PermissionBundle\Security\PermissionVoter');
        $definition->setArgument(1, $config['user_entity']);
    }
}