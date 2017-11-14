<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * User
 *
 * @ORM\Table(name="report_grades")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportGradeRepository")
 */
class ReportGrade
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
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="reportGrades")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=false)
     * */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reportGrades")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     * */
    private $owner;

    /**
     * @var integer
     * @ORM\Column(name="grade", type="integer")
     */
    private $grade;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_update_time", type="datetime")
     */
    private $lastUpdateTime;


    private static $serializationContextArray = [
        'Default',
        'owner'  => ['basic_info'],
        'report' => ['basic_info']
    ];

    public function __construct()
    {
        $this->setCreationTime(new \DateTime());
        $this->setLastUpdateTime(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $grade
     * @return ReportGrade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $owner
     * @return ReportGrade
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $report
     * @return ReportGrade
     */
    public function setReport($report)
    {
        $this->report = $report;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param \DateTime $creationTime
     * @return ReportGrade
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @param \DateTime $lastUpdateTime
     * @return ReportGrade
     */
    public function setLastUpdateTime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }

    /**
     * @return array
     */
    public static function getSerializationContextArray()
    {
        return self::$serializationContextArray;
    }
}
