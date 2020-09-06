<?php

namespace Eddmash\PermissionBundle\Entity;

use Eddmash\PermissionBundle\Entity\Annotations\AccessRights;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @AccessRights(label="Grant access rights", tag="rights")
 * @ORM\Entity(repositoryClass="Eddmash\PermissionBundle\Repository\AuthPermissionRepository")
 */
class AuthPermission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Eddmash\PermissionBundle\Entity\AuthRole", mappedBy="permissions",
     *     cascade={"persist"})
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $label;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|AuthRole[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(AuthRole $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addPermission($this);
        }

        return $this;
    }

    public function removeRole(AuthRole $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $role->removePermission($this);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
