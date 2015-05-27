<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    protected $id;
 
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string $name
     */
    protected $name;
 
    /**
     * Геттер для id.
     *
     * @return integer The id.
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * Геттер для имени пользователя.
     *
     * @return string The name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Сеттер для имени пользователя.
     *
     * @param string $value The name.
     */
    public function setName($value)
    {
        $this->name = $value;
    }
    
}