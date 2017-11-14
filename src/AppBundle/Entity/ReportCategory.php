<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * ReportCategory
 *
 * @ORM\Table(name="report_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportCategoryRepository")
 */
class ReportCategory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

/********************/
/*** Some Basics ****/
/********************/

    /**
     * @Exclude
     * @var \DateTime
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @Exclude
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_time", type="datetime")
     */
    private $lastUpdateTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", options={"default": true})
     */
    private $active;

/***** END Basics ****/

/******************** */
/**** Relations ***** */
/******************** */

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Report", mappedBy="category")
     * */
    private $reports;

/************************ */
/**** Relations END ***** */
/************************ */



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setActive(1);
        $this->setCreationTime(new \DateTime());
        $this->setLastUpdateTime(new \DateTime());
        $this->reports = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getTitle();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ReportCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return ReportCategory
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Set lastUpdateTime
     *
     * @param \DateTime $lastUpdateTime
     *
     * @return ReportCategory
     */
    public function setLastUpdateTime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;

        return $this;
    }

    /**
     * Get lastUpdateTime
     *
     * @return \DateTime
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }

    /**
     * Set active
     *
     * @param string $active
     *
     * @return ReportCategory
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add Report
     *
     * @param \AppBundle\Entity\Report $report
     *
     * @return ReportCategory
     */
    public function addReport(\AppBundle\Entity\Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove Report
     *
     * @param \AppBundle\Entity\Report $report
     */
    public function removeReport(\AppBundle\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get Reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReports()
    {
        return $this->reports;
    }
}
