<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 3/9/19
 * Time: 11:13 PM
 */

namespace Eddmash\PermissionBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Eddmash\PermissionBundle\Entity\AuthRole;

class DynamicRelationSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    private $userEntity;

    public function __construct(string $userEntity)
    {
        $this->userEntity = $userEntity;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() != AuthRole::class) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy();

        if (!$this->userEntity) {
            return;
        }

        $metadata->mapManyToMany(array(
            'targetEntity' => $this->userEntity,
            'fieldName' => 'users',
            'cascade' => array('persist'),

//            'joinTable' => array(
//                'name' => strtolower($namingStrategy->classToTableName($metadata->getName())) . '_users',
//                'joinColumns' => array(
//                    array(
//                        'name' => $namingStrategy->joinKeyColumnName($metadata->getName()),
//                        'referencedColumnName' => $namingStrategy->referenceColumnName(),
//                        'onDelete' => 'CASCADE',
//                        'onUpdate' => 'CASCADE',
//                    ),
//                ),
//                'inverseJoinColumns' => array(
//                    array(
//                        'name' => 'document_id',
//                        'referencedColumnName' => $namingStrategy->referenceColumnName(),
//                        'onDelete' => 'CASCADE',
//                        'onUpdate' => 'CASCADE',
//                    ),
//                )
//            )
        ));
    }
}
