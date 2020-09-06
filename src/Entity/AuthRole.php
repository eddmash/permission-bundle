<?php

namespace Eddmash\PermissionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Eddmash\PermissionBundle\Entity\Annotations\AccessRights;

use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @AccessRights(label="Assign Group", tag="groups")
 * @ORM\Entity(repositoryClass="Eddmash\PermissionBundle\Repository\AuthRoleRepository")
 */
class AuthRole
{
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_FINANCE_OFFICER = "ROLE_FINANCE_OFFICER";
    const ROLE_SERVICE_DESK = "ROLE_SERVICE_DESK";
    const ROLE_ROOT = "ROLE_ROOT";
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @var ArrayCollection
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="Eddmash\PermissionBundle\Entity\AuthPermission", inversedBy="roles",cascade={"persist"})
     */
    private $permissions;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Collection|AuthPermission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(AuthPermission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->addRole($this);
        }

        return $this;
    }

    public function removePermission(AuthPermission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
        }

        return $this;
    }


    public function __toString()
    {
        return $this->getName();
    }

    public function getSimpleName(): string
    {
        return str_replace("ROLE_", "Group ", $this->getName());
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }


    public function addUser($user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
//            $user->addRole($this);
        }

        return $this;
    }

    public function removeUser($user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }


}
