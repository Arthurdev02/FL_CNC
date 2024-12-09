<?php

namespace App\Model;

class Equipment
{
    private $id;
    private $label;


    /** Get the value of id*/
    public function getId()
    {
        return $this->id;
    }

    /** Set the value of id @return  self*/
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /** Get the value of label*/
    public function getLabel()
    {
        return $this->label;
    }

    /** Set the value of label @return  self*/
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
