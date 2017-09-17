<?php

namespace BackendBundle\Entity;

/**
 * TblTiposComp
 */
class TblTiposComp
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tipoComp;

    /**
     * @var \BackendBundle\Entity\TblComprobantes
     */
    private $codComp;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipoComp
     *
     * @param string $tipoComp
     *
     * @return TblTiposComp
     */
    public function setTipoComp($tipoComp)
    {
        $this->tipoComp = $tipoComp;

        return $this;
    }

    /**
     * Get tipoComp
     *
     * @return string
     */
    public function getTipoComp()
    {
        return $this->tipoComp;
    }

    /**
     * Set codComp
     *
     * @param \BackendBundle\Entity\TblComprobantes $codComp
     *
     * @return TblTiposComp
     */
    public function setCodComp(\BackendBundle\Entity\TblComprobantes $codComp = null)
    {
        $this->codComp = $codComp;

        return $this;
    }

    /**
     * Get codComp
     *
     * @return \BackendBundle\Entity\TblComprobantes
     */
    public function getCodComp()
    {
        return $this->codComp;
    }
}

